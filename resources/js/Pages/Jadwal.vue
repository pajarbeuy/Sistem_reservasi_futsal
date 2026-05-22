<script setup>
import { ref, onMounted } from 'vue';
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';

const fields = ref([]);
const schedules = ref([]);
const selectedField = ref(null);
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const loading = ref(false);
const error = ref('');

const loadFields = async () => {
    try {
        const response = await fetch('/api/fields');
        const data = await response.json();
        fields.value = data.data || data;
        if (fields.value.length > 0) {
            selectedField.value = fields.value[0].id;
            await loadSchedule();
        }
    } catch (err) {
        error.value = 'Gagal memuat daftar lapangan: ' + err.message;
        console.error('Error loading fields:', err);
    }
};

const loadSchedule = async () => {
    if (!selectedField.value || !selectedDate.value) return;
    
    loading.value = true;
    error.value = '';

    try {
        const response = await fetch(`/api/schedule/day-schedule?field_id=${selectedField.value}&date=${selectedDate.value}`);
        const data = await response.json();

        if (data.success) {
            schedules.value = data.schedule || [];
        } else {
            error.value = data.message || 'Gagal memuat jadwal';
        }
    } catch (err) {
        error.value = 'Gagal memuat jadwal: ' + err.message;
        console.error('Error loading schedule:', err);
    } finally {
        loading.value = false;
    }
};

const onChoose = () => {
    // Redirect to booking form
    window.location.href = '/booking-form';
};

const onDateChange = (newDate) => {
    selectedDate.value = newDate;
    loadSchedule();
};

const onFieldChange = (fieldId) => {
    selectedField.value = fieldId;
    loadSchedule();
};

const getMinDate = () => {
    return new Date().toISOString().split('T')[0];
};

onMounted(() => {
    loadFields();
});
</script>

<template>
    <FutsalLayout>
        <div>
            <div class="max-w-6xl mx-auto px-4">
                <div class="py-10">
                    <h1 class="text-4xl md:text-5xl font-bold">Jadwal Reservasi</h1>
                    <p class="text-slate-300 mt-3">Pilih lapangan dan tanggal untuk melihat slot waktu yang tersedia.</p>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded text-red-200">
                    {{ error }}
                </div>

                <!-- Field Selection -->
                <div class="mb-8 p-6 bg-white/10 rounded-lg backdrop-blur">
                    <h2 class="text-xl font-semibold text-white mb-4">Pilih Lapangan</h2>
                    <div v-if="fields.length === 0" class="text-slate-300">Memuat lapangan...</div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button
                            v-for="field in fields"
                            :key="field.id"
                            @click="onFieldChange(field.id)"
                            :class="['p-4 rounded-lg transition', 
                                selectedField === field.id 
                                    ? 'bg-orange-600 text-white' 
                                    : 'bg-white/10 text-white hover:bg-white/20']"
                        >
                            <h3 class="font-semibold text-lg">{{ field.name }}</h3>
                            <p class="text-sm opacity-80">{{ field.type }}</p>
                        </button>
                    </div>
                </div>

                <!-- Date Selection -->
                <div class="mb-8 p-6 bg-white/10 rounded-lg backdrop-blur">
                    <h2 class="text-xl font-semibold text-white mb-4">Pilih Tanggal</h2>
                    <input
                        :value="selectedDate"
                        @change="onDateChange($event.target.value)"
                        type="date"
                        :min="getMinDate()"
                        class="w-full md:w-64 px-4 py-2 rounded-lg bg-white text-gray-900"
                    />
                </div>

                <!-- Schedule Display -->
                <div v-if="loading" class="text-center py-8">
                    <p class="text-white text-lg">Memuat jadwal...</p>
                </div>

                <div v-else-if="schedules.length > 0" class="space-y-4 pb-10">
                    <div v-for="schedule in schedules" :key="schedule.time_period"
                        class="p-6 bg-white/10 rounded-lg backdrop-blur border border-white/20">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ schedule.time_period }}</h3>
                                <p class="text-slate-300 mt-2">
                                    {{ schedule.start_time }} - {{ schedule.end_time }}
                                </p>
                                <p class="text-orange-400 font-semibold mt-2">
                                    {{ schedule.price_per_hour.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }) }}/jam
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0 text-right">
                                <p class="text-slate-300 text-sm">Status: 
                                    <span :class="[
                                        'font-semibold',
                                        schedule.status === 'available' ? 'text-green-400' : 'text-yellow-400'
                                    ]">
                                        {{ schedule.status === 'available' ? 'Tersedia' : 'Sebagian Dipesan' }}
                                    </span>
                                </p>
                                <p class="text-white mt-2">
                                    <span class="font-bold text-green-400">{{ schedule.available_count }}</span> slot tersedia
                                </p>
                                <button
                                    @click="onChoose"
                                    class="mt-4 px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition"
                                >
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8">
                    <p class="text-white text-lg">Tidak ada jadwal tersedia untuk tanggal ini.</p>
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>

