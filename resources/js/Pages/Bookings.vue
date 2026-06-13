<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const bookings = ref([]);
const loading = ref(true);
const error = ref('');
const message = ref('');
const selectedMethods = ref({});
const paying = ref({});
const methods = ['Transfer Bank', 'E-Wallet', 'Cash'];

const formatCurrency = (value) => {
    if (value === null || value === undefined) {
        return '-';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const loadBookings = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await axios.get('/api/bookings');
        bookings.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memuat daftar booking.';
    } finally {
        loading.value = false;
    }
};

const payBooking = async (booking) => {
    const method = selectedMethods.value[booking.id] || methods[0];

    paying.value[booking.id] = true;
    message.value = '';
    error.value = '';

    try {
        const response = await axios.post('/api/payments', {
            booking_id: booking.id,
            payment_method: method,
        });

        const updatedBooking = response.data.booking;
        const index = bookings.value.findIndex((item) => item.id === booking.id);
        if (index !== -1) {
            bookings.value[index] = {
                ...bookings.value[index],
                ...updatedBooking,
                payment: response.data.payment,
            };
        }

        message.value = response.data.message || 'Pembayaran berhasil.';
    } catch (err) {
        error.value = err.response?.data?.error || err.response?.data || 'Pembayaran gagal. Coba lagi.';
    } finally {
        paying.value[booking.id] = false;
    }
};

onMounted(loadBookings);
</script>

<template>
    <Head title="Bookings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">Riwayat Booking & Transaksi</h2>
                    <p class="mt-1 text-sm text-gray-500">Lihat semua booking dan status pembayaran Anda.</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="space-y-4 mb-6">
                    <div v-if="message" class="rounded-lg border border-emerald-700 bg-emerald-900/20 px-4 py-3 text-sm text-emerald-200">{{ message }}</div>
                    <div v-if="error" class="rounded-lg border border-rose-700 bg-rose-900/20 px-4 py-3 text-sm text-rose-200">{{ error }}</div>
                </div>

                <div v-if="loading" class="rounded-2xl border border-gray-700 bg-gray-800 p-6 text-center text-gray-300 shadow-sm">
                    Memuat riwayat booking...
                </div>

                <div v-else-if="!bookings.length" class="rounded-2xl border border-gray-700 bg-gray-800 p-6 text-center text-gray-300 shadow-sm">
                    Belum ada booking. <a href="/jadwal" class="text-blue-400 hover:text-blue-300">Mulai booking sekarang</a>
                </div>

                <div v-else class="space-y-6">
                    <!-- Pending Payment Section -->
                    <div v-if="bookings.some(b => b.status === 'pending')" class="space-y-4">
                        <div class="border-l-4 border-yellow-500 bg-yellow-50 p-4 rounded">
                            <h3 class="text-lg font-semibold text-yellow-900">Menunggu Pembayaran</h3>
                            <p class="text-sm text-yellow-700 mt-1">Selesaikan pembayaran untuk mengkonfirmasi booking Anda</p>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div v-for="booking in bookings.filter(b => b.status === 'pending')" :key="booking.id" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">{{ booking.field.name }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ booking.field.type }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-yellow-700">
                                            {{ booking.status }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Tanggal</div>
                                        <div>{{ new Date(booking.start_time).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Waktu</div>
                                        <div>{{ new Date(booking.start_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} - {{ new Date(booking.end_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Total Harga</div>
                                        <div class="text-lg font-bold text-orange-600">{{ formatCurrency(booking.total_price) }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Catatan</div>
                                        <div class="text-xs">{{ booking.notes || '-' }}</div>
                                    </div>
                                </div>

                                <div class="mt-5 space-y-3">
                                    <label class="block text-sm font-medium text-slate-700">Pilih metode pembayaran</label>
                                    <select
                                        v-model="selectedMethods[booking.id]"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                    >
                                        <option v-for="method in methods" :key="method" :value="method">{{ method }}</option>
                                    </select>
                                    <button
                                        @click="payBooking(booking)"
                                        :disabled="paying[booking.id]"
                                        class="w-full inline-flex items-center justify-center rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:bg-slate-400"
                                    >
                                        <span v-if="paying[booking.id]">Memproses...</span>
                                        <span v-else>Bayar Sekarang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Bookings Section -->
                    <div v-if="bookings.some(b => b.status !== 'pending')" class="space-y-4">
                        <div class="border-l-4 border-emerald-500 bg-emerald-50 p-4 rounded">
                            <h3 class="text-lg font-semibold text-emerald-900">Riwayat Booking Terkonfirmasi</h3>
                            <p class="text-sm text-emerald-700 mt-1">Booking yang sudah dibayar dan dikonfirmasi</p>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div v-for="booking in bookings.filter(b => b.status !== 'pending')" :key="booking.id" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">{{ booking.field.name }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ booking.field.type }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                            {{ booking.status }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Tanggal</div>
                                        <div>{{ new Date(booking.start_time).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Waktu</div>
                                        <div>{{ new Date(booking.start_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} - {{ new Date(booking.end_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Total Harga</div>
                                        <div>{{ formatCurrency(booking.total_price) }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                                        <div class="font-medium text-slate-900">Metode Bayar</div>
                                        <div>{{ booking.payment?.payment_method || '-' }}</div>
                                    </div>
                                </div>

                                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-900">Status Pembayaran</span>
                                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold uppercase text-emerald-700">{{ booking.payment?.payment_status || 'completed' }}</span>
                                    </div>
                                    <div class="mt-3 space-y-1 text-xs text-slate-600">
                                        <div v-if="booking.payment?.transaction_id"><strong>ID Transaksi:</strong> {{ booking.payment.transaction_id }}</div>
                                        <div v-if="booking.phone_number"><strong>No. Telepon:</strong> {{ booking.phone_number }}</div>
                                        <div v-if="booking.notes"><strong>Catatan:</strong> {{ booking.notes }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
