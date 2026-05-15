<script setup>
import { computed } from 'vue';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    start: {
        type: String,
        default: '08:00',
    },
    end: {
        type: String,
        default: '19:30',
    },
    intervalMinutes: {
        type: Number,
        default: 60,
    },
    items: {
        type: Array,
        default: () => [],
        // item shape: { time: '08:00', status: 'Tersedia'|'Terbooking', label?: string }
    },
});

const minutesFromHHMM = (hhmm) => {
    const [hh, mm] = hhmm.split(':').map(Number);
    return hh * 60 + mm;
};

const hhmmFromMinutes = (total) => {
    const hh = String(Math.floor(total / 60)).padStart(2, '0');
    const mm = String(total % 60).padStart(2, '0');
    return `${hh}:${mm}`;
};

const times = computed(() => {
    const startMin = minutesFromHHMM(props.start);
    const endMin = minutesFromHHMM(props.end);

    const list = [];
    for (let m = startMin; m <= endMin; m += props.intervalMinutes) {
        list.push(hhmmFromMinutes(m));
    }

    // Ensure end time appears if not aligned by interval
    if (list[list.length - 1] !== props.end) list.push(props.end);
    return list;
});

const getItem = (time) => {
    return props.items.find((it) => it.time === time);
};

const buttonFor = (time) => {
    const item = getItem(time);
    const status = item?.status ?? 'Tersedia';

    if (status === 'Terbooking') {
        return {
            component: SecondaryButton,
            props: {
                disabled: true,
            },
            label: item?.label ?? 'Terbooking',
        };
    }

    return {
        component: PrimaryButton,
        props: {},
        label: item?.label ?? 'Tersedia',
    };
};

const getItemPrice = (time) => {
    return props.items.find((it) => it.time === time)?.harga ?? null;
};

const getItemLabel = (time) => {
    return props.items.find((it) => it.time === time)?.label ?? null;
};

const emit = defineEmits(['choose']);

const onChoose = (time) => {
    emit('choose', time);
};
</script>

<template>
    <div class="rounded-2xl border border-emerald-500/30 bg-slate-900/60 p-6 shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-white">Jadwal Reservasi</h3>
                <p class="text-sm text-slate-300">Tersedia & Terbooking per slot waktu</p>
            </div>
            <div class="text-xs text-slate-400">
                {{ start }} - {{ end }} ({{ intervalMinutes }} menit)
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="time in times"
                :key="time"
                class="rounded-xl border border-white/10 bg-slate-950/30 p-4"
            >
                <div class="flex items-center justify-between mb-3">
                    <div class="text-white font-semibold">{{ time }}</div>
                    <div class="flex items-center gap-2">
                        <div class="text-xs px-2 py-0.5 rounded-full" :class="getItem(time)?.status === 'Terbooking' ? 'bg-rose-500/15 text-rose-200 border border-rose-500/20' : 'bg-emerald-500/15 text-emerald-200 border border-emerald-500/20'">
                            {{ getItem(time)?.status ?? 'Tersedia' }}
                        </div>
                    </div>
                </div>

                <div v-if="getItemPrice(time)" class="text-xs text-slate-200/90 mb-3">
                    Harga: <span class="font-semibold text-emerald-200">{{ getItemPrice(time) }}</span>
                </div>

                <component
                    :is="buttonFor(time).component"
                    v-bind="buttonFor(time).props"
                    class="w-full justify-center"
                    @click="getItem(time)?.status === 'Terbooking' ? null : onChoose(time)"
                >
                    {{ buttonFor(time).label }}
                </component>
            </div>
        </div>
    </div>
</template>

