@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-slate-50">
    <!-- Sidebar -->
    <div class="hidden md:flex flex-col w-64 bg-dark text-white">
        <div class="p-6">
            <h2 class="text-xl font-bold text-primary">Futsal Panel</h2>
        </div>
        <nav class="flex-1 px-4 space-y-2 mt-4">
            <a href="#" class="flex items-center px-4 py-3 bg-primary text-white rounded-xl transition">
                <span class="mr-3">🏟️</span> Fields
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition">
                <span class="mr-3">📅</span> Bookings
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition">
                <span class="mr-3">👤</span> Users
            </a>
        </nav>
        <div class="p-4 mt-auto">
            <div class="bg-slate-800 rounded-xl p-4 flex items-center">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold mr-3">A</div>
                <div>
                    <p class="text-sm font-semibold">Admin Futsal</p>
                    <p class="text-xs text-slate-400">admin@futsal.com</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-white border-b flex items-center justify-between px-8">
            <h3 class="text-lg font-semibold text-slate-800">Manage Fields</h3>
            <div class="flex items-center space-x-4">
                <button class="p-2 text-slate-400 hover:text-primary transition">🔔</button>
                <button class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-sky-500 transition shadow-lg shadow-sky-500/20">
                    + Add New Field
                </button>
            </div>
        </header>

        <!-- Dashboard Body -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Field Card Example -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="h-48 bg-slate-200 relative">
                        <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=800" alt="Field" class="w-full h-full object-cover">
                        <div class="absolute top-4 right-4 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-md">Available</div>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-bold text-slate-800 mb-1">Field Arena A</h4>
                        <p class="text-slate-500 text-sm mb-4">Synthetic Grass • Professional Grade</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-primary">Rp 150.000 <span class="text-sm font-normal text-slate-400">/hr</span></span>
                            <div class="flex space-x-2">
                                <button class="p-2 text-slate-400 hover:text-primary hover:bg-sky-50 transition rounded-lg">✏️</button>
                                <button class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 transition rounded-lg">🗑️</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Another Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="h-48 bg-slate-200 relative">
                        <img src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&q=80&w=800" alt="Field" class="w-full h-full object-cover">
                        <div class="absolute top-4 right-4 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-md">Available</div>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-bold text-slate-800 mb-1">Field Arena B</h4>
                        <p class="text-slate-500 text-sm mb-4">Vinyl Floor • Multi-purpose</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-primary">Rp 120.000 <span class="text-sm font-normal text-slate-400">/hr</span></span>
                            <div class="flex space-x-2">
                                <button class="p-2 text-slate-400 hover:text-primary hover:bg-sky-50 transition rounded-lg">✏️</button>
                                <button class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 transition rounded-lg">🗑️</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
