<script setup>
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';
</script>

<template>
    <FutsalLayout>
        <div class="max-w-6xl mx-auto px-4">
            <div class="pt-10 pb-16">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold">Tentang <span class="text-emerald-400">FUTSAL 35</span></h1>
                    <p class="text-slate-300 mt-4 max-w-3xl mx-auto leading-relaxed">
                        FUTSAL 35 adalah web reservasi lapangan futsal online yang memudahkan kamu untuk memilih jadwal, cek ketersediaan slot, dan
                        melakukan booking dengan cepat.
                    </p>
                </div>

                <div class="mt-12 grid md:grid-cols-3 gap-6">
                    <div class="bg-slate-900/40 border border-white/10 rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-white">Reservasi Cepat</h2>
                        <p class="text-slate-300 mt-2 leading-relaxed">
                            Pilih lapangan dan slot waktu yang tersedia tanpa antri.
                        </p>
                    </div>
                    <div class="bg-slate-900/40 border border-white/10 rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-white">Harga Transparan</h2>
                        <p class="text-slate-300 mt-2 leading-relaxed">
                            Informasi harga disajikan jelas untuk kategori pagi, siang, dan malam.
                        </p>
                    </div>
                    <div class="bg-slate-900/40 border border-white/10 rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-white">Nyaman & Aman</h2>
                        <p class="text-slate-300 mt-2 leading-relaxed">
                            Proses booking dibuat lebih nyaman serta pembayaran yang tertata.
                        </p>
                    </div>
                </div>

                <div class="mt-10 bg-slate-900/40 border border-white/10 rounded-2xl p-7">
                    <h2 class="text-2xl font-bold text-white">Kenapa Harus FUTSAL 35?</h2>
                    <ul class="mt-4 space-y-3 text-slate-300">
                        <li class="flex gap-3 items-start">
                            <span class="text-emerald-300">✓</span>
                            <span>Antarmuka sederhana untuk pemesanan lapangan futsal.</span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="text-emerald-300">✓</span>
                            <span>Slot jadwal tertera sehingga kamu bisa memilih waktu yang pas.</span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="text-emerald-300">✓</span>
                            <span>Kami fokus pada pengalaman pengguna agar booking jadi lebih cepat.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>

