<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    fields: Array,
    prices: Array,
    bookings: Array
});

const activeTab = ref('lapangan');

// Field Form
const fieldForm = useForm({
    id: null,
    name: '',
    type: '',
    price_per_hour: '',
    is_available: true
});

const editField = (field) => {
    fieldForm.id = field.id;
    fieldForm.name = field.name;
    fieldForm.type = field.type;
    fieldForm.price_per_hour = field.price_per_hour;
    fieldForm.is_available = field.is_available;
};

const saveField = () => {
    if (fieldForm.id) {
        fieldForm.put(`/dashboard/fields/${fieldForm.id}`, {
            onSuccess: () => fieldForm.reset()
        });
    } else {
        fieldForm.post('/dashboard/fields', {
            onSuccess: () => fieldForm.reset()
        });
    }
};

const deleteField = (id) => {
    if (confirm('Yakin ingin menghapus lapangan ini?')) {
        router.delete(`/dashboard/fields/${id}`);
    }
};

// Price Form
const priceForm = useForm({
    id: null,
    time_period: '',
    start_time: '',
    end_time: '',
    price_per_hour: '',
    description: '',
    is_active: true
});

const editPrice = (price) => {
    priceForm.id = price.id;
    priceForm.time_period = price.time_period;
    priceForm.start_time = price.start_time;
    priceForm.end_time = price.end_time;
    priceForm.price_per_hour = price.price_per_hour;
    priceForm.description = price.description;
    priceForm.is_active = !!price.is_active;
};

const savePrice = () => {
    if (priceForm.id) {
        priceForm.put(`/dashboard/prices/${priceForm.id}`, {
            onSuccess: () => priceForm.reset()
        });
    } else {
        priceForm.post('/dashboard/prices', {
            onSuccess: () => priceForm.reset()
        });
    }
};

const deletePrice = (id) => {
    if (confirm('Yakin ingin menghapus harga ini?')) {
        router.delete(`/dashboard/prices/${id}`);
    }
};

