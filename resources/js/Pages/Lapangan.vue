<script setup>
defineOptions({ name: 'LapanganPage' });
import { ref, onMounted } from 'vue';
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';
import { apiGet } from '@/utils/api';

const fields = ref([]);
const loading = ref(true);
const error = ref('');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const getFieldImage = (field) => {
    if (field.name?.toLowerCase().includes('lapangan a') || field.price_per_hour >= 200000) {
        return '/images/Lapang A.png';
    }
    return '/images/Lapang B.png';
};

const loadFields = async () => {
    try {
        const data = await apiGet('/api/fields');
        fields.value = data.data || data;
    } catch (err) {
        error.value = 'Gagal memuat daftar lapangan: ' + err.message;
        console.error('Error loading fields:', err);
    } finally {
        loading.value = false;
    }
};

const goToBooking = (fieldId) => {
    window.location.href = `/lapangan/${fieldId}/booking`;
};

onMounted(() => {
    loadFields();
});
</script>

<template>
    <FutsalLayout>
        <div>
            <div class="max-w-6xl mx-auto px-4">
                <div class="py-10">
                    <h1 class="text-4xl md:text-5xl font-bold">Pilih Lapangan</h1>
                    <p class="text-slate-300 mt-3">Lapangan futsal berkualitas dengan fasilitas lengkap</p>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded text-red-200">
                    {{ error }}
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                    <p class="text-white text-lg">Memuat daftar lapangan...</p>
                </div>

                <!-- Fields Grid -->
                <div v-else class="pb-10">
                    <div v-if="fields.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="field in fields"
                            :key="field.id"
                            @click="goToBooking(field.id)"
                            class="bg-white/10 backdrop-blur rounded-lg overflow-hidden border border-white/20 hover:border-orange-500 transition group cursor-pointer text-left"
                            role="button"
                            tabindex="0"
                        >
                            <div class="h-48 overflow-hidden">
                                <img
                                    :src="getFieldImage(field)"
                                    :alt="field.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            </div>
                            <div class="p-6">
                                <h3 class="text-2xl font-bold text-white mb-2">{{ field.name }}</h3>
                                <p class="text-slate-300 text-sm mb-4">{{ field.type }}</p>
                                <p class="text-slate-300 text-sm mb-4">
                                    {{ field.description }}
                                </p>
                                <div class="flex justify-between items-center">
                                    <p class="text-orange-400 font-semibold">
                                        {{ formatCurrency(field.price_per_hour) }}/jam
                                    </p>
                                    <span v-if="field.is_available" class="text-green-400 text-sm font-semibold">Tersedia</span>
                                    <span v-else class="text-red-400 text-sm font-semibold">Tidak Tersedia</span>
                                </div>
                                <div class="w-full mt-4 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded-lg transition text-center">
                                    Pesan Sekarang
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-white text-lg">Belum ada lapangan tersedia.</p>
                    </div>
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>


