<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const menuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Navigation -->
        <nav class="fixed w-full top-0 z-50 bg-slate-950/70 backdrop-blur-sm shadow-none">

            <div class="max-w-6xl mx-auto px-4 flex justify-between items-center h-[70px]">
                <img src="/images/logo_transparent.png" alt="FUTSAL 35 Logo" class="h-12 w-auto">

                <div class="hidden md:flex gap-8 items-center">
                    <Link href="/" class="text-white hover:text-green-500 transition text-sm no-underline">
                        Beranda
                    </Link>
                    <Link href="/jadwal" class="text-white hover:text-green-500 transition text-sm no-underline">
                        Jadwal
                    </Link>
                    <Link href="/lapangan" class="text-white hover:text-green-500 transition text-sm no-underline">
                        Lapangan
                    </Link>
                    <Link href="/harga" class="text-white hover:text-green-500 transition text-sm no-underline">
                        Harga
                    </Link>

                    <Link href="/tentang" class="text-white hover:text-green-500 transition text-sm no-underline">
                        Tentang Kami
                    </Link>



                </div>


                <div class="hidden md:flex gap-3 items-center">
                    <template v-if="$page.props.auth.user">
                        <div class="flex items-center gap-3">
                            <span class="text-white text-sm font-medium">Halo, {{ $page.props.auth.user.name }}</span>
                            <Link :href="$page.props.auth.isAdmin ? '/admin/dashboard' : '/dashboard'" class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded hover:bg-green-600 transition no-underline">
                                Dashboard
                            </Link>
                            <Link href="/logout" method="post" as="button" class="px-4 py-2 border border-red-500 text-red-500 text-sm font-semibold rounded hover:bg-red-500 hover:text-white transition no-underline bg-transparent cursor-pointer">
                                Logout
                            </Link>
                        </div>
                    </template>
                    <template v-else>
                        <Link href="/login" class="px-5 py-2 border border-white text-white text-sm font-semibold rounded hover:bg-white hover:text-gray-900 transition no-underline">
                            Masuk
                        </Link>
                        <Link href="/register" class="px-5 py-2 bg-green-500 text-white text-sm font-semibold rounded hover:bg-green-600 transition no-underline">
                            Daftar
                        </Link>
                    </template>
                </div>

                <button
                    @click="menuOpen = !menuOpen"
                    class="md:hidden text-white text-2xl bg-none border-none cursor-pointer"
                >
                    ☰
                </button>
            </div>
        </nav>

        <main class="pt-[70px]">
            <div class="-mt-[70px] pt-[70px]">
                <slot />
            </div>
        </main>
    </div>
</template>

