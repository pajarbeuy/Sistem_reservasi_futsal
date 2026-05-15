<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

import NavLink from '@/Components/NavLink.vue';

const sections = computed(() => [
    {
        title: 'Tentang',
        links: [
            { label: 'FUTSAL 35', href: '#tentang' },
        ],
    },
    {
        title: 'Menu',
        links: [
            { label: 'Jadwal', href: '/jadwal' },
            { label: 'Lapangan', href: '/lapangan' },
            { label: 'Harga', href: '/harga' },
        ],
    },
    {
        title: 'Kontak',
        links: [
            { label: 'WhatsApp', href: '#kontak' },
            { label: 'Email', href: '#kontak' },
        ],
    },
]);
</script>

<template>
    <footer class="bg-slate-950 border-t border-emerald-500/20 mt-20">
        <div class="max-w-6xl mx-auto px-4 py-12">
            <div class="grid md:grid-cols-3 gap-10">
                <div v-for="col in sections" :key="col.title">
                    <h4 class="text-emerald-300 font-bold text-lg mb-4">{{ col.title }}</h4>
                    <ul class="space-y-2">
                        <li v-for="l in col.links" :key="l.label">
                            <NavLink :href="l.href" class="text-sm text-slate-300 hover:text-emerald-300">
                                {{ l.label }}
                            </NavLink>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-white/10 text-center text-sm text-slate-400">
                <p>© {{ new Date().getFullYear() }} FUTSAL 35. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>
</template>

