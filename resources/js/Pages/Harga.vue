<script setup>
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import HargaCard from '@/Components/HargaCard.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';

const onChoose = () => {
    // nanti bisa diarahkan ke proses booking
};
</script>

<template>
    <FutsalLayout>
        <div>
            <div class="max-w-6xl mx-auto px-4">
                <div class="py-10">
                    <h1 class="text-4xl md:text-5xl font-bold">Harga Reservasi</h1>
                    <p class="text-slate-300 mt-3">Pagi, Siang (Populer), dan Malam</p>
                </div>

                <div class="pb-10">
                    <HargaCard
                        harga-pagi="Rp 150rb - 200rb"
                        harga-siang="Rp 200rb - 250rb"
                        harga-malam="Rp 180rb - 240rb"
                        @choose="onChoose"
                    />
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>


