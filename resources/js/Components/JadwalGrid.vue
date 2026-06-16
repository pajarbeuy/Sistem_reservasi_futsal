<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
        // item shape: { start_time: '08:00', end_time: '09:00', status: 'tersedia'|'booked', price_per_hour: 100000, time_period: 'Pagi' }
    },
});

const emit = defineEmits(['choose']);

const selectedDuration = ref(60); // default 1 jam

const durationOptions = [
    { value: 30, label: '30 menit' },
    { value: 60, label: '1 jam' },
    { value: 90, label: '1.5 jam' },
    { value: 120, label: '2 jam' },
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Parse "HH:MM" to minutes since midnight
const timeToMinutes = (timeStr) => {
    const [h, m] = timeStr.split(':').map(Number);
    return h * 60 + m;
};

// Format minutes to "HH:MM"
const minutesToTime = (mins) => {
    const h = Math.floor(mins / 60).toString().padStart(2, '0');
    const m = (mins % 60).toString().padStart(2, '0');
    return `${h}:${m}`;
};

// Group slots by their source (start_time) and generate multi-hour slots
const groupedSlots = computed(() => {
    const slots = [];
    const availableItems = props.items.filter(i => i.status === 'tersedia' || i.status === 'Tersedia');

    // Sort by start_time
    const sorted = [...availableItems].sort((a, b) => timeToMinutes(a.start_time) - timeToMinutes(b.start_time));

    // Build a set of available start times (in minutes)
    const availableStartTimes = new Set(sorted.map(s => timeToMinutes(s.start_time)));
    // Also collect end times for each start time to know the range
    const slotMap = {};
    sorted.forEach(s => {
        const key = s.start_time;
        if (!slotMap[key]) slotMap[key] = s;
    });

    // For each available start time, if we can fit the selected duration, create a slot
    const duration = selectedDuration.value;

    sorted.forEach(item => {
        const startMins = timeToMinutes(item.start_time);
        const endMins = startMins + duration;
        const endTime = minutesToTime(endMins);

        // Check if all required 60-min slots within this range are available
        let allAvailable = true;
        for (let m = startMins; m < endMins; m += 60) {
            if (!availableStartTimes.has(m)) {
                allAvailable = false;
                break;
            }
        }

        if (allAvailable) {
            // Calculate total price
            const totalPrice = (duration / 60) * item.price_per_hour;

            slots.push({
                start_time: item.start_time,
                end_time: endTime,
                time_period: item.time_period,
                price_per_hour: item.price_per_hour,
                total_price: totalPrice,
                duration_minutes: duration,
                status: 'Tersedia',
            });
        }
    });

    return slots;
});

const onChoose = (item) => {
    emit('choose', item);
};

const onDurationChange = (duration) => {
    selectedDuration.value = duration;
};
</script>

<template>
    <div class="w-full space-y-4">
        <!-- Duration Selector -->
        <div class="flex items-center gap-3 mb-4">
            <span class="text-sm font-medium text-slate-300">Durasi:</span>
            <div class="flex gap-2">
                <button
                    v-for="opt in durationOptions"
                    :key="opt.value"
                    @click="onDurationChange(opt.value)"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-sm font-medium transition border',
                        selectedDuration === opt.value
                            ? 'bg-orange-500/20 border-orange-500 text-orange-300'
                            : 'bg-slate-800/60 border-slate-700 text-slate-400 hover:border-slate-600'
                    ]"
                >
                    {{ opt.label }}
                </button>
            </div>
        </div>

        <!-- Slot Grid -->
        <div v-if="groupedSlots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <button
                v-for="item in groupedSlots"
                :key="`${item.start_time}-${item.end_time}`"
                @click="onChoose(item)"
                class="group relative rounded-lg border-2 bg-slate-800/40 p-4 transition-all border-emerald-500/50 hover:border-emerald-500 hover:bg-emerald-500/10 cursor-pointer"
            >
                <!-- Time Period Name -->
                <div class="mb-2 text-center">
                    <p class="text-xs font-semibold text-slate-400">{{ item.time_period }}</p>
                </div>

                <!-- Time Range -->
                <div class="mb-3 text-center">
                    <p class="text-sm font-semibold text-white">{{ item.start_time }}</p>
                    <p class="text-xs text-slate-400">-</p>
                    <p class="text-sm font-semibold text-white">{{ item.end_time }}</p>
                </div>

                <!-- Duration -->
                <div class="mb-2 text-center">
                    <p class="text-xs text-slate-500">{{ item.duration_minutes }} menit</p>
                </div>

                <!-- Price -->
                <div class="mb-3 text-center">
                    <p class="text-xs text-orange-400">{{ formatCurrency(item.price_per_hour) }}/jam</p>
                    <p class="text-sm font-bold text-emerald-400 mt-1">{{ formatCurrency(item.total_price) }}</p>
                </div>

                <!-- Status Badge -->
                <div class="mx-auto inline-flex px-3 py-1.5 rounded-md font-medium text-sm transition-all bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 group-hover:bg-emerald-500/30">
                    {{ item.status }}
                </div>
            </button>
        </div>

        <div v-else class="text-center py-8 text-slate-400">
            Tidak ada slot tersedia dengan durasi {{ selectedDuration }} menit.
            <br />Coba pilih durasi yang lebih pendek.
        </div>

        <!-- Original static items (fallback for day-schedule data) -->
        <div v-if="groupedSlots.length === 0 && items.length > 0 && items[0].status === 'Tersedia'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <button
                v-for="item in items"
                :key="item.start_time"
                @click="onChoose({ ...item, duration_minutes: selectedDuration, total_price: (selectedDuration / 60) * (item.price || item.price_per_hour) })"
                :disabled="item.status !== 'Tersedia'"
                class="group relative rounded-lg border-2 bg-slate-800/40 p-4 transition-all"
                :class="[
                    item.status === 'Tersedia'
                        ? 'border-emerald-500/50 hover:border-emerald-500 hover:bg-emerald-500/10 cursor-pointer'
                        : 'border-red-500/50 cursor-not-allowed',
                ]"
            >
                <div class="mb-2 text-center">
                    <p class="text-xs font-semibold text-slate-400">{{ item.time_period }}</p>
                </div>
                <div class="mb-3 text-center">
                    <p class="text-sm font-semibold text-white">{{ item.start_time }}</p>
                    <p class="text-xs text-slate-400">-</p>
                    <p class="text-sm font-semibold text-white">{{ item.end_time }}</p>
                </div>
                <div class="mb-3 text-center">
                    <p class="text-xs text-orange-400">{{ formatCurrency(item.price || item.price_per_hour) }}/jam</p>
                </div>
                <div
                    class="mx-auto inline-flex px-3 py-1.5 rounded-md font-medium text-sm transition-all"
                    :class="
                        item.status === 'Tersedia'
                            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 group-hover:bg-emerald-500/30'
                            : 'bg-red-500/20 text-red-300 border border-red-500/30'
                    "
                >
                    {{ item.status }}
                </div>
            </button>
        </div>

        <!-- Fully booked -->
        <div v-if="items.length > 0 && items.every(i => i.status === 'Terbooking')" class="text-center py-8 text-slate-400">
            Semua slot sudah terbooking untuk tanggal ini.
        </div>
    </div>
</template>
