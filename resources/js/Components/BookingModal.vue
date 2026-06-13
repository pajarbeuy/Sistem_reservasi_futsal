<script setup>
import { ref } from 'vue';

const props = defineProps({
    isOpen: Boolean,
    fieldName: String,
    fieldType: String,
    selectedDate: String,
    startTime: String,
    endTime: String,
    price: Number,
});

const emit = defineEmits(['close', 'confirm']);

const phoneNumber = ref('');
const notes = ref('');
const isSubmitting = ref(false);
const error = ref('');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const handleSubmit = async () => {
    error.value = '';
    
    if (!phoneNumber.value) {
        error.value = 'Nomor telepon harus diisi';
        return;
    }

    isSubmitting.value = true;
    try {
        emit('confirm', {
            phoneNumber: phoneNumber.value,
            notes: notes.value,
        });
    } catch (err) {
        error.value = err.message || 'Terjadi kesalahan saat booking';
    } finally {
        isSubmitting.value = false;
    }
};

const handleClose = () => {
    phoneNumber.value = '';
    notes.value = '';
    error.value = '';
    emit('close');
};
</script>

<template>
    <!-- Modal Backdrop -->
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm transition-opacity"
        @click="handleClose"
    />

    <!-- Modal -->
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="w-full max-w-md rounded-2xl bg-slate-900 shadow-2xl border border-slate-700 flex flex-col max-h-[90vh]"
            @click.stop
        >
            <!-- Header -->
            <div class="border-b border-slate-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
                <h2 class="text-xl font-bold text-white">Konfirmasi Booking</h2>
                <button
                    @click="handleClose"
                    class="text-slate-400 hover:text-white transition"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1">
                <!-- Error Message -->
                <div v-if="error" class="rounded-lg bg-red-500/20 border border-red-500 px-4 py-3 text-sm text-red-200">
                    {{ error }}
                </div>

                <!-- Lapangan Info -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-300">Lapangan</label>
                    <div class="rounded-lg bg-slate-800 px-4 py-3 border border-slate-700">
                        <div class="font-semibold text-white">{{ fieldName }}</div>
                        <div class="text-sm text-slate-400">{{ fieldType }}</div>
                    </div>
                </div>

                <!-- Date Info -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-300">Tanggal</label>
                    <div class="rounded-lg bg-slate-800 px-4 py-3 border border-slate-700 text-white">
                        {{ formatDate(selectedDate) }}
                    </div>
                </div>

                <!-- Time Info -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-300">Waktu</label>
                    <div class="rounded-lg bg-slate-800 px-4 py-3 border border-slate-700 text-white">
                        {{ startTime }} - {{ endTime }}
                    </div>
                </div>

                <!-- Price Info -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-300">Harga Total</label>
                    <div class="rounded-lg bg-emerald-500/10 px-4 py-3 border border-emerald-500/30">
                        <div class="text-2xl font-bold text-emerald-400">
                            {{ formatCurrency(price) }}
                        </div>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-300">Nomor Telepon *</label>
                    <input
                        v-model="phoneNumber"
                        type="tel"
                        placeholder="08xxxxxxxxxx"
                        class="w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-2 text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                    />
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-300">Catatan (Opsional)</label>
                    <textarea
                        v-model="notes"
                        placeholder="Tambahkan catatan khusus..."
                        rows="3"
                        class="w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-2 text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                    />
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-slate-700 px-6 py-4 flex gap-3 flex-shrink-0 bg-slate-900">
                <button
                    @click="handleClose"
                    class="flex-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 transition"
                >
                    Batal
                </button>
                <button
                    @click="handleSubmit"
                    :disabled="isSubmitting"
                    class="flex-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 transition disabled:bg-emerald-600/50 disabled:cursor-not-allowed"
                >
                    <span v-if="isSubmitting">Memproses...</span>
                    <span v-else>Pesan Sekarang</span>
                </button>
            </div>
        </div>
    </div>
</template>
