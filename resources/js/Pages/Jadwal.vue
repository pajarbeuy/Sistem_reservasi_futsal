<script setup>
import { ref, onMounted, computed } from 'vue';
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';
import JadwalGrid from '@/Components/JadwalGrid.vue';

const fields = ref([]);
const schedules = ref([]);
const selectedField = ref(null);
const getLocalDateString = () => {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const selectedDate = ref(getLocalDateString());
const loading = ref(false);
const error = ref('');

const loadFields = async () => {
    try {
        const response = await fetch('/api/fields');
        if (!response.ok) {
            throw new Error(`API error: ${response.status} ${response.statusText}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response type: expected JSON');
        }
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
        // Get hourly slots for the selected field and date
        const url = `/api/schedule/available-slots?field_id=${selectedField.value}&date=${selectedDate.value}`;
        console.log('Loading schedule from:', url);
        
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`API error: ${response.status} ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response type: expected JSON');
        }
        
        const data = await response.json();
        console.log('Schedule response:', data);

        if (data.success && data.slots) {
            schedules.value = data.slots.map(item => ({
                start_time: item.start_time,
                end_time: item.end_time,
                price_per_hour: item.price_per_hour,
                time_period: item.time_period,
                status: item.available ? 'Tersedia' : 'Terbooking'
            }));
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

const scheduleItems = computed(() => {
    return schedules.value.map(schedule => ({
        start_time: schedule.start_time,
        end_time: schedule.end_time,
        price_per_hour: schedule.price_per_hour,
        time_period: schedule.time_period,
        status: schedule.status
    }));
});

const onDateChange = (newDate) => {
    selectedDate.value = newDate;
    loadSchedule();
};

const onFieldChange = (fieldId) => {
    selectedField.value = fieldId;
    loadSchedule();
};

const getMinDate = () => {
    return getLocalDateString();
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
                <div class="mb-10 p-6 bg-white/10 rounded-lg backdrop-blur border border-white/20">
                    <h2 class="text-xl font-semibold text-white mb-6">Pilih Slot Waktu</h2>
                    
                    <div v-if="loading" class="text-center py-8">
                        <p class="text-white text-lg">Memuat jadwal...</p>
                    </div>

                    <div v-else-if="schedules.length > 0">
                        <JadwalGrid 
                            :items="scheduleItems"
                            :readonly="true"
                            :start="schedules[0]?.start_time || '08:00'"
                            :end="schedules[schedules.length - 1]?.end_time || '19:30'"
                            :slotDuration="90"
                        />
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-white text-lg">Tidak ada jadwal tersedia untuk tanggal ini.</p>
                    </div>
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>
