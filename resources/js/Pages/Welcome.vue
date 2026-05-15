<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const features = ref([
    { id: 1, icon: '/images/booking.png', title: 'Booking Cepat', description: 'Proses booking yang cepat dan mudah hanya dalam beberapa klik saja' },
    { id: 2, icon: '/images/price.png', title: 'Harga Terjangkau', description: 'Kami menawarkan harga yang kompetitif dengan kualitas lapangan terbaik' },
    { id: 3, icon: '/images/schedule.png', title: 'Jadwal Fleksibel', description: 'Pilih jadwal yang sesuai dengan waktu luang Anda sepanjang hari' },
    { id: 4, icon: '/images/field.png', title: 'Lapangan Berkualitas', description: 'Lapangan futsal premium dengan fasilitas lengkap dan terawat dengan baik' },
    { id: 5, icon: '/images/community.png', title: 'Komunitas', description: 'Bergabunglah dengan komunitas futsal terbesar dan temukan tim baru' },
    { id: 6, icon: '/images/security.png', title: 'Keamanan', description: 'Sistem pembayaran aman dan data pribadi Anda terjaga dengan baik' }
]);

const menuOpen = ref(false);
</script>

<template>
    <Head title="FUTSAL 35 - Reservasi Lapangan Futsal Online" />

    <div class="bg-white">
        <!-- Navigation -->
        <nav class="fixed w-full top-0 z-50 bg-black/90 backdrop-blur-sm shadow-lg">
            <div class="max-w-6xl mx-auto px-4 flex justify-between items-center h-[70px]">
                <img src="/images/logo.png" alt="FUTSAL 35 Logo" class="h-12 w-auto">
                <div class="hidden md:flex gap-8 items-center">
                    <a href="#beranda" class="text-white hover:text-green-500 transition text-sm">Beranda</a>
                    <a href="#jadwal" class="text-white hover:text-green-500 transition text-sm">Jadwal</a>
                    <a href="#lapangan" class="text-white hover:text-green-500 transition text-sm">Lapangan</a>
                    <a href="#harga" class="text-white hover:text-green-500 transition text-sm">Harga</a>
                    <a href="#tentang" class="text-white hover:text-green-500 transition text-sm">Tentang Kami</a>
                </div>
                <div class="hidden md:flex gap-3 items-center">
                    <Link href="/login" class="px-5 py-2 border border-white text-white text-sm font-semibold rounded hover:bg-white hover:text-gray-900 transition no-underline">
                        Masuk
                    </Link>
                    <Link href="/register" class="px-5 py-2 bg-green-500 text-white text-sm font-semibold rounded hover:bg-green-600 transition no-underline">
                        Daftar
                    </Link>
                </div>
                <button @click="menuOpen = !menuOpen" class="md:hidden text-white text-2xl bg-none border-none cursor-pointer">
                    ☰
                </button>
            </div>
        </nav>

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
                        <a href="#lapangan" class="px-8 py-3 border-2 border-green-500 text-white font-semibold rounded-lg hover:bg-green-500 transition no-underline">
                            Lihat Jadwal
                        </a>
                    </div>
                </div>
                <div class="flex justify-center">
                    <img src="/images/logo.png" alt="FUTSAL 35 Logo" class="h-50 w-auto">
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="lapangan" class="py-20 bg-gray-100">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="text-4xl font-bold text-center mb-16 text-gray-900">
                    Kenapa Memilih <span class="text-green-500">FUTSAL 35?</span>
                </h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="feature in features" :key="feature.id" class="bg-white rounded-2xl p-8 text-center shadow-md hover:shadow-lg hover:-translate-y-2 transition">
                        <div class="text-6xl mb-4">
                            <img :src="feature.icon" :alt="feature.title" class="h-16 w-16 mx-auto">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ feature.title }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-green-500 to-green-600 text-center">
            <div class="max-w-3xl mx-auto px-4">
                <h2 class="text-4xl font-bold text-white mb-6">Siap untuk Bermain?</h2>
                <p class="text-xl text-white/90 mb-8">
                    Daftar sekarang dan dapatkan diskon khusus untuk booking pertama Anda!
                </p>
                <Link href="/register" class="inline-block px-10 py-4 bg-white text-green-500 font-bold rounded-lg hover:bg-gray-100 transition shadow-lg no-underline">
                    Daftar Gratis Sekarang
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-6xl mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h3 class="text-green-500 font-bold mb-4 text-lg">FUTSAL 35</h3>
                        <p class="text-gray-400 mb-3 text-sm">Platform booking lapangan futsal online terpercaya dan mudah digunakan.</p>
                        <p class="text-gray-400 text-sm">📍 Bandung, Indonesia</p>
                    </div>
                    <div>
                        <h3 class="text-green-500 font-bold mb-4 text-lg">Navigasi</h3>
                        <ul class="space-y-2">
                            <li><a href="#beranda" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Beranda</a></li>
                            <li><a href="#jadwal" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Jadwal</a></li>
                            <li><a href="#lapangan" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Lapangan</a></li>
                            <li><a href="#harga" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Harga</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-green-500 font-bold mb-4 text-lg">Akun</h3>
                        <ul class="space-y-2">
                            <li><Link href="/login" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Masuk</Link></li>
                            <li><Link href="/register" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Daftar</Link></li>
                            <li><a href="#" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Lupa Password</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-green-500 font-bold mb-4 text-lg">Bantuan</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">FAQ</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Hubungi Kami</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-green-500 transition text-sm no-underline">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 pt-8 text-center text-gray-500 text-sm">
                    <p>&copy; 2026 FUTSAL 35. Semua hak dilindungi. | Dibuat dengan ❤️ untuk pecinta futsal</p>
                </div>
            </div>
        </footer>
    </div>
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