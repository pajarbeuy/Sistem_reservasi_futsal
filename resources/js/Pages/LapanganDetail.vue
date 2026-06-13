<script setup>
import { ref, onMounted, computed } from 'vue';
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';
import JadwalGrid from '@/Components/JadwalGrid.vue';
import { apiGet, apiPost } from '@/Utils/api';

const props = defineProps({
    fieldId: {
        type: [String, Number],
        required: true,
    },
});

const field = ref(null);
const schedules = ref([]);
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const selectedSlot = ref(null);
const phoneNumber = ref('');
const notes = ref('');
const loading = ref(false);
const error = ref('');
const isSubmitting = ref(false);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const loadField = async () => {
    try {
        const data = await apiGet(`/api/fields/${props.fieldId}`);
        field.value = data.data || data;
    } catch (err) {
        error.value = 'Gagal memuat detail lapangan: ' + err.message;
        console.error('Field loading error:', err);
    }
};

const loadSchedule = async () => {
    if (!props.fieldId || !selectedDate.value) return;

    loading.value = true;
    try {
        // Use available-slots with duration 30 to get atomic half-hour slots for flexible duration building
        const url = `/api/schedule/available-slots?field_id=${props.fieldId}&date=${selectedDate.value}&duration_minutes=30`;
        console.log('Loading schedule from:', url);
        
        const data = await apiGet(url);
        console.log('Schedule API response:', data);
        
        if (data.success && data.slots) {
            // Map API response to schedule items — per-hour base slots
            schedules.value = data.slots.map(item => ({
                time_period: item.time_period,
                start_time: item.start_time.substring(0, 5),
                end_time: item.end_time.substring(0, 5),
                price_per_hour: item.price_per_hour,
                status: item.status,
            }));
            console.log('Mapped schedules:', schedules.value);
        } else {
            console.warn('No schedule data received');
            schedules.value = [];
        }
    } catch (err) {
        console.error('Error loading schedule:', err);
        error.value = 'Gagal memuat jadwal: ' + err.message;
        schedules.value = [];
    } finally {
        loading.value = false;
    }
};

const scheduleItems = computed(() => {
    return schedules.value.map(item => ({
        time_period: item.time_period,
        start_time: item.start_time,
        end_time: item.end_time,
        price_per_hour: item.price_per_hour,
        status: item.status === 'tersedia' ? 'Tersedia' : 'Terbooking'
    }));
});

// selectedSlot now contains the full slot object from JadwalGrid (includes duration_minutes, total_price, etc.)
const selectedScheduleData = computed(() => {
    return selectedSlot.value;
});

const totalPrice = computed(() => {
    if (!selectedScheduleData.value) return 0;
    return selectedScheduleData.value.total_price || selectedScheduleData.value.price_per_hour || 0;
});

const onDateChange = (newDate) => {
    selectedDate.value = newDate;
    selectedSlot.value = null;
    loadSchedule();
};

const onSlotChoose = (slotData) => {
    selectedSlot.value = slotData;
};

const handlePayment = async () => {
    if (isSubmitting.value) return;

    error.value = '';

    if (!phoneNumber.value) {
        error.value = 'Nomor telepon harus diisi';
        return;
    }

    if (!selectedSlot.value) {
        error.value = 'Pilih slot waktu terlebih dahulu';
        return;
    }

    if (!selectedScheduleData.value) {
        console.error('Selected slot:', selectedSlot.value);
        console.error('Available schedules:', schedules.value);
        error.value = 'Data jadwal tidak ditemukan. Pastikan jadwal sudah dimuat.';
        return;
    }

    isSubmitting.value = true;

    try {
        const slot = selectedScheduleData.value;
        const startTimeStr = slot.start_time;
        const endTimeStr = slot.end_time;

        console.log('Booking details:', {
            fieldId: props.fieldId,
            selectedDate: selectedDate.value,
            startTime: startTimeStr,
            endTime: endTimeStr,
            phoneNumber: phoneNumber.value,
            totalPrice: totalPrice.value,
            durationMinutes: slot.duration_minutes,
        });

        // 1. Create booking
        const bookingData = await apiPost('/api/bookings', {
            field_id: props.fieldId,
            start_time: new Date(`${selectedDate.value}T${startTimeStr}:00`).toISOString(),
            end_time: new Date(`${selectedDate.value}T${endTimeStr}:00`).toISOString(),
            phone_number: phoneNumber.value,
            notes: notes.value,
            total_price: totalPrice.value,
        });

        console.log('Booking response:', bookingData);

        if (!bookingData || bookingData.error) {
            error.value = bookingData?.message || bookingData?.error || 'Gagal membuat booking';
            return;
        }

        // 2. Create payment with Midtrans
        const paymentData = await apiPost('/api/payments/create-midtrans-token', {
            booking_id: bookingData.data.id,
            amount: totalPrice.value,
            customer_email: bookingData.data.user?.email || 'customer@example.com',
            customer_name: bookingData.data.user?.name || 'Customer',
            customer_phone: phoneNumber.value,
        });

        console.log('Payment response:', paymentData);

        if (!paymentData || paymentData.error) {
            error.value = paymentData?.message || paymentData?.error || 'Gagal membuat token pembayaran';
            return;
        }

        // 3. Show Midtrans payment modal
        if (window.snap) {
            window.snap.pay(paymentData.token, {
                onSuccess: function (result) {
                    alert('Pembayaran berhasil!');
                    window.location.href = '/dashboard';
                },
                onPending: function (result) {
                    alert('Pembayaran menunggu konfirmasi');
                },
                onError: function (result) {
                    error.value = 'Pembayaran gagal: ' + result.status_message;
                },
                onClose: function () {
                    console.log('Pembayaran ditutup');
                }
            });
        } else {
            error.value = 'Payment gateway tidak tersedia. Silakan refresh halaman.';
        }
    } catch (err) {
        error.value = 'Terjadi kesalahan: ' + err.message;
        console.error('Payment error:', err);
    } finally {
        isSubmitting.value = false;
    }
};

