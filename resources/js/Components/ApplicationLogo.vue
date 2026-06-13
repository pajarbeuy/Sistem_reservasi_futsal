<script setup>
import { ref } from 'vue';

const logoSrc = ref('/images/logo.png?t=' + Date.now());
</script>

<template>
    <img :src="logoSrc" alt="FUTSAL 35" class="h-12 w-auto mix-blend-lighten" />
</template>
