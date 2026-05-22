<template>
  <div class="min-h-screen bg-gradient-to-r from-slate-900 to-slate-800">
    <!-- Header -->
    <header class="w-full bg-black py-3 px-0 flex items-center justify-between">
      <div class="flex items-center gap-2 px-8">
        <img src="/images/logo.png" alt="Logo" class="h-10 w-auto" />
      </div>
      <nav class="flex-1 flex justify-center">
        <ul class="flex gap-8 text-white font-medium text-base">
          <li><a href="#" class="hover:text-green-400 transition">Beranda</a></li>
          <li><a href="#" class="hover:text-green-400 transition">Jadwal</a></li>
          <li><a href="#" class="hover:text-green-400 transition">Lapangan</a></li>
          <li><a href="#" class="hover:text-green-400 transition">Harga</a></li>
          <li><a href="#" class="hover:text-green-400 transition">Tentang Kami</a></li>
        </ul>
      </nav>
      <div class="flex items-center gap-2 px-8">
        <a href="/login" class="px-5 py-2 border border-white text-white rounded-md font-semibold hover:bg-white hover:text-black transition">Masuk</a>
        <a href="/register" class="px-5 py-2 bg-green-500 text-white rounded-md font-semibold hover:bg-green-600 transition">Daftar</a>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="flex flex-col md:flex-row items-center justify-center min-h-[80vh] px-8 gap-8">
      <div class="flex-1 max-w-xl">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-6">
          Booking Lapangan<br />
          Futsal Jadi Lebih <span class="text-green-400">Mudah</span>
        </h1>
        <p class="text-lg text-slate-200 mb-8">
          Pesan lapangan favorit mu kapan saja dan di mana saja. Cepat, mudah, dan tanpa antri!
        </p>
        <div class="flex gap-4">
          <a href="#" class="px-6 py-3 bg-green-500 text-white font-bold rounded-lg shadow hover:bg-green-600 transition">Booking Sekarang</a>
          <a href="#" class="px-6 py-3 border-2 border-green-500 text-green-400 font-bold rounded-lg hover:bg-green-500 hover:text-white transition">Lihat Jadwal</a>
        </div>
      </div>
      <div class="flex-1 flex justify-center">
        <img src="/images/logo.png" alt="Futsal 35 Logo" class="w-[340px] md:w-[400px]" />
      </div>
    </section>
  </div>
</template>
