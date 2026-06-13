<script setup>
import { ref, onMounted } from 'vue';
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';

const prices = ref([]);
const loading = ref(true);
const error = ref('');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const loadPrices = async () => {
    try {
        const response = await fetch('/api/prices');
        if (!response.ok) {
            throw new Error(`API error: ${response.status} ${response.statusText}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response type: expected JSON');
        }
        const data = await response.json();
        prices.value = data.data || data;
    } catch (err) {
        error.value = 'Gagal memuat informasi harga: ' + err.message;
        console.error('Error loading prices:', err);
    } finally {
        loading.value = false;
    }
};

const onChoose = () => {
    window.location.href = '/booking-form';
};

onMounted(() => {
    loadPrices();
});
</script>

<template>
    <FutsalLayout>
        <div>
            <div class="max-w-6xl mx-auto px-4">
                <div class="py-10">
                    <h1 class="text-4xl md:text-5xl font-bold">Harga Reservasi</h1>
                    <p class="text-slate-300 mt-3">Paket harga untuk setiap waktu</p>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded text-red-200">
                    {{ error }}
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                    <p class="text-white text-lg">Memuat harga...</p>
                </div>

                <!-- Price Grid -->
                <div v-else class="pb-10">
                    <div v-if="prices.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-for="price in prices" :key="price.id"
                            class="bg-white/10 backdrop-blur rounded-lg p-6 border border-white/20 hover:border-orange-500 transition">
                            <h3 class="text-2xl font-bold text-white mb-2">{{ price.time_period }}</h3>
                            <p class="text-slate-300 text-sm mb-4">{{ price.start_time }} - {{ price.end_time }}</p>
                            <div class="mb-4 border-t border-white/20 pt-4">
                                <p class="text-orange-400 font-semibold text-3xl">
                                    {{ formatCurrency(price.price_per_hour) }}
                                    <span class="text-sm text-slate-300">/jam</span>
                                </p>
                            </div>
                            <p v-if="price.description" class="text-slate-300 text-sm mb-6">{{ price.description }}</p>
                            <button
                                @click="onChoose"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded-lg transition"
                            >
                                Pesan Sekarang
                            </button>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-white text-lg">Belum ada informasi harga tersedia.</p>
                    </div>
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>


