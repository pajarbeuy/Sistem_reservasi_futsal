<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    // Data slot jadwal dasar (atomic) yang diterima dari parent, biasanya per 30 menit atau 1 jam
    items: {
        type: Array,
        required: true,
        // Contoh bentuk item: { start_time: '08:00', end_time: '09:00', status: 'tersedia'|'booked', price_per_hour: 100000, time_period: 'Pagi' }
    },
    // Jika readonly true, user tidak bisa mengklik jadwal (hanya untuk tampilan)
    readonly: {
        type: Boolean,
        default: false,
    },
});

// Event emit ketika user memilih (mengklik) suatu slot jadwal
const emit = defineEmits(['choose']);

// Menyimpan durasi yang sedang dipilih oleh user (default 60 menit / 1 jam)
const selectedDuration = ref(60); 

// Opsi durasi yang bisa dipilih oleh user di atas grid jadwal
const durationOptions = [
    { value: 30, label: '30 menit' },
    { value: 60, label: '1 jam' },
    { value: 90, label: '1.5 jam' },
    { value: 120, label: '2 jam' },
];

/**
 * Memformat angka menjadi format mata uang Rupiah (IDR)
 * @param {number} value - Nominal harga
 * @returns {string} - Harga dalam format "Rp xxx.xxx"
 */
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

/**
 * Mengonversi format jam "HH:MM" menjadi total menit sejak tengah malam (00:00).
 * Berguna untuk perhitungan durasi dan penambahan waktu secara matematis.
 * @param {string} timeStr - Waktu dalam format "HH:MM" (contoh: "06:30")
 * @returns {number} - Total menit (contoh: "06:30" menjadi 390)
 */
const timeToMinutes = (timeStr) => {
    const [h, m] = timeStr.split(':').map(Number);
    return h * 60 + m;
};

/**
 * Mengonversi total menit kembali ke format jam "HH:MM".
 * @param {number} mins - Total menit sejak tengah malam
 * @returns {string} - Waktu dalam format "HH:MM" (contoh: 390 menjadi "06:30")
 */
const minutesToTime = (mins) => {
    const h = Math.floor(mins / 60).toString().padStart(2, '0');
    const m = (mins % 60).toString().padStart(2, '0');
    return `${h}:${m}`;
};

/**
 * Mengelompokkan dan membangun slot-slot waktu berdasarkan durasi yang dipilih user.
 * Logika ini akan mencari semua jam mulai (start_time) yang tersedia dari data API, 
 * lalu memvalidasi apakah untuk blok waktu sepanjang `selectedDuration` ke depan 
 * semua slot-nya berstatus "tersedia".
 */
const groupedSlots = computed(() => {
    const slots = [];
    // Urutkan item dari waktu paling pagi ke malam
    const sorted = [...props.items].sort((a, b) => timeToMinutes(a.start_time) - timeToMinutes(b.start_time));

    // Membuat kumpulan (Set) menit dari setiap slot yang statusnya "tersedia".
    // Digunakan untuk lookup / pencarian ketersediaan secara cepat di dalam loop.
    const availableStartTimes = new Set(
        sorted.filter(i => i.status === 'tersedia' || i.status === 'Tersedia').map(s => timeToMinutes(s.start_time))
    );

    const duration = selectedDuration.value;

    // Loop ke setiap slot dasar untuk mencoba membuat paket blok berdurasi `duration`
    sorted.forEach(item => {
        const startMins = timeToMinutes(item.start_time); // Waktu mulai blok
        const endMins = startMins + duration;             // Waktu selesai blok
        const endTime = minutesToTime(endMins);           // String jam selesai

        let allAvailable = true;
        
        // Cari tahu jeda waktu antar slot dasar dari API (misal 30 menit)
        const slotStep = sorted.length > 0
            ? timeToMinutes(sorted[0].end_time) - timeToMinutes(sorted[0].start_time)
            : 30;

        // Validasi: Cek apakah SETIAP irisan slot dalam rentang durasi ini tersedia
        // Misal user pilih 2 jam (120 menit) dari 06:00, maka kita cek apakah 06:00, 06:30, 07:00, dan 07:30 tersedia.
        for (let m = startMins; m < endMins; m += slotStep) {
            // Jika ada satu saja slot yang tidak ada di Set "tersedia", maka seluruh blok dianggap "Terbooking"
            if (!availableStartTimes.has(m)) {
                allAvailable = false;
                break;
            }
        }

        // Hitung total harga sesuai durasi. Misal durasi 90 menit (1.5 jam) dikalikan harga per jam.
        const totalPrice = (duration / 60) * item.price_per_hour;

        slots.push({
            start_time: item.start_time,
            end_time: endTime,
            time_period: item.time_period,
            price_per_hour: item.price_per_hour,
            total_price: totalPrice,
            duration_minutes: duration,
            status: allAvailable ? 'Tersedia' : 'Terbooking',
        });
    });

    return slots;
});

// Trigger ketika user memilih slot jadwal
const onChoose = (item) => {
    emit('choose', item);
};

// Trigger ketika user mengubah opsi durasi
const onDurationChange = (duration) => {
    selectedDuration.value = duration;
};
</script>