const getMinDate = () => {
    return new Date().toISOString().split('T')[0];
};

onMounted(() => {
    loadField();
    loadSchedule();

    // Load Midtrans Snap script with client key
    const script = document.createElement('script');
    script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
    script.setAttribute('data-client-key', import.meta.env.VITE_MIDTRANS_CLIENT_KEY || 'YOUR_CLIENT_KEY_HERE');
    document.body.appendChild(script);
});
</script>

<template>
    <FutsalLayout>
        <div class="min-h-screen">
            <div class="max-w-4xl mx-auto px-4 py-10">
                <!-- Back Button -->
                <a href="/lapangan" class="text-orange-400 hover:text-orange-300 mb-8 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Daftar Lapangan
                </a>

                <!-- Error Message -->
                <div v-if="error" class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-lg text-red-200">
                    {{ error }}
                </div>

                <div v-if="field" class="space-y-8">
                    <!-- Field Detail -->
                    <div class="bg-white/10 backdrop-blur rounded-lg p-8 border border-white/20">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h1 class="text-4xl font-bold text-white mb-2">{{ field.name }}</h1>
                                <p class="text-slate-300 text-lg">{{ field.type }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-bold text-orange-400">
                                    {{ formatCurrency(field.price_per_hour) }}
                                </p>
                                <p class="text-slate-300">/jam</p>
                            </div>
                        </div>
                        <p class="text-slate-300">
                            Lapangan futsal dengan standar internasional. Cocok untuk pertandingan, latihan, atau acara keluarga.
                        </p>
                    </div>

                    <!-- Booking Form -->
                    <div class="bg-white/10 backdrop-blur rounded-lg p-8 border border-white/20 space-y-6">
                        <h2 class="text-2xl font-bold text-white">Pesan Lapangan</h2>

                        <!-- Date Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-300">Tanggal</label>
                            <input
                                v-model="selectedDate"
                                @change="onDateChange(selectedDate)"
                                type="date"
                                :min="getMinDate()"
                                class="w-full px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white focus:border-orange-500 focus:outline-none"
                            />
                        </div>

                        <!-- Time Slot Selection -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-slate-300">Pilih Slot Waktu</label>
                            <div v-if="loading" class="text-center py-4 text-slate-300">
                                Memuat jadwal...
                            </div>
                            <div v-else-if="schedules.length > 0">
                                <JadwalGrid
                                    :items="scheduleItems"
                                    @choose="onSlotChoose"
                                />
                            </div>
                            <div v-else class="text-center py-4 text-slate-300">
                                Tidak ada jadwal tersedia untuk tanggal ini
                            </div>
                        </div>

                        <!-- Selected Slot Info -->
                        <div v-if="selectedSlot && selectedScheduleData" class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-slate-400">Waktu Booking</p>
                                    <p class="text-white font-semibold">{{ selectedScheduleData.start_time }} - {{ selectedScheduleData.end_time }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ selectedScheduleData.duration_minutes || 60 }} menit</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-400">Harga Total</p>
                                    <p class="text-2xl font-bold text-orange-400">{{ formatCurrency(totalPrice) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-300">Nomor Telepon *</label>
                            <input
                                v-model="phoneNumber"
                                type="tel"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:border-orange-500 focus:outline-none"
                            />
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-300">Catatan (Opsional)</label>
                            <textarea
                                v-model="notes"
                                placeholder="Tambahkan catatan khusus..."
                                rows="3"
                                class="w-full px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:border-orange-500 focus:outline-none"
                            />
                        </div>

                        <!-- Submit Button -->
                        <button
                            @click="handlePayment"
                            :disabled="isSubmitting || !selectedSlot"
                            class="w-full bg-orange-600 hover:bg-orange-700 disabled:bg-orange-600/50 text-white font-semibold py-3 rounded-lg transition"
                        >
                            <span v-if="isSubmitting">Memproses...</span>
                            <span v-else>Lanjut ke Pembayaran</span>
                        </button>
                    </div>
                </div>

                <div v-else class="text-center py-8">
                    <p class="text-white text-lg">Memuat detail lapangan...</p>
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>
