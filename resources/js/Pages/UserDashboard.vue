<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    bookings: Array,
    payments: Array
});

const activeTab = ref('booking');

const timeZone = 'Asia/Jakarta';

const parseDate = (value) => {
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? null : date;
};

const formatDateWib = (value) => {
    const date = parseDate(value);

    if (!date) return '-';

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        timeZone,
    }).format(date);
};

const formatTimeWib = (value) => {
    const date = parseDate(value);

    if (!date) return '-';

    return new Intl.DateTimeFormat('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone,
    }).format(date);
};

const formatBookingTime = (booking) => {
    const start = formatTimeWib(booking.start_time);
    const end = formatTimeWib(booking.end_time);

    return start === '-' || end === '-' ? '-' : `${start} - ${end} WIB`;
};

const formatDuration = (booking) => {
    const start = parseDate(booking.start_time);
    const end = parseDate(booking.end_time);

    if (!start || !end) return '-';

    const minutes = Math.max(0, Math.round((end - start) / 60000));
    const hours = Math.floor(minutes / 60);
    const restMinutes = minutes % 60;

    if (hours && restMinutes) return `${hours} jam ${restMinutes} menit`;
    if (hours) return `${hours} jam`;

    return `${restMinutes} menit`;
};

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-100">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-100">Dashboard</h3>
                    <Link href="/" class="px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700 transition">Buat Reservasi</Link>
                </div>

                <!-- Tabs -->
                <div class="flex space-x-4 mb-6 border-b border-gray-700">
                    <button 
                        @click="activeTab = 'booking'" 
                        :class="{'border-sky-500 text-sky-400': activeTab === 'booking', 'border-transparent text-gray-400': activeTab !== 'booking'}" 
                        class="px-4 py-2 border-b-2 font-medium hover:text-sky-400 transition">
                        Riwayat Booking
                    </button>
                    <button 
                        @click="activeTab = 'transaksi'" 
                        :class="{'border-sky-500 text-sky-400': activeTab === 'transaksi', 'border-transparent text-gray-400': activeTab !== 'transaksi'}" 
                        class="px-4 py-2 border-b-2 font-medium hover:text-sky-400 transition">
                        Riwayat Transaksi
                    </button>
                </div>

                <!-- Tab Riwayat Booking -->
                <div v-if="activeTab === 'booking'" class="bg-gray-800 text-gray-100 p-6 rounded-xl shadow-sm border border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Riwayat Booking</h3>
                    
                    <div v-if="bookings && bookings.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-700 border-b border-gray-600 text-gray-200">
                                    <th class="p-3">Lapangan</th>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Jam</th>
                                    <th class="p-3">Durasi</th>
                                    <th class="p-3">Harga</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="booking in bookings" :key="booking.id" class="border-b border-gray-700 hover:bg-gray-700">
                                    <td class="p-3">{{ booking.field?.name || '-' }}</td>
                                    <td class="p-3">{{ formatDateWib(booking.start_time) }}</td>
                                    <td class="p-3">{{ formatBookingTime(booking) }}</td>
                                    <td class="p-3">{{ formatDuration(booking) }}</td>
                                    <td class="p-3">Rp {{ Number(booking.total_price).toLocaleString('id-ID') }}</td>
                                    <td class="p-3">
                                        <span 
                                            :class="{
                                                'text-green-400': booking.status === 'confirmed',
                                                'text-yellow-400': booking.status === 'pending',
                                                'text-red-400': booking.status === 'cancelled'
                                            }"
                                            class="font-semibold">
                                            {{ booking.status === 'confirmed' ? 'Terkonfirmasi' : booking.status === 'pending' ? 'Menunggu' : 'Dibatalkan' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center text-gray-400 py-8">
                        <p>Belum ada riwayat booking</p>
                    </div>
                </div>

                <!-- Tab Riwayat Transaksi -->
                <div v-if="activeTab === 'transaksi'" class="bg-gray-800 text-gray-100 p-6 rounded-xl shadow-sm border border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Riwayat Transaksi</h3>
                    
                    <div v-if="payments && payments.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-700 border-b border-gray-600 text-gray-200">
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Nominal</th>
                                    <th class="p-3">Metode</th>
                                    <th class="p-3">Referensi</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in payments" :key="payment.id" class="border-b border-gray-700 hover:bg-gray-700">
                                    <td class="p-3">{{ formatDateWib(payment.created_at) }}</td>
                                    <td class="p-3">Rp {{ Number(payment.amount).toLocaleString('id-ID') }}</td>
                                    <td class="p-3">{{ payment.payment_method || '-' }}</td>
                                    <td class="p-3">{{ payment.reference_id || '-' }}</td>
                                    <td class="p-3">
                                        <span 
                                            :class="{
                                                'text-green-400': payment.payment_status === 'success',
                                                'text-yellow-400': payment.payment_status === 'pending',
                                                'text-red-400': payment.payment_status === 'failed'
                                            }"
                                            class="font-semibold">
                                            {{ payment.payment_status === 'success' ? 'Berhasil' : payment.payment_status === 'pending' ? 'Menunggu' : 'Gagal' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center text-gray-400 py-8">
                        <p>Belum ada riwayat transaksi</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