<template>
    <div class="w-full space-y-4">
        <!-- Duration Selector -->
        <div class="flex items-center gap-3 mb-4">
            <span class="text-sm font-medium text-slate-300">Durasi:</span>
            <div class="flex gap-2">
                <button
                    v-for="opt in durationOptions"
                    :key="opt.value"
                    @click="onDurationChange(opt.value)"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-sm font-medium transition border',
                        selectedDuration === opt.value
                            ? 'bg-orange-500/20 border-orange-500 text-orange-300'
                            : 'bg-slate-800/60 border-slate-700 text-slate-400 hover:border-slate-600'
                    ]"
                >
                    {{ opt.label }}
                </button>
            </div>
        </div>

        <!-- Slot Grid -->
        <div v-if="groupedSlots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <button
                v-for="item in groupedSlots"
                :key="`${item.start_time}-${item.end_time}`"
                @click="!readonly && item.status === 'Tersedia' ? onChoose(item) : null"
                :disabled="readonly || item.status !== 'Tersedia'"
                class="group relative rounded-lg border-2 bg-slate-800/40 p-4 transition-all"
                :class="[
                    item.status === 'Tersedia' && !readonly
                        ? 'border-emerald-500/50 hover:border-emerald-500 hover:bg-emerald-500/10 cursor-pointer'
                        : item.status === 'Tersedia' && readonly
                        ? 'border-emerald-500/50 cursor-default'
                        : 'border-red-500/50 cursor-not-allowed opacity-75'
                ]"
            >
                <!-- Time Period Name -->
                <div class="mb-2 text-center">
                    <p class="text-xs font-semibold text-slate-400">{{ item.time_period }}</p>
                </div>

                <!-- Time Range -->
                <div class="mb-3 text-center">
                    <p class="text-sm font-semibold text-white">{{ item.start_time }}</p>
                    <p class="text-xs text-slate-400">-</p>
                    <p class="text-sm font-semibold text-white">{{ item.end_time }}</p>
                </div>

                <!-- Duration -->
                <div class="mb-2 text-center">
                    <p class="text-xs text-slate-500">{{ item.duration_minutes }} menit</p>
                </div>

                <!-- Price -->
                <div class="mb-3 text-center">
                    <p class="text-xs text-orange-400">{{ formatCurrency(item.price_per_hour) }}/jam</p>
                    <p class="text-sm font-bold mt-1" :class="item.status === 'Tersedia' ? 'text-emerald-400' : 'text-slate-400'">{{ formatCurrency(item.total_price) }}</p>
                </div>

                <!-- Status Badge -->
                <div class="mx-auto inline-flex px-3 py-1.5 rounded-md font-medium text-sm transition-all"
                     :class="[
                         item.status === 'Tersedia' && !readonly
                            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 group-hover:bg-emerald-500/30'
                            : item.status === 'Tersedia' && readonly
                            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                            : 'bg-red-500/20 text-red-300 border border-red-500/30'
                     ]">
                    {{ item.status }}
                </div>
            </button>
        </div>

        <div v-else class="text-center py-8 text-slate-400">
            Tidak ada slot tersedia dengan durasi {{ selectedDuration }} menit.
            <br />Coba pilih durasi yang lebih pendek.
        </div>

        <!-- Original static items (fallback for day-schedule data) -->
        <div v-if="groupedSlots.length === 0 && items.length > 0 && items[0].status === 'Tersedia'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <button
                v-for="item in items"
                :key="item.start_time"
                @click="!readonly ? onChoose({ ...item, duration_minutes: selectedDuration, total_price: (selectedDuration / 60) * (item.price || item.price_per_hour) }) : null"
                :disabled="readonly || item.status !== 'Tersedia'"
                class="group relative rounded-lg border-2 bg-slate-800/40 p-4 transition-all"
                :class="[
                    item.status === 'Tersedia' && !readonly
                        ? 'border-emerald-500/50 hover:border-emerald-500 hover:bg-emerald-500/10 cursor-pointer'
                        : item.status === 'Tersedia' && readonly
                        ? 'border-emerald-500/50 cursor-default'
                        : 'border-red-500/50 cursor-not-allowed',
                ]"
            >
                <div class="mb-2 text-center">
                    <p class="text-xs font-semibold text-slate-400">{{ item.time_period }}</p>
                </div>
                <div class="mb-3 text-center">
                    <p class="text-sm font-semibold text-white">{{ item.start_time }}</p>
                    <p class="text-xs text-slate-400">-</p>
                    <p class="text-sm font-semibold text-white">{{ item.end_time }}</p>
                </div>
                <div class="mb-3 text-center">
                    <p class="text-xs text-orange-400">{{ formatCurrency(item.price || item.price_per_hour) }}/jam</p>
                </div>
                <div
                    class="mx-auto inline-flex px-3 py-1.5 rounded-md font-medium text-sm transition-all"
                    :class="[
                        item.status === 'Tersedia' && !readonly
                            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 group-hover:bg-emerald-500/30'
                            : item.status === 'Tersedia' && readonly
                            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                            : 'bg-red-500/20 text-red-300 border border-red-500/30'
                    ]"
                >
                    {{ item.status }}
                </div>
            </button>
        </div>

        <!-- Fully booked -->
        <div v-if="items.length > 0 && items.every(i => i.status === 'Terbooking')" class="text-center py-8 text-slate-400">
            Semua slot sudah terbooking untuk tanggal ini.
        </div>
    </div>
</template>
