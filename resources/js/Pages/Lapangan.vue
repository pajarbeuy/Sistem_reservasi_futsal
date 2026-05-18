<script setup>
import FutsalLayout from '@/Layouts/FutsalLayout.vue';
import LapanganCard from '@/Components/LapanganCard.vue';
import FooterFutsal from '@/Components/FooterFutsal.vue';

const onSelect = () => {
    // nanti bisa diarahkan ke proses booking
};
</script>

<template>
    <FutsalLayout>
        <div>
            <div class="max-w-6xl mx-auto px-4">
                <div class="py-10">
                    <h1 class="text-4xl md:text-5xl font-bold">Pilih Lapangan</h1>
                    <p class="text-slate-300 mt-3">Lapangan A, B, dan C</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6 pb-10">
                    <LapanganCard
                        lapangan="A"
                        image-src="/images/field.png"
                        description="Lapangan A siap untuk pertandingan dengan pencahayaan maksimal dan area tribun yang nyaman."
                        @select="onSelect"
                    />
                    <LapanganCard
                        lapangan="B"
                        image-src="/images/field.png"
                        description="Lapangan B cocok untuk latihan terstruktur maupun kompetisi santai bersama teman-teman."
                        @select="onSelect"
                    />
                    <LapanganCard
                        lapangan="C"
                        image-src="/images/field.png"
                        description="Lapangan C dengan permukaan lapangan terawat serta suasana pertandingan yang kompetitif."
                        @select="onSelect"
                    />
                </div>
            </div>

            <FooterFutsal />
        </div>
    </FutsalLayout>
</template>


