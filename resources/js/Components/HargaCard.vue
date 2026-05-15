<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    hargaPagi: {
        type: String,
        default: 'Rp 150rb - 200rb',
    },
    hargaSiang: {
        type: String,
        default: 'Rp 200rb - 250rb',
    },
    hargaMalam: {
        type: String,
        default: 'Rp 180rb - 240rb',
    },
});

const emit = defineEmits(['choose']);

const choose = (slot) => emit('choose', slot);
</script>

<template>
    <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-6 shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-white">Harga Reservasi</h3>
                <p class="text-sm text-slate-300">Pagi, Siang (Populer), dan Malam</p>
            </div>
            <div class="text-xs text-slate-400">Tema Emerald Green</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Pagi -->
            <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div class="text-white font-bold">Pagi</div>
                <div class="mt-2 text-3xl font-extrabold text-white">{{ hargaPagi }}</div>
                <div class="mt-4 text-sm text-slate-300">Cocok untuk latihan dan match santai.</div>
                <div class="mt-5">
                    <PrimaryButton class="w-full justify-center" @click="choose('Pagi')">Pilih</PrimaryButton>
                </div>
            </div>

            <!-- Siang (Populer) -->
            <div class="rounded-2xl border border-emerald-500/60 bg-emerald-500/10 p-5">
                <div class="flex items-center justify-between">
                    <div class="text-white font-bold">Siang</div>
                    <div class="text-xs px-2 py-0.5 rounded-full border border-emerald-500/50 bg-emerald-500/15 text-emerald-200">
                        Populer
                    </div>
                </div>
                <div class="mt-2 text-3xl font-extrabold text-emerald-200">{{ hargaSiang }}</div>
                <div class="mt-4 text-sm text-slate-200">Slot paling banyak dipesan. Ayo ambil sekarang.</div>
                <div class="mt-5">
                    <PrimaryButton class="w-full justify-center" @click="choose('Siang')">Pilih</PrimaryButton>
                </div>
            </div>

            <!-- Malam -->
            <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div class="text-white font-bold">Malam</div>
                <div class="mt-2 text-3xl font-extrabold text-white">{{ hargaMalam }}</div>
                <div class="mt-4 text-sm text-slate-300">Untuk pertandingan intens setelah aktivitas.</div>
                <div class="mt-5">
                    <PrimaryButton class="w-full justify-center" @click="choose('Malam')">Pilih</PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>

