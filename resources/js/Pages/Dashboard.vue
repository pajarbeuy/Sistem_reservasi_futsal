<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const fields = ref([
    {
        id: 1,
        name: 'Field Arena A',
        type: 'Synthetic Grass • Professional Grade',
        price: 150000,
        status: 'Available',
        image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=800'
    },
    {
        id: 2,
        name: 'Field Arena B',
        type: 'Vinyl Floor • Multi-purpose',
        price: 120000,
        status: 'Available',
        image: 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&q=80&w=800'
    }
]);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Manage Fields
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <!-- Toolbar -->
                <div class="flex items-center justify-between mb-6">
                    <p class="text-slate-500 text-sm">Showing {{ fields.length }} fields</p>
                    <button class="px-4 py-2 bg-sky-500 text-white text-sm font-medium rounded-lg hover:bg-sky-600 transition shadow-lg shadow-sky-500/20">
                        + Add New Field
                    </button>
                </div>

                <!-- Field Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="field in fields" :key="field.id" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                        <div class="h-48 bg-slate-200 relative">
                            <img :src="field.image" :alt="field.name" class="w-full h-full object-cover">
                            <div class="absolute top-4 right-4 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-md">{{ field.status }}</div>
                        </div>
                        <div class="p-6">
                            <h4 class="text-xl font-bold text-slate-800 mb-1">{{ field.name }}</h4>
                            <p class="text-slate-500 text-sm mb-4">{{ field.type }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-sky-500">
                                    Rp {{ field.price.toLocaleString('id-ID') }}
                                    <span class="text-sm font-normal text-slate-400">/hr</span>
                                </span>
                                <div class="flex space-x-2">
                                    <button class="p-2 text-slate-400 hover:text-sky-500 hover:bg-sky-50 transition rounded-lg">✏️</button>
                                    <button class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 transition rounded-lg">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>