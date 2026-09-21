<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - SIMPEL KELURAHAN PATOKAN</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    <!-- Rich Text Editor (Trix CDN untuk form editor konten) -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            .admin-main-wrapper {
                padding-left: 18rem !important;
            }
        }
        /* Custom scrollbar sidebar */
        aside::-webkit-scrollbar { width: 5px; }
        aside::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="h-full font-sans text-slate-800 antialiased bg-slate-50" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen bg-slate-50 relative">
        
        <!-- Mobile Backdrop Overlay -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

        <!-- ========================================== -->
        <!-- 1. SIDEBAR NAVIGATION                      -->
        <!-- ========================================== -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 lg:z-30 w-72 h-screen bg-white border-r border-slate-200 text-slate-800 flex flex-col justify-between shadow-lg lg:shadow-none transition-transform duration-300 ease-in-out overflow-y-auto">
            
            <div>
                <!-- Brand Header -->
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-xs z-10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-center p-1.5 shadow-sm group-hover:scale-105 transition duration-200 shrink-0">
                            <img src="{{ !empty($systemSettings['app_logo']) ? asset('storage/' . $systemSettings['app_logo']) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlwlIShkVajC2C_tEglw59FLYjmw5n-E1vAgqplpW75A&s=10' }}" 
                                 alt="Logo Aplikasi" 
                                 class="w-full h-full object-contain drop-shadow">
                        </div>
                        <div class="min-w-0 flex flex-col justify-center">
                            <div class="font-extrabold text-sm sm:text-base tracking-tight text-slate-900 leading-none truncate">
                                {{ strtoupper($systemSettings['app_name'] ?? 'SIMPEL KELURAHAN') }}
                            </div>
                            <div class="text-[9px] sm:text-[10px] text-emerald-600/90 font-medium tracking-wide uppercase mt-1 truncate">
                                {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}
                            </div>
                        </div>
                    </a>

                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1.5 rounded-lg focus:outline-none hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Nav Menu -->
                <nav class="px-3.5 py-4 space-y-5">

                    <!-- SECTION 1: DASHBOARD UTAMA -->
                    <div class="space-y-1">
                        <div class="px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            Dashboard Utama
                        </div>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Dashboard Admin</span>
                        </a>
                    </div>

                    <!-- SECTION 2: PROFIL KELURAHAN -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.beranda.identitas_sambutan') || request()->routeIs('admin.beranda.sotk') || request()->routeIs('admin.beranda.kemitraan') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition">
                            <span>Profil Kelurahan</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 mt-1">
                            <a href="{{ route('admin.beranda.identitas_sambutan') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.identitas_sambutan') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.identitas_sambutan') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0l-3 3m3-3l3 3M9 7h6"></path></svg>
                                <span class="truncate">Identitas & Sambutan</span>
                            </a>

                            <a href="{{ route('admin.beranda.sotk') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.sotk') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.sotk') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="truncate">Struktur Organisasi (SOTK)</span>
                            </a>

                            <a href="{{ route('admin.beranda.kemitraan') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.kemitraan') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.kemitraan') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="truncate">Kemitraan Instansi</span>
                            </a>
                        </div>
                    </div>

                    <!-- SECTION 3: LAYANAN PUBLIK -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.jenis-layanan.*') || request()->routeIs('admin.layanan-publik.*') || request()->routeIs('admin.documents.*') || request()->routeIs('admin.beranda.maklumat') || request()->routeIs('admin.beranda.transparansi') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition">
                            <span>Layanan Publik</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 mt-1">
                            <a href="{{ route('admin.beranda.maklumat') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.maklumat') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.maklumat') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="truncate">Maklumat Pelayanan</span>
                            </a>

                            <a href="{{ route('admin.jenis-layanan.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.jenis-layanan.*') || request()->routeIs('admin.layanan-publik.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.jenis-layanan.*') || request()->routeIs('admin.layanan-publik.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="truncate">Layanan & Standar SOP</span>
                            </a>
                            
                            <a href="{{ route('admin.beranda.transparansi') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.transparansi') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.transparansi') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="truncate">Transparansi & APBD</span>
                            </a>

                            <a href="{{ route('admin.documents.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.documents.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.documents.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="truncate">Dokumen Publik (PDF)</span>
                            </a>
                        </div>
                    </div>

                    <!-- SECTION 4: KONTEN & PUBLIKASI -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.berita.*') || request()->routeIs('admin.pengumuman.*') || request()->routeIs('admin.galeri.*') || request()->routeIs('admin.pages.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition">
                            <span>Konten & Publikasi</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 mt-1">
                            <a href="{{ route('admin.berita.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.berita.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.berita.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                <span>Berita & Artikel</span>
                            </a>

                            <a href="{{ route('admin.pengumuman.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.pengumuman.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.pengumuman.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                <span>Pengumuman & Marquee</span>
                            </a>

                            <a href="{{ route('admin.galeri.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.galeri.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.galeri.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Galeri Foto & Video</span>
                            </a>

                            <a href="{{ route('admin.pages.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.pages.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.pages.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <span class="truncate">Halaman Dinamis</span>
                            </a>
                        </div>
                    </div>

                    <!-- SECTION 5: TAMPILAN WEBSITE -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.beranda.banner') || request()->routeIs('admin.navigation.*') || request()->routeIs('admin.beranda.footer') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition">
                            <span>Tampilan Website</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 mt-1">
                            <a href="{{ route('admin.beranda.banner') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.banner') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.banner') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="truncate">Hero Banner & Slider</span>
                            </a>

                            <a href="{{ route('admin.navigation.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.navigation.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.navigation.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                                <span>Navigasi Menu</span>
                            </a>

                            <a href="{{ route('admin.beranda.footer') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.footer') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.footer') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                <span class="truncate">Footer & Media Sosial</span>
                            </a>
                        </div>
                    </div>

                    <!-- SECTION 6: INFORMASI & KONTAK -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.beranda.statistik') || request()->routeIs('admin.beranda.kontak') || request()->routeIs('admin.beranda.lokasi') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition">
                            <span>Informasi & Kontak</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 mt-1">
                            <a href="{{ route('admin.beranda.statistik') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.statistik') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.statistik') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <span class="truncate">Statistik & Demografi</span>
                            </a>

                            <a href="{{ route('admin.beranda.kontak') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.kontak') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.kontak') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span class="truncate">Kontak & Jam Kerja</span>
                            </a>
                            
                            <a href="{{ route('admin.beranda.lokasi') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.beranda.lokasi') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.beranda.lokasi') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate">Alamat & Peta Lokasi</span>
                            </a>
                        </div>
                    </div>

                    <!-- SECTION 7: PENGATURAN SISTEM -->
                    <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.kategori.*') || request()->routeIs('admin.media.*') || request()->routeIs('admin.activity-log.*') || request()->routeIs('admin.operator.*') || request()->routeIs('admin.pengaturan.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition">
                            <span>Pengaturan Sistem</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-collapse class="space-y-1 mt-1">
                            <a href="{{ route('admin.operator.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.operator.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.operator.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span class="truncate">Operator & Hak Akses</span>
                            </a>

                            <a href="{{ route('admin.activity-log.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.activity-log.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.activity-log.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="truncate">Log Aktivitas Sistem</span>
                            </a>

                            <a href="{{ route('admin.kategori.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.kategori.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.kategori.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <span>Master Kategori</span>
                            </a>

                            <a href="{{ route('admin.media.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.media.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.media.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                <span>File Manager & Media</span>
                            </a>

                            <a href="{{ route('admin.pengaturan.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs transition {{ request()->routeIs('admin.pengaturan.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 {{ request()->routeIs('admin.pengaturan.*') ? 'text-white' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate">Konfigurasi Sistem & SEO</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Footer Profile & Logout -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/80 shrink-0">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs sm:text-sm shadow-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 truncate">
                                {{ Auth::user()->name ?? 'Admin Kelurahan' }}
                            </div>
                            <div class="text-[10px] text-slate-500 truncate">
                                {{ Auth::user()->email ?? 'admin@kelurahan.go.id' }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" 
                                title="Keluar Akun"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition focus:outline-none">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- 2. MAIN CONTENT AREA                       -->
        <!-- ========================================== -->
        <div class="admin-main-wrapper lg:pl-72 flex flex-col min-h-screen">
            
            <!-- Topbar Sticky -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-20 shadow-xs">
                <div class="px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-3 sm:gap-4">
                    
                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="text-sm sm:text-base md:text-lg font-bold text-slate-900 tracking-tight truncate">
                                @yield('header-title', 'Dashboard Panel Admin')
                            </h1>
                            <p class="text-[11px] sm:text-xs text-slate-500 truncate hidden xs:block sm:block">
                                @yield('header-subtitle', 'Sistem Informasi Manajemen Pelayanan Kelurahan Patokan')
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 sm:gap-4 shrink-0">
                        <!-- Jam & Tanggal Realtime -->
                        <div class="hidden md:flex flex-col items-end text-right border-r border-slate-200 pr-3.5">
                            <div class="text-xs font-bold text-slate-900 whitespace-nowrap" id="live-date">
                                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                            </div>
                            <div class="text-[11px] font-mono text-slate-500 font-semibold" id="live-clock">
                                {{ \Carbon\Carbon::now()->format('H:i:s') }} WIB
                            </div>
                        </div>

                        <!-- Tombol Pratinjau Website Publik -->
                        <a href="{{ route('home') }}" 
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition shadow-xs whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            <span class="hidden sm:inline">Lihat Website</span>
                            <span class="sm:hidden">Web</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Alert & Flash Messages -->
            <main class="flex-1 p-3.5 sm:p-6 lg:p-8">
                @if(session('status') || session('success'))
                    <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-900 p-3.5 sm:p-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-semibold">{{ session('status') ?? session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-900 p-3.5 sm:p-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-900 p-3.5 sm:p-4 rounded-2xl shadow-xs">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-bold">Terjadi kesalahan pada data yang dikirim:</span>
                        </div>
                        <ul class="list-disc list-inside text-[11px] sm:text-xs font-medium space-y-1 ml-10 sm:ml-11">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer Kelurahan -->
            <footer class="bg-white border-t border-slate-200 px-4 sm:px-6 py-3.5 text-center text-[11px] sm:text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
                <div>
                    &copy; {{ date('Y') }} <strong>{{ $villageProfile['village_name'] ?? 'Pemerintah Kelurahan Patokan' }}</strong>
                </div>
                <div class="text-[10px] sm:text-[11px] text-slate-400">
                    {{ $systemSettings['app_name'] ?? 'SIMPEL KELURAHAN' }} &bull; CMS Engine
                </div>
            </footer>

        </div>
    </div>

    <!-- Global Image Cropper Modal -->
    <div x-data="imageCropper()" 
         @open-cropper.window="openCropper($event.detail, $event.target)"
         x-show="isOpen" 
         x-cloak 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         role="dialog" aria-modal="true">
        
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="isOpen" @click="cancel()" class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity"></div>

            <div x-show="isOpen" class="relative inline-block bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-3xl my-8">
                <div class="bg-gradient-to-r from-emerald-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                    <h3 class="text-base font-bold">Sesuaikan Gambar (Crop)</h3>
                    <button type="button" @click="cancel()" class="text-emerald-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="w-full bg-slate-100 rounded-xl overflow-hidden flex items-center justify-center relative" style="height: 50vh; max-height: 500px;">
                        <img x-ref="image" src="" alt="Source Image" class="max-w-full max-h-full block">
                    </div>
                    <p class="text-[11px] text-slate-500 mt-3 text-center">Geser dan atur perbesaran gambar untuk mendapatkan area yang sesuai.</p>
                </div>
                <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                    <button type="button" @click="cancel()" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                    <button type="button" @click="crop()" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl shadow">Terapkan Potongan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts: Live Clock & File Validator -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElem = document.getElementById('live-clock');
            if (clockElem) {
                clockElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateClock, 1000);

        function validateFileInput(input, maxMb = 3) {
            const file = input.files[0];
            const container = input.closest('div');
            if (!container) return;

            let feedback = container.querySelector('.js-file-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'js-file-feedback mt-1.5 text-xs font-semibold';
                container.appendChild(feedback);
            }

            if (!file) {
                feedback.innerHTML = '';
                input.classList.remove('border-rose-500', 'bg-rose-50', 'border-emerald-500', 'bg-emerald-50');
                return;
            }

            const fileName = file.name;
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const fileExt = fileName.split('.').pop().toLowerCase();
            const validExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

            if (!validExts.includes(fileExt)) {
                input.value = '';
                input.classList.add('border-rose-500', 'bg-rose-50');
                feedback.className = 'js-file-feedback mt-1.5 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-2';
                feedback.innerHTML = `Format <strong>.${fileExt}</strong> tidak didukung! Gunakan gambar JPG, PNG, WEBP atau PDF.`;
                return false;
            }

            if (file.size > maxMb * 1024 * 1024) {
                input.value = '';
                input.classList.add('border-rose-500', 'bg-rose-50');
                feedback.className = 'js-file-feedback mt-1.5 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-2';
                feedback.innerHTML = `Ukuran file terlalu besar (<strong>${fileSizeMB} MB</strong>). Maksimal ${maxMb} MB.`;
                return false;
            }

            input.classList.remove('border-rose-500', 'bg-rose-50');
            input.classList.add('border-emerald-500', 'bg-emerald-50/50');
            feedback.className = 'js-file-feedback mt-1.5 p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-semibold flex items-center gap-2';
            feedback.innerHTML = `File siap diunggah (${fileSizeMB} MB): <strong>${fileName}</strong>`;
            return true;
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('imageCropper', () => ({
                isOpen: false,
                cropper: null,
                file: null,
                onCropCallback: null,

                openCropper(detail, target) {
                    this.file = detail.file;
                    
                    if (!this.file || !this.file.type.startsWith('image/')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format Tidak Didukung',
                            text: 'Mohon pilih file gambar (JPG, PNG, WEBP).',
                            confirmButtonColor: '#047857'
                        }).then(() => {
                            if (target && target.tagName === 'INPUT' && target.type === 'file') {
                                target.value = '';
                                target.click();
                            }
                        });
                        return;
                    }

                    this.onCropCallback = detail.onCrop;
                    const aspectRatio = detail.aspectRatio || NaN;
                    
                    const url = URL.createObjectURL(this.file);
                    this.$refs.image.src = url;
                    
                    this.isOpen = true;
                    
                    this.$nextTick(() => {
                        if(this.cropper) {
                            this.cropper.destroy();
                        }
                        this.cropper = new Cropper(this.$refs.image, {
                            aspectRatio: aspectRatio,
                            viewMode: 2,
                            autoCropArea: 1,
                            background: false,
                        });
                    });
                },

                cancel() {
                    this.isOpen = false;
                    if(this.cropper) {
                        this.cropper.destroy();
                        this.cropper = null;
                    }
                    this.$refs.image.src = '';
                },

                crop() {
                    if(!this.cropper) return;
                    
                    this.cropper.getCroppedCanvas({
                        maxWidth: 1920,
                        maxHeight: 1920,
                    }).toBlob((blob) => {
                        const croppedUrl = URL.createObjectURL(blob);
                        if(this.onCropCallback) {
                            this.onCropCallback(blob, croppedUrl);
                        }
                        this.cancel();
                    }, this.file.type || 'image/jpeg', 0.85);
                }
            }));
        });
    </script>
</body>
</html>