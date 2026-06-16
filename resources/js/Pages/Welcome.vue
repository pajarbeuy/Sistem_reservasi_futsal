<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';


const features = ref([
    { id: 1, icon: '/images/booking.png', title: 'Booking Cepat', description: 'Proses booking yang cepat dan mudah hanya dalam beberapa klik saja' },
    { id: 2, icon: '/images/price.png', title: 'Harga Terjangkau', description: 'Kami menawarkan harga yang kompetitif dengan kualitas lapangan terbaik' },
    { id: 3, icon: '/images/schedule.png', title: 'Jadwal Fleksibel', description: 'Pilih jadwal yang sesuai dengan waktu luang Anda sepanjang hari' },
    { id: 4, icon: '/images/field.png', title: 'Lapangan Berkualitas', description: 'Lapangan futsal premium dengan fasilitas lengkap dan terawat dengan baik' },
    { id: 5, icon: '/images/community.png', title: 'Komunitas', description: 'Bergabunglah dengan komunitas futsal terbesar dan temukan tim baru' },
    { id: 6, icon: '/images/security.png', title: 'Keamanan', description: 'Sistem pembayaran aman dan data pribadi Anda terjaga dengan baik' },
]);

const menuOpen = ref(false);

const scheduleItems = ref([
    { time: '08:00', status: 'Tersedia' },
    { time: '09:00', status: 'Tersedia' },
    { time: '10:00', status: 'Terbooking' },
    { time: '11:00', status: 'Tersedia' },
    { time: '12:00', status: 'Terbooking' },
    { time: '13:00', status: 'Tersedia' },
    { time: '14:00', status: 'Terbooking' },
    { time: '15:00', status: 'Tersedia' },
    { time: '16:00', status: 'Tersedia' },
    { time: '17:00', status: 'Terbooking' },
    { time: '18:00', status: 'Tersedia' },
    { time: '19:00', status: 'Tersedia' },
    { time: '19:30', status: 'Tersedia' },
]);
</script>

<template>
    <Head title="FUTSAL 35 - Reservasi Lapangan Futsal Online" />

    <FutsalLayout>



        <!-- Hero Section -->
        <section id="beranda" class="min-h-screen bg-gradient-to-r from-slate-900 to-slate-800 flex items-center justify-center relative overflow-hidden mt-[70px]">
            <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-8 lg:gap-12 items-center relative z-10">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                        Booking Lapangan Futsal Jadi Lebih <span class="text-green-500">Mudah</span>
                    </h1>
                    <p class="text-lg text-gray-300 mb-8 leading-relaxed">
                        Pesan lapangan favorit mu kapan saja dan di mana saja. Cepat, mudah, dan tanpa antri!
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link href="/register" class="px-8 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition shadow-lg no-underline">
                            Booking Sekarang
                        </Link>
                        <Link href="/jadwal" class="px-8 py-3 border-2 border-green-500 text-white font-semibold rounded-lg hover:bg-green-500 transition no-underline">
                            Lihat Jadwal
                        </Link>
                    </div>
                </div>
                <div class="flex justify-center">
                    <img src="/images/logo_transparent.png" alt="FUTSAL 35 Logo" class="h-50 w-auto">
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="text-4xl font-bold text-center mb-16">
                    Kenapa Memilih <span class="text-emerald-400">FUTSAL 35?</span>
                </h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="feature in features" :key="feature.id" class="bg-slate-900/40 border border-white/10 rounded-2xl p-8 text-center shadow-md hover:shadow-lg hover:-translate-y-2 transition">
                        <div class="text-6xl mb-4">
                            <img :src="feature.icon" :alt="feature.title" class="h-16 w-16 mx-auto">
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">{{ feature.title }}</h3>
                        <p class="text-slate-300 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Catatan: Menu Jadwal/Harga/Lapangan sekarang dipisah ke halaman sendiri -->

        <FooterFutsal />
    </FutsalLayout>


</template>

<style scoped>
@keyframes bounce {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
.animate-bounce {
    animation: bounce 3s ease-in-out infinite;
}
</style>

