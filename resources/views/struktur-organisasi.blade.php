@extends('layouts.app')

@section('title', 'Struktur Organisasi (SOTK) - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full pt-6 pb-16 bg-slate-50/70 border-t border-slate-200 relative min-h-screen" x-data="{ viewMode: 'bagan' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Single Sleek Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-400 mb-1.5">
                    <a href="{{ route('home') }}" class="hover:text-slate-900 transition">Beranda</a>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <span class="text-slate-600 font-semibold">Profil Kelurahan</span>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <span class="text-slate-900 font-bold">Struktur Organisasi</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Struktur Organisasi (SOTK)
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Bagan alur kelembagaan, hierarki aparatur resmi, dan tata kerja Pemerintah Kelurahan Semampir.
                </p>
            </div>

            {{-- Controls / Switcher Mode --}}
            <div class="flex items-center gap-3 shrink-0">
                <div class="inline-flex p-1 bg-slate-200/80 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">
                    <button type="button" @click="viewMode = 'bagan'" 
                            :class="viewMode === 'bagan' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-sitemap text-xs"></i>
                        <span>Bagan Alur</span>
                    </button>
                    <button type="button" @click="viewMode = 'daftar'" 
                            :class="viewMode === 'daftar' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-th-list text-xs"></i>
                        <span>Daftar Pegawai</span>
                    </button>
                </div>

                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-xs transition-all shadow-xs">
                    <i class="fas fa-arrow-left text-slate-400"></i>
                    <span>Beranda</span>
                </a>
            </div>
        </div>

        {{-- Main Container Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200 space-y-8">
            
            {{-- MODE 1: BAGAN HIRARKI (ORG CHART) --}}
            <div x-show="viewMode === 'bagan'" x-transition class="space-y-6">
                <div class="text-center max-w-xl mx-auto space-y-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold uppercase tracking-wider">
                        Bagan Tata Kerja Resmi
                    </span>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                        Bagan Struktur Pemerintahan Kelurahan Semampir
                    </h2>
                </div>

                {{-- Org Chart Canvas --}}
                <div class="overflow-x-auto pb-4 custom-scrollbar">
                    <div class="min-w-[880px] flex flex-col items-center py-4">

                        {{-- 1. TOP NODE: LURAH --}}
                        <div class="flex flex-col items-center z-10 relative">
                            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-slate-400 hover:shadow-sm flex flex-col items-center text-center w-60 transition-all duration-200 group">
                                <div class="w-20 h-20 rounded-full border-2 border-slate-900 p-0.5 mb-2.5 bg-slate-50 shrink-0 shadow-2xs group-hover:scale-105 transition-transform duration-200">
                                    <img src="{{ !empty($villageProfile['head_photo']) ? (str_starts_with($villageProfile['head_photo'], 'http') || str_starts_with($villageProfile['head_photo'], 'data:image') ? $villageProfile['head_photo'] : asset('storage/' . ltrim($villageProfile['head_photo'], '/'))) : asset('images/sotk/lurah.png') }}" 
                                         alt="Lurah Semampir" 
                                         onerror="this.onerror=null; this.src='{{ asset('images/sotk/lurah.png') }}';"
                                         class="w-full h-full object-cover rounded-full">
                                </div>
                                <h3 class="font-bold text-xs text-slate-900 leading-snug">
                                    {{ $villageProfile['head_name'] ?? 'H. Ahmad Fauzi, S.STP, M.Si' }}
                                </h3>
                                <div class="mt-2">
                                    <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-3 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                        LURAH SEMAMPIR
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1.5 font-medium">Kepala Pemerintahan Kelurahan</p>
                            </div>

                            {{-- Vertical connector down --}}
                            <div class="w-0.5 h-7 bg-slate-300"></div>
                        </div>

                        {{-- 2. BOTTOM NODES (4 Columns: Sekel + 3 Kasis) --}}
                        <div class="w-full max-w-4xl relative">
                            {{-- Horizontal Branch Line --}}
                            <div class="w-full h-0.5 bg-slate-300 absolute top-0 left-0 right-0"></div>

                            {{-- 4 Columns Grid --}}
                            <div class="grid grid-cols-4 gap-4 sm:gap-5 pt-6">

                                {{-- Node 1: Sekel --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-0.5 h-6 bg-slate-300 absolute -top-6 left-1/2 -translate-x-1/2"></div>
                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-slate-400 shadow-2xs hover:shadow-sm flex flex-col items-center text-center w-full transition-all duration-200 group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ !empty($villageProfile['sekel_photo']) ? asset('storage/' . $villageProfile['sekel_photo']) : asset('images/sotk/sekel.png') }}" 
                                                 alt="Sekretaris Kelurahan" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['sekel_name'] ?? 'Sekretaris Kelurahan' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1.5 font-medium">Sekretariat Kelurahan</span>
                                    </div>
                                </div>

                                {{-- Node 2: Kasi Pem --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-0.5 h-6 bg-slate-300 absolute -top-6 left-1/2 -translate-x-1/2"></div>
                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-slate-400 shadow-2xs hover:shadow-sm flex flex-col items-center text-center w-full transition-all duration-200 group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ !empty($villageProfile['kasi_pem_photo']) ? asset('storage/' . $villageProfile['kasi_pem_photo']) : asset('images/sotk/kasi_pem.png') }}" 
                                                 alt="Kasi Pemerintahan" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_pem_name'] ?? 'Kasi Pemerintahan' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1.5 font-medium">Seksi Pemerintahan</span>
                                    </div>
                                </div>

                                {{-- Node 3: Kasi Kesra --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-0.5 h-6 bg-slate-300 absolute -top-6 left-1/2 -translate-x-1/2"></div>
                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-slate-400 shadow-2xs hover:shadow-sm flex flex-col items-center text-center w-full transition-all duration-200 group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ !empty($villageProfile['kasi_kesra_photo']) ? asset('storage/' . $villageProfile['kasi_kesra_photo']) : asset('images/sotk/kasi_kesra.png') }}" 
                                                 alt="Kasi Kesra" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_kesra_name'] ?? 'Kasi Kesra' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1.5 font-medium">Seksi Sosial & Kesra</span>
                                    </div>
                                </div>

                                {{-- Node 4: Kasi Ekbang --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-0.5 h-6 bg-slate-300 absolute -top-6 left-1/2 -translate-x-1/2"></div>
                                    <div class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-slate-400 shadow-2xs hover:shadow-sm flex flex-col items-center text-center w-full transition-all duration-200 group">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ !empty($villageProfile['kasi_ekbang_photo']) ? asset('storage/' . $villageProfile['kasi_ekbang_photo']) : asset('images/sotk/kasi_ekbang.png') }}" 
                                                 alt="Kasi Ekbang" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_ekbang_name'] ?? 'Kasi Ekbang' }}
                                        </h5>
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1.5 font-medium">Seksi Perekonomian</span>
                                    </div>
                                </div>

                            </div>

                            {{-- 3. ADDITIONAL MEMBERS TIER (STAF & JABATAN FUNGSIONAL) --}}
                            @if(!empty($villageProfile['sotk_members']) && count($villageProfile['sotk_members']) > 0)
                                <div class="pt-7 relative">
                                    {{-- Connector line --}}
                                    <div class="w-0.5 h-7 bg-slate-300 absolute top-0 left-1/2 -translate-x-1/2"></div>
                                    
                                    {{-- Section Badge --}}
                                    <div class="text-center mb-5 pt-1">
                                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold uppercase px-3.5 py-1 rounded-full tracking-wider shadow-2xs">
                                            <i class="fas fa-users text-slate-500"></i>
                                            <span>STAF PELAYANAN & JABATAN FUNGSIONAL ({{ count($villageProfile['sotk_members']) }})</span>
                                        </span>
                                    </div>

                                    {{-- Staff Cards Centered --}}
                                    <div class="flex flex-wrap justify-center gap-4 sm:gap-6">
                                        @foreach($villageProfile['sotk_members'] as $m)
                                            <div class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-slate-400 shadow-2xs hover:shadow-sm flex flex-col items-center text-center w-52 transition-all duration-200 group">
                                                <div class="w-16 h-16 rounded-full border-2 border-slate-700 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                                    @if(!empty($m['photo']))
                                                        <img src="{{ asset('storage/' . $m['photo']) }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-extrabold text-base uppercase">
                                                            {{ strtoupper(substr($m['name'] ?? 'S', 0, 2)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                                    {{ $m['name'] }}
                                                </h5>
                                                <div class="mt-1.5">
                                                    <span class="bg-slate-900 text-white font-bold text-[9px] uppercase px-2.5 py-0.5 rounded-full tracking-wider inline-block">
                                                        {{ $m['position'] ?? 'STAF' }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] text-slate-500 mt-1 font-medium">Staf Pendukung</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            {{-- MODE 2: DAFTAR PEGAWAI & APARATUR (DIRECTORY LIST) --}}
            <div x-show="viewMode === 'daftar'" x-transition class="space-y-4" style="display: none;">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Aparatur & Staf Kelurahan</h3>
                        <p class="text-xs text-slate-500">Daftar lengkap pejabat struktural inti dan staf pelaksana Kelurahan Semampir.</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full w-fit">
                        Total {{ 5 + count($villageProfile['sotk_members'] ?? []) }} Aparatur
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- 1. Lurah --}}
                    <div class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-slate-300 flex items-start gap-3.5 transition shadow-2xs">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-900 overflow-hidden bg-white shrink-0">
                            <img src="{{ !empty($villageProfile['head_photo']) ? (str_starts_with($villageProfile['head_photo'], 'http') || str_starts_with($villageProfile['head_photo'], 'data:image') ? $villageProfile['head_photo'] : asset('storage/' . ltrim($villageProfile['head_photo'], '/'))) : asset('images/sotk/lurah.png') }}" 
                                 onerror="this.onerror=null; this.src='{{ asset('images/sotk/lurah.png') }}';"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Pimpinan</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate">{{ $villageProfile['head_name'] ?? 'Lurah' }}</h4>
                            <p class="text-[11px] font-semibold text-slate-600">Lurah Semampir</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['lurah_tupoksi'] ?? 'Pimpinan utama pemerintahan kelurahan.' }}</p>
                        </div>
                    </div>

                    {{-- 2. Sekel --}}
                    <div class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-slate-300 flex items-start gap-3.5 transition shadow-2xs">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0">
                            <img src="{{ !empty($villageProfile['sekel_photo']) ? asset('storage/' . $villageProfile['sekel_photo']) : asset('images/sotk/sekel.png') }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Sekretariat</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['sekel_name'] ?? 'Sekretaris' }}</h4>
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['sekel_role'] ?? 'Sekretaris Kelurahan' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['sekel_tupoksi'] ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- 3. Kasi Pem --}}
                    <div class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-slate-300 flex items-start gap-3.5 transition shadow-2xs">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0">
                            <img src="{{ !empty($villageProfile['kasi_pem_photo']) ? asset('storage/' . $villageProfile['kasi_pem_photo']) : asset('images/sotk/kasi_pem.png') }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Pemerintahan</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_pem_name'] ?? 'Kasi Pem' }}</h4>
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['kasi_pem_role'] ?? 'Kasi Pemerintahan & Trantib' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['kasi_pem_tupoksi'] ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- 4. Kasi Kesra --}}
                    <div class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-slate-300 flex items-start gap-3.5 transition shadow-2xs">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0">
                            <img src="{{ !empty($villageProfile['kasi_kesra_photo']) ? asset('storage/' . $villageProfile['kasi_kesra_photo']) : asset('images/sotk/kasi_kesra.png') }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Sosial & Kesra</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_kesra_name'] ?? 'Kasi Kesra' }}</h4>
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['kasi_kesra_role'] ?? 'Kasi Pelayanan & Kesra' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['kasi_kesra_tupoksi'] ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- 5. Kasi Ekbang --}}
                    <div class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-slate-300 flex items-start gap-3.5 transition shadow-2xs">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0">
                            <img src="{{ !empty($villageProfile['kasi_ekbang_photo']) ? asset('storage/' . $villageProfile['kasi_ekbang_photo']) : asset('images/sotk/kasi_ekbang.png') }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Perekonomian</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_ekbang_name'] ?? 'Kasi Ekbang' }}</h4>
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['kasi_ekbang_role'] ?? 'Kasi Pemberdayaan & Ekbang' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['kasi_ekbang_tupoksi'] ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Anggota & Staf Tambahan --}}
                    @foreach($villageProfile['sotk_members'] ?? [] as $m)
                        <div class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-slate-300 flex items-start gap-3.5 transition shadow-2xs">
                            <div class="w-14 h-14 rounded-full border-2 border-slate-700 overflow-hidden bg-white shrink-0">
                                @if(!empty($m['photo']))
                                    <img src="{{ asset('storage/' . $m['photo']) }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-extrabold text-sm uppercase">
                                        {{ strtoupper(substr($m['name'] ?? 'S', 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-700">Staf Pelaksana</span>
                                <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $m['name'] }}</h4>
                                <p class="text-[11px] font-semibold text-slate-700">{{ $m['position'] ?? 'Staf' }}</p>
                                <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $m['tupoksi'] ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>



        </div>

    </div>
</section>

@endsection