</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-100">
                Admin Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <!-- Top summary cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="p-6 bg-gradient-to-r from-slate-800 to-slate-700 rounded-xl shadow">
                        <div class="text-sm text-gray-400">Total Lapangan</div>
                        <div class="text-2xl font-bold text-white">{{ fields.length }}</div>
                    </div>
                    <div class="p-6 bg-gradient-to-r from-slate-800 to-slate-700 rounded-xl shadow">
                        <div class="text-sm text-gray-400">Periode Harga</div>
                        <div class="text-2xl font-bold text-white">{{ prices.length }}</div>
                    </div>
                    <div class="p-6 bg-gradient-to-r from-slate-800 to-slate-700 rounded-xl shadow">
                        <div class="text-sm text-gray-400">Total Reservasi</div>
                        <div class="text-2xl font-bold text-white">{{ bookings.length }}</div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex space-x-4 mb-6 border-b border-gray-700">
                    <button @click="activeTab = 'lapangan'" :class="{'border-sky-500 text-sky-400': activeTab === 'lapangan', 'border-transparent text-gray-400': activeTab !== 'lapangan'}" class="px-4 py-2 border-b-2 font-medium hover:text-sky-400 transition">Manajemen Lapangan</button>
                    <button @click="activeTab = 'harga'" :class="{'border-sky-500 text-sky-400': activeTab === 'harga', 'border-transparent text-gray-400': activeTab !== 'harga'}" class="px-4 py-2 border-b-2 font-medium hover:text-sky-400 transition">Manajemen Harga & Jadwal</button>
                    <button @click="activeTab = 'reservasi'" :class="{'border-sky-500 text-sky-400': activeTab === 'reservasi', 'border-transparent text-gray-400': activeTab !== 'reservasi'}" class="px-4 py-2 border-b-2 font-medium hover:text-sky-400 transition">Data Reservasi</button>
                </div>

                <!-- Tab Lapangan -->
                <div v-if="activeTab === 'lapangan'" class="bg-gray-800 text-gray-100 p-6 rounded-xl shadow-sm border border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Form Lapangan</h3>
                    <form @submit.prevent="saveField" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <input v-model="fieldForm.name" type="text" placeholder="Nama Lapangan" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2" required>
                        <input v-model="fieldForm.type" type="text" placeholder="Tipe Lapangan (Contoh: Sintetis)" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2" required>
                        <input v-model="fieldForm.price_per_hour" type="number" placeholder="Harga Dasar Per Jam" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2" required>
                        <label class="flex items-center space-x-2">
                            <input v-model="fieldForm.is_available" type="checkbox" class="rounded">
                            <span>Tersedia?</span>
                        </label>
                        <div class="md:col-span-2 flex justify-end gap-2">
                            <button type="button" @click="fieldForm.reset()" class="px-4 py-2 bg-gray-600 text-gray-200 hover:bg-gray-500 transition rounded">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-sky-500 text-white rounded hover:bg-sky-600">Simpan Lapangan</button>
                        </div>
                    </form>

                    <h3 class="text-lg font-bold mb-4">Daftar Lapangan</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-700 border-b border-gray-600 text-gray-200">
                                    <th class="p-3">Nama</th>
                                    <th class="p-3">Tipe</th>
                                    <th class="p-3">Harga Dasar</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="field in fields" :key="field.id" class="border-b border-gray-700 hover:bg-gray-700">
                                    <td class="p-3">{{ field.name }}</td>
                                    <td class="p-3">{{ field.type }}</td>
                                    <td class="p-3">Rp {{ Number(field.price_per_hour).toLocaleString() }}</td>
                                    <td class="p-3">{{ field.is_available ? 'Aktif' : 'Nonaktif' }}</td>
                                    <td class="p-3 flex gap-2">
                                        <button @click="editField(field)" class="text-sky-500">Edit</button>
                                        <button @click="deleteField(field.id)" class="text-red-500">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Harga & Jadwal -->
                <div v-if="activeTab === 'harga'" class="bg-gray-800 text-gray-100 p-6 rounded-xl shadow-sm border border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Form Harga/Sesi</h3>
                    <form @submit.prevent="savePrice" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <input v-model="priceForm.time_period" type="text" placeholder="Periode (Contoh: Pagi / Malam)" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2" required>
                        <input v-model="priceForm.price_per_hour" type="number" placeholder="Harga Per Jam" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2" required>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Waktu Mulai</label>
                            <input v-model="priceForm.start_time" type="time" step="1" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2 w-full" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Waktu Selesai</label>
                            <input v-model="priceForm.end_time" type="time" step="1" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2 w-full" required>
                        </div>
                        <input v-model="priceForm.description" type="text" placeholder="Deskripsi" class="border border-gray-600 bg-gray-700 text-gray-100 rounded p-2 md:col-span-2">
                        <label class="flex items-center space-x-2 md:col-span-2">
                            <input v-model="priceForm.is_active" type="checkbox" class="rounded">
                            <span>Aktif?</span>
                        </label>
                        <div class="md:col-span-2 flex justify-end gap-2">
                            <button type="button" @click="priceForm.reset()" class="px-4 py-2 bg-gray-600 text-gray-200 hover:bg-gray-500 transition rounded">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-sky-500 text-white rounded hover:bg-sky-600">Simpan Harga</button>
                        </div>
                    </form>

                    <h3 class="text-lg font-bold mb-4">Daftar Harga & Sesi Jadwal</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-700 border-b border-gray-600 text-gray-200">
                                    <th class="p-3">Periode</th>
                                    <th class="p-3">Waktu</th>
                                    <th class="p-3">Harga/Jam</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="price in prices" :key="price.id" class="border-b border-gray-700 hover:bg-gray-700">
                                    <td class="p-3">{{ price.time_period }}</td>
                                    <td class="p-3">{{ price.start_time }} - {{ price.end_time }}</td>
                                    <td class="p-3">Rp {{ Number(price.price_per_hour).toLocaleString() }}</td>
                                    <td class="p-3">{{ price.is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                    <td class="p-3 flex gap-2">
                                        <button @click="editPrice(price)" class="text-sky-500">Edit</button>
                                        <button @click="deletePrice(price.id)" class="text-red-500">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Reservasi -->
                <div v-if="activeTab === 'reservasi'" class="bg-gray-800 text-gray-100 p-6 rounded-xl shadow-sm border border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Daftar Semua Reservasi</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-700 border-b border-gray-600 text-gray-200">
                                    <th class="p-3">ID Booking</th>
                                    <th class="p-3">User</th>
                                    <th class="p-3">Lapangan</th>
                                    <th class="p-3">Waktu Main</th>
                                    <th class="p-3">Total Harga</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="booking in bookings" :key="booking.id" class="border-b border-gray-700 hover:bg-gray-700">
                                    <td class="p-3 font-mono text-sm">{{ booking.booking_code || booking.id }}</td>
                                    <td class="p-3">{{ booking.user?.name }}</td>
                                    <td class="p-3">{{ booking.field?.name }}</td>
                                    <td class="p-3">{{ booking.start_time }} - {{ booking.end_time }}</td>
                                    <td class="p-3">Rp {{ Number(booking.total_price).toLocaleString() }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 text-xs rounded-full" 
                                              :class="{'bg-yellow-100 text-yellow-700': booking.status === 'pending', 'bg-green-100 text-green-700': booking.status === 'confirmed', 'bg-red-100 text-red-700': booking.status === 'cancelled'}">
                                            {{ booking.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="bookings.length === 0">
                                    <td colspan="6" class="p-4 text-center text-gray-400">Belum ada reservasi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>