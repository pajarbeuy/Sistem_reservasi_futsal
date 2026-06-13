<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const step = ref(1); // 1: Select Field & Date, 2: Select Time, 3: Confirm
const fields = ref([]);
const prices = ref([]);
const availableSlots = ref([]);
const loading = ref(false);
const error = ref('');
const success = ref('');

const form = useForm({
    field_id: '',
    date: '',
    start_time: '',
    end_time: '',
    duration_minutes: 60,
    phone_number: '',
    customer_name: '',
    customer_email: '',
});

const selectedField = computed(() => {
    return fields.value.find(f => f.id === parseInt(form.field_id));
});

const bookingTotal = computed(() => {
    if (!form.start_time || !form.end_time || !selectedField.value) return 0;
    
    const startHour = parseInt(form.start_time.split(':')[0]);
    const endHour = parseInt(form.end_time.split(':')[0]);
    const durationHours = endHour - startHour;
    
    return durationHours * selectedField.value.price_per_hour;
});

const getPriceLabel = (time) => {
    const hour = parseInt(time.split(':')[0]);
    const matchingPrice = prices.value.find(p => {
        const startHour = parseInt(p.start_time.split(':')[0]);
        const endHour = parseInt(p.end_time.split(':')[0]);
        return hour >= startHour && hour < endHour;
    });
    return matchingPrice ? matchingPrice.time_period : '-';
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const loadFields = async () => {
    try {
        const response = await fetch('/api/fields', {
            headers: {
                'Accept': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error(`API error: ${response.status} ${response.statusText}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response type: expected JSON');
        }
        const data = await response.json();
        fields.value = data.data || data;
    } catch (err) {
        error.value = 'Gagal memuat daftar lapangan: ' + err.message;
        console.error('Error loading fields:', err);
    }
};

const loadPrices = async () => {
    try {
        const response = await fetch('/api/prices', {
            headers: {
                'Accept': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error(`API error: ${response.status} ${response.statusText}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response type: expected JSON');
        }
        const data = await response.json();
        prices.value = data.data || data;
    } catch (err) {
        console.log('Gagal memuat harga:', err.message);
    }
};

const loadAvailableSlots = async () => {
    if (!form.field_id || !form.date) return;
    
    loading.value = true;
    error.value = '';
    
    try {
        const params = new URLSearchParams({
            field_id: form.field_id,
            date: form.date,
            duration_minutes: form.duration_minutes,
        });
        
        const url = `/api/schedule/available-slots?${params}`;
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error(`API error: ${response.status} ${response.statusText}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response type: expected JSON');
        }
        const data = await response.json();
        
        if (data.success) {
            availableSlots.value = data.slots;
            step.value = 2;
        }
    } catch (err) {
        error.value = 'Gagal memuat slot waktu yang tersedia: ' + err.message;
        console.error('Error loading available slots:', err);
    } finally {
        loading.value = false;
    }
};

const selectTimeSlot = (slot) => {
    form.start_time = slot.start_time;
    form.end_time = slot.end_time;
    step.value = 3;
};

const submitBooking = async () => {
    loading.value = true;
    error.value = '';
    success.value = '';

    // Validate phone number is provided
    if (!form.phone_number.trim()) {
        error.value = 'Nomor telepon harus diisi untuk pembayaran';
        loading.value = false;
        return;
    }

    try {
        // Step 1: Create booking
        const bookingResponse = await fetch('/api/bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                field_id: form.field_id,
                start_time: `${form.date} ${form.start_time}`,
                end_time: `${form.date} ${form.end_time}`,
                phone_number: form.phone_number,
            })
        });

        const bookingData = await bookingResponse.json();

        if (!bookingResponse.ok) {
            throw new Error(bookingData.message || bookingData.error || `API error: ${bookingResponse.status}`);
        }

        const bookingId = bookingData.data?.id || bookingData.id;
        success.value = 'Booking berhasil dibuat! Memproses pembayaran...';

        // Step 2: Create Midtrans payment token
        const paymentResponse = await fetch('/api/payments/create-midtrans-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                booking_id: bookingId,
                amount: bookingTotal.value,
                customer_phone: form.phone_number,
                customer_name: form.customer_name || selectedField.value?.name || 'Customer',
                customer_email: form.customer_email || 'customer@futsal35.com',
            })
        });

        const paymentData = await paymentResponse.json();

        if (!paymentResponse.ok) {
            throw new Error(paymentData.message || 'Gagal membuat token pembayaran');
        }

        // Step 3: Load and display Midtrans Snap
        const clientKey = import.meta.env.VITE_MIDTRANS_CLIENT_KEY;
        if (!clientKey) {
            throw new Error('Midtrans client key tidak dikonfigurasi');
        }

        // Load Midtrans Snap script if not already loaded
        if (!window.snap) {
            const script = document.createElement('script');
            script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
            script.setAttribute('data-client-key', clientKey);
            document.head.appendChild(script);

            // Wait for script to load
            await new Promise((resolve) => {
                script.onload = resolve;
                script.onerror = () => {
                    throw new Error('Gagal memuat Midtrans Snap');
                };
            });
        }

        // Open Midtrans payment page
        if (window.snap && paymentData.token) {
            window.snap.pay(paymentData.token, {
                onSuccess: function () {
                    success.value = 'Pembayaran berhasil! Mengarahkan ke dashboard...';
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 2000);
                },
                onPending: function () {
                    success.value = 'Pembayaran sedang diproses...';
                },
                onError: function () {
                    error.value = 'Pembayaran gagal. Silakan coba lagi.';
                    loading.value = false;
                },
            });
        } else {
            throw new Error('Snap token tidak tersedia');
        }
    } catch (err) {
        error.value = 'Error: ' + err.message;
        console.error('Error submitting booking:', err);
        loading.value = false;
    }
};

const goBack = () => {
    if (step.value > 1) {
        step.value--;
    }
};

const resetForm = () => {
    form.reset();
    step.value = 1;
    availableSlots.value = [];
};

const getMinDate = () => {
    const today = new Date();
    return today.toISOString().split('T')[0];
};

onMounted(() => {
    loadFields();
    loadPrices();
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Buat Booking Baru" />

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <h1 class="text-3xl font-bold mb-2">Buat Booking Baru</h1>
                        <p class="text-gray-600 mb-6">Pilih lapangan, tanggal, dan waktu untuk reservasi</p>

                        <!-- Progress Indicator -->
                        <div class="mb-8 flex justify-between items-center">
                            <div class="flex-1 text-center">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 font-bold', step >= 1 ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600']">
                                    1
                                </div>
                                <p class="text-sm font-medium">Pilih Lapangan & Tanggal</p>
                            </div>
                            <div class="flex-1 h-1 mx-2" :class="step >= 2 ? 'bg-blue-500' : 'bg-gray-300'"></div>
                            <div class="flex-1 text-center">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 font-bold', step >= 2 ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600']">
                                    2
                                </div>
                                <p class="text-sm font-medium">Pilih Waktu</p>
                            </div>
                            <div class="flex-1 h-1 mx-2" :class="step >= 3 ? 'bg-blue-500' : 'bg-gray-300'"></div>
                            <div class="flex-1 text-center">
                                <div :class="['w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 font-bold', step >= 3 ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600']">
                                    3
                                </div>
                                <p class="text-sm font-medium">Konfirmasi</p>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded text-red-700">
                            {{ error }}
                        </div>

                        <!-- Success Message -->
                        <div v-if="success" class="mb-4 p-4 bg-green-50 border border-green-200 rounded text-green-700">
                            {{ success }}
                        </div>

                        <!-- Step 1: Select Field & Date -->
                        <div v-if="step === 1">
                            <div class="space-y-6">
                                <!-- Field Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Lapangan</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="field in fields" :key="field.id" @click="form.field_id = field.id"
                                            :class="['p-4 border-2 rounded-lg cursor-pointer transition', 
                                                form.field_id === field.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300']">
                                            <h3 class="font-semibold text-lg">{{ field.name }}</h3>
                                            <p class="text-gray-600 text-sm">{{ field.type }}</p>
                                            <p class="text-blue-600 font-semibold mt-2">
                                                {{ formatCurrency(field.price_per_hour) }}/jam
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Tanggal</label>
                                    <input
                                        v-model="form.date"
                                        type="date"
                                        :min="getMinDate()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                <!-- Duration Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Durasi Booking</label>
                                    <select
                                        v-model.number="form.duration_minutes"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="30">30 menit</option>
                                        <option value="60">1 jam</option>
                                        <option value="90">1.5 jam</option>
                                        <option value="120">2 jam</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-8 flex justify-end gap-4">
                                <button
                                    @click="resetForm"
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Reset
                                </button>
                                <button
                                    @click="loadAvailableSlots"
                                    :disabled="!form.field_id || !form.date || loading"
                                    :class="['px-6 py-2 rounded-lg text-white font-medium', 
                                        form.field_id && form.date ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed']"
                                >
                                    {{ loading ? 'Memuat...' : 'Lanjut' }}
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Select Time -->
                        <div v-if="step === 2">
                            <div class="mb-6">
                                <h2 class="text-lg font-semibold mb-4">Slot Waktu Tersedia</h2>
                                <div class="mb-4 p-4 bg-gray-50 rounded">
                                    <p><strong>Lapangan:</strong> {{ selectedField?.name }}</p>
                                    <p><strong>Tanggal:</strong> {{ new Date(form.date).toLocaleDateString('id-ID') }}</p>
                                </div>

                                <div v-if="availableSlots.length === 0" class="p-4 bg-yellow-50 border border-yellow-200 rounded text-yellow-700">
                                    Tidak ada slot waktu yang tersedia untuk tanggal ini.
                                </div>

                                <div v-else class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <button
                                        v-for="slot in availableSlots"
                                        :key="`${slot.start_time}-${slot.end_time}`"
                                        @click="selectTimeSlot(slot)"
                                        class="p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-center"
                                    >
                                        <p class="font-semibold">{{ slot.start_time }} - {{ slot.end_time }}</p>
                                        <p class="text-sm text-gray-600">{{ slot.time_period }}</p>
                                        <p class="text-sm text-blue-600 font-medium">{{ formatCurrency(slot.price_per_hour) }}/jam</p>
                                    </button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-8 flex justify-between gap-4">
                                <button
                                    @click="goBack"
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Kembali
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Confirm -->
                        <div v-if="step === 3">
                            <div class="space-y-6">
                                <h2 class="text-lg font-semibold">Konfirmasi Booking</h2>

                                <!-- Booking Details -->
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <p class="text-gray-600 text-sm">Lapangan</p>
                                            <p class="font-semibold text-lg">{{ selectedField?.name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-sm">Tanggal</p>
                                            <p class="font-semibold text-lg">{{ new Date(form.date).toLocaleDateString('id-ID') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-sm">Waktu</p>
                                            <p class="font-semibold text-lg">{{ form.start_time }} - {{ form.end_time }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-sm">Harga/Jam</p>
                                            <p class="font-semibold text-lg">{{ formatCurrency(selectedField?.price_per_hour) }}</p>
                                        </div>
                                    </div>

                                    <div class="border-t pt-4">
                                        <div class="flex justify-between items-center">
                                            <p class="text-lg font-semibold">Total Harga</p>
                                            <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(bookingTotal) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Contact Info -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                                        <input
                                            v-model="form.phone_number"
                                            type="tel"
                                            placeholder="08xx xxxx xxxx"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Digunakan untuk konfirmasi pembayaran</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                                        <input
                                            v-model="form.customer_name"
                                            type="text"
                                            placeholder="Nama Anda"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input
                                            v-model="form.customer_email"
                                            type="email"
                                            placeholder="email@example.com"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="p-4 bg-blue-50 rounded border border-blue-200">
                                    <p class="text-sm text-gray-700">
                                        <strong>Catatan:</strong> Setelah membuat booking, Anda akan diminta untuk melakukan pembayaran. 
                                        Booking akan dikonfirmasi setelah pembayaran diterima.
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-8 flex justify-between gap-4">
                                <button
                                    @click="goBack"
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Kembali
                                </button>
                                <button
                                    @click="submitBooking"
                                    :disabled="loading"
                                    :class="['px-6 py-2 rounded-lg text-white font-medium', 
                                        loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700']"
                                >
                                    {{ loading ? 'Memproses...' : 'Buat Booking' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
