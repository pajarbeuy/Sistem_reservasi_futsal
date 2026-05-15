<script setup>
import { computed } from 'vue';

import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    lapangan: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    imageSrc: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['select']);

const selectNow = () => emit('select', props.lapangan);

const badgeTone = computed(() => {
    // deterministic tones per lapangan letter
    const key = props.lapangan.toUpperCase();
    if (key === 'A') return 'bg-emerald-500/15 text-emerald-200 border border-emerald-500/25';
    if (key === 'B') return 'bg-sky-500/15 text-sky-200 border border-sky-500/25';
    if (key === 'C') return 'bg-violet-500/15 text-violet-200 border border-violet-500/25';
    return 'bg-emerald-500/15 text-emerald-200 border border-emerald-500/25';
});
</script>

<template>
    <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden shadow-lg hover:shadow-xl transition">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-lg font-bold text-white">Lapangan {{ lapangan }}</h4>
                    <p class="text-sm text-slate-300 mt-1">Siap untuk pertandingan seru.</p>
                </div>
                <div class="text-xs px-2 py-0.5 rounded-full" :class="badgeTone">
                    Terbaik
                </div>
            </div>

            <div class="rounded-xl overflow-hidden border border-white/10 bg-slate-950/30">
                <slot name="image">
                    <img
                        v-if="imageSrc"
                        :src="imageSrc"
                        :alt="`Lapangan ${lapangan}`"
                        class="w-full h-40 object-cover"
                    />
                    <div v-else class="w-full h-40 flex items-center justify-center text-slate-400 text-sm">
                        Slot Gambar
                    </div>
                </slot>
            </div>

            <p class="text-sm text-slate-300 mt-4 leading-relaxed">
                <slot>
                    {{ description }}
                </slot>
            </p>

            <div class="mt-5">
                <PrimaryButton class="w-full justify-center" @click="selectNow">
                    Pilih
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>

