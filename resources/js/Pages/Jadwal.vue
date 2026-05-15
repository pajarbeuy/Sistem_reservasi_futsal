<script setup>
import { ref } from 'vue';

import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import JadwalGrid from '@/Components/JadwalGrid.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';


const scheduleItems = ref([
    // Semua slot masih tersedia
    // Properti wajib: jam, status, harga
    { time: '08:00', status: 'Tersedia', harga: 'Harga Pagi' },
    { time: '09:00', status: 'Tersedia', harga: 'Harga Pagi' },
    { time: '10:00', status: 'Tersedia', harga: 'Harga Siang' },
    { time: '11:00', status: 'Tersedia', harga: 'Harga Siang' },
    { time: '12:00', status: 'Tersedia', harga: 'Harga Siang' },
    { time: '13:00', status: 'Tersedia', harga: 'Harga Siang' },
    { time: '14:00', status: 'Tersedia', harga: 'Harga Siang' },
    { time: '15:00', status: 'Tersedia', harga: 'Harga Siang' },
    { time: '16:00', status: 'Tersedia', harga: 'Harga Malam' },
    { time: '17:00', status: 'Tersedia', harga: 'Harga Malam' },
    { time: '18:00', status: 'Tersedia', harga: 'Harga Malam' },
    { time: '19:00', status: 'Tersedia', harga: 'Harga Malam' },
    { time: '19:30', status: 'Tersedia', harga: 'Harga Malam' },
]);

const onChoose = (time) => {
    // nanti bisa diarahkan ke proses booking
    // console.log('choose time', time)
};
</script>

<template>
    <FutsalLayout>
        <div>
            <div class="max-w-6xl mx-auto px-4">

            <div class="py-10">
                <h1 class="text-4xl md:text-5xl font-bold">Jadwal Reservasi</h1>
                <p class="text-slate-300 mt-3">Pilih slot waktu yang tersedia.</p>
            </div>

            <JadwalGrid
                start="08:00"
                end="19:30"
                :interval-minutes="60"
                :items="scheduleItems"
                @choose="onChoose"
            />
        </div>

        <FooterFutsal />
    </div>
    </FutsalLayout>
</template>

