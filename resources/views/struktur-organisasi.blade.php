@extends('layouts.app')

@section('title', 'Struktur Organisasi (SOTK) - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $headPhotoUrl = !empty($villageProfile['head_photo']) ? (str_starts_with($villageProfile['head_photo'], 'http') || str_starts_with($villageProfile['head_photo'], 'data:image') ? $villageProfile['head_photo'] : asset('storage/' . ltrim($villageProfile['head_photo'], '/')) . '?v=' . time()) : asset('images/sotk/lurah.png');
    $sekelPhotoUrl = !empty($villageProfile['sekel_photo']) ? asset('storage/' . $villageProfile['sekel_photo']) : asset('images/sotk/sekel.png');
    $kasiPemPhotoUrl = !empty($villageProfile['kasi_pem_photo']) ? asset('storage/' . $villageProfile['kasi_pem_photo']) : asset('images/sotk/kasi_pem.png');
    $kasiKesraPhotoUrl = !empty($villageProfile['kasi_kesra_photo']) ? asset('storage/' . $villageProfile['kasi_kesra_photo']) : asset('images/sotk/kasi_kesra.png');
    $kasiEkbangPhotoUrl = !empty($villageProfile['kasi_ekbang_photo']) ? asset('storage/' . $villageProfile['kasi_ekbang_photo']) : asset('images/sotk/kasi_ekbang.png');
@endphp

<section class="w-full pt-6 pb-16 bg-slate-50/70 border-t border-slate-200 relative min-h-screen" 
         x-data="{ 
             viewMode: 'bagan',
             showTupoksiModal: false,
             selectedOfficer: {
                 name: '',
                 nip: '',
                 role: '',
                 unit: '',
                 photo: '',
                 initial: '',
                 tupoksi: ''
             },
             openTupoksiModal(data) {
                 this.selectedOfficer = data;
                 this.showTupoksiModal = true;
             }
         }">
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
                <div class="text-center max-w-xl mx-auto space-y-1.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold uppercase tracking-wider">
                        Bagan Tata Kerja Resmi
                    </span>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                        Bagan Struktur Pemerintahan Kelurahan Semampir
                    </h2>
                    <p class="text-xs text-slate-500 flex items-center justify-center gap-1.5">
                        <i class="fas fa-mouse-pointer text-blue-500"></i>
                        <span>Klik pada kartu pejabat untuk melihat rincian <strong>Tugas Pokok & Fungsi (TUPOKSI)</strong>.</span>
                    </p>
                </div>

                {{-- Org Chart Canvas --}}
                <div class="overflow-x-auto pb-4 custom-scrollbar">
                    <div class="min-w-[880px] flex flex-col items-center py-4">

                        {{-- 1. TOP NODE: LURAH --}}
                        <div class="flex flex-col items-center z-10 relative">
                            <div @click="openTupoksiModal({
                                    name: {{ json_encode($villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.') }},
                                    nip: {{ json_encode($villageProfile['head_nip'] ?? '') }},
                                    role: 'LURAH SEMAMPIR',
                                    unit: 'Kepala Pemerintahan Kelurahan',
                                    photo: {{ json_encode($headPhotoUrl) }},
                                    initial: 'LH',
                                    tupoksi: {{ json_encode($villageProfile['lurah_tupoksi'] ?? 'Penyelenggara utama urusan pemerintahan, ketertiban umum, pelayanan publik, dan pembinaan wilayah.') }}
                                 })"
                                 class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-blue-500 hover:shadow-md flex flex-col items-center text-center w-64 transition-all duration-200 group cursor-pointer">
                                <div class="w-20 h-20 rounded-full border-2 border-slate-900 p-0.5 mb-2.5 bg-slate-50 shrink-0 shadow-2xs group-hover:scale-105 transition-transform duration-200">
                                    <img src="{{ $headPhotoUrl }}" 
                                         alt="Lurah Semampir" 
                                         onerror="this.onerror=null; this.src='{{ asset('images/sotk/lurah.png') }}';"
                                         class="w-full h-full object-cover rounded-full">
                                </div>
                                <h3 class="font-bold text-xs text-slate-900 leading-snug">
                                    {{ $villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.' }}
                                </h3>
                                @if(!empty($villageProfile['head_nip']))
                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['head_nip'] }}</p>
                                @endif
                                <div class="mt-2">
                                    <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-3 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                        LURAH SEMAMPIR
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1 font-medium">Kepala Pemerintahan Kelurahan</p>
                                <span class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200/70 transition shadow-2xs">
                                    <i class="fas fa-clipboard-list text-[9px]"></i>
                                    <span>Lihat TUPOKSI</span>
                                </span>
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
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['sekel_name'] ?? 'Sekretaris Kelurahan') }},
                                            nip: {{ json_encode($villageProfile['sekel_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN')) }},
                                            unit: 'Sekretariat Kelurahan',
                                            photo: {{ json_encode($sekelPhotoUrl) }},
                                            initial: 'SK',
                                            tupoksi: {{ json_encode($villageProfile['sekel_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $sekelPhotoUrl }}" 
                                                 alt="Sekretaris Kelurahan" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['sekel_name'] ?? 'Sekretaris Kelurahan' }}
                                        </h5>
                                        @if(!empty($villageProfile['sekel_nip']))
                                            <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['sekel_nip'] }}</p>
                                        @endif
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1 font-medium">Sekretariat Kelurahan</span>
                                        <span class="mt-2.5 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200/70 transition shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[9px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Node 2: Kasi Pem --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-0.5 h-6 bg-slate-300 absolute -top-6 left-1/2 -translate-x-1/2"></div>
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['kasi_pem_name'] ?? 'Kasi Pemerintahan') }},
                                            nip: {{ json_encode($villageProfile['kasi_pem_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB')) }},
                                            unit: 'Seksi Pemerintahan',
                                            photo: {{ json_encode($kasiPemPhotoUrl) }},
                                            initial: 'KP',
                                            tupoksi: {{ json_encode($villageProfile['kasi_pem_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $kasiPemPhotoUrl }}" 
                                                 alt="Kasi Pemerintahan" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_pem_name'] ?? 'Kasi Pemerintahan' }}
                                        </h5>
                                        @if(!empty($villageProfile['kasi_pem_nip']))
                                            <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_pem_nip'] }}</p>
                                        @endif
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1 font-medium">Seksi Pemerintahan</span>
                                        <span class="mt-2.5 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200/70 transition shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[9px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Node 3: Kasi Kesra --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-0.5 h-6 bg-slate-300 absolute -top-6 left-1/2 -translate-x-1/2"></div>
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['kasi_kesra_name'] ?? 'Kasi Kesra') }},
                                            nip: {{ json_encode($villageProfile['kasi_kesra_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA')) }},
                                            unit: 'Seksi Sosial & Kesra',
                                            photo: {{ json_encode($kasiKesraPhotoUrl) }},
                                            initial: 'KK',
                                            tupoksi: {{ json_encode($villageProfile['kasi_kesra_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $kasiKesraPhotoUrl }}" 
                                                 alt="Kasi Kesra" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_kesra_name'] ?? 'Kasi Kesra' }}
                                        </h5>
                                        @if(!empty($villageProfile['kasi_kesra_nip']))
                                            <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_kesra_nip'] }}</p>
                                        @endif
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1 font-medium">Seksi Sosial & Kesra</span>
                                        <span class="mt-2.5 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200/70 transition shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[9px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Node 4: Kasi Ekbang --}}
                                <div class="flex flex-col items-center relative">
                                    <div class="w-0.5 h-6 bg-slate-300 absolute -top-6 left-1/2 -translate-x-1/2"></div>
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['kasi_ekbang_name'] ?? 'Kasi Ekbang') }},
                                            nip: {{ json_encode($villageProfile['kasi_ekbang_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG')) }},
                                            unit: 'Seksi Perekonomian',
                                            photo: {{ json_encode($kasiEkbangPhotoUrl) }},
                                            initial: 'KE',
                                            tupoksi: {{ json_encode($villageProfile['kasi_ekbang_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $kasiEkbangPhotoUrl }}" 
                                                 alt="Kasi Ekbang" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_ekbang_name'] ?? 'Kasi Ekbang' }}
                                        </h5>
                                        @if(!empty($villageProfile['kasi_ekbang_nip']))
                                            <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_ekbang_nip'] }}</p>
                                        @endif
                                        <div class="mt-2">
                                            <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                {{ strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG') }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 mt-1 font-medium">Seksi Perekonomian</span>
                                        <span class="mt-2.5 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200/70 transition shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[9px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
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
                                            @php
                                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                                            @endphp
                                            <div @click="openTupoksiModal({
                                                    name: {{ json_encode($m['name'] ?? 'Staf') }},
                                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                                    role: {{ json_encode(strtoupper($m['position'] ?? 'STAF')) }},
                                                    unit: 'Staf Pelaksana Kelurahan',
                                                    photo: {{ json_encode($mPhoto) }},
                                                    initial: {{ json_encode($mInitial) }},
                                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                                 })"
                                                 class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-52 transition-all duration-200 group cursor-pointer">
                                                <div class="w-16 h-16 rounded-full border-2 border-slate-700 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200">
                                                    @if(!empty($m['photo']))
                                                        <img src="{{ asset('storage/' . $m['photo']) }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-extrabold text-base uppercase">
                                                            {{ $mInitial }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                                    {{ $m['name'] }}
                                                </h5>
                                                @if(!empty($m['nip']))
                                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $m['nip'] }}</p>
                                                @endif
                                                <div class="mt-1.5">
                                                    <span class="bg-slate-900 text-white font-bold text-[9px] uppercase px-2.5 py-0.5 rounded-full tracking-wider inline-block">
                                                        {{ $m['position'] ?? 'STAF' }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] text-slate-500 mt-1 font-medium">Staf Pendukung</span>
                                                <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200/70 transition shadow-2xs">
                                                    <i class="fas fa-clipboard-list text-[9px]"></i>
                                                    <span>Lihat TUPOKSI</span>
                                                </span>
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
                    <div @click="openTupoksiModal({
                            name: {{ json_encode($villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.') }},
                            nip: {{ json_encode($villageProfile['head_nip'] ?? '') }},
                            role: 'LURAH SEMAMPIR',
                            unit: 'Kepala Pemerintahan Kelurahan',
                            photo: {{ json_encode($headPhotoUrl) }},
                            initial: 'LH',
                            tupoksi: {{ json_encode($villageProfile['lurah_tupoksi'] ?? 'Penyelenggara utama urusan pemerintahan, ketertiban umum, pelayanan publik, dan pembinaan wilayah.') }}
                         })"
                         class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-900 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ $headPhotoUrl }}" 
                                 onerror="this.onerror=null; this.src='{{ asset('images/sotk/lurah.png') }}';"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Pimpinan</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate">{{ $villageProfile['head_name'] ?? 'Lurah' }}</h4>
                            @if(!empty($villageProfile['head_nip']))
                                <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $villageProfile['head_nip'] }}</p>
                            @endif
                            <p class="text-[11px] font-semibold text-slate-600">Lurah Semampir</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['lurah_tupoksi'] ?? 'Pimpinan utama pemerintahan kelurahan.' }}</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 group-hover:text-blue-700">
                                <span>Lihat TUPOKSI</span>
                                <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
                            </span>
                        </div>
                    </div>

                    {{-- 2. Sekel --}}
                    <div @click="openTupoksiModal({
                            name: {{ json_encode($villageProfile['sekel_name'] ?? 'Sekretaris Kelurahan') }},
                            nip: {{ json_encode($villageProfile['sekel_nip'] ?? '') }},
                            role: {{ json_encode(strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN')) }},
                            unit: 'Sekretariat Kelurahan',
                            photo: {{ json_encode($sekelPhotoUrl) }},
                            initial: 'SK',
                            tupoksi: {{ json_encode($villageProfile['sekel_tupoksi'] ?? '') }}
                         })"
                         class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ $sekelPhotoUrl }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Sekretariat</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['sekel_name'] ?? 'Sekretaris' }}</h4>
                            @if(!empty($villageProfile['sekel_nip']))
                                <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $villageProfile['sekel_nip'] }}</p>
                            @endif
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['sekel_role'] ?? 'Sekretaris Kelurahan' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['sekel_tupoksi'] ?? '-' }}</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 group-hover:text-blue-700">
                                <span>Lihat TUPOKSI</span>
                                <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
                            </span>
                        </div>
                    </div>

                    {{-- 3. Kasi Pem --}}
                    <div @click="openTupoksiModal({
                            name: {{ json_encode($villageProfile['kasi_pem_name'] ?? 'Kasi Pemerintahan') }},
                            nip: {{ json_encode($villageProfile['kasi_pem_nip'] ?? '') }},
                            role: {{ json_encode(strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB')) }},
                            unit: 'Seksi Pemerintahan',
                            photo: {{ json_encode($kasiPemPhotoUrl) }},
                            initial: 'KP',
                            tupoksi: {{ json_encode($villageProfile['kasi_pem_tupoksi'] ?? '') }}
                         })"
                         class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ $kasiPemPhotoUrl }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Pemerintahan</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_pem_name'] ?? 'Kasi Pem' }}</h4>
                            @if(!empty($villageProfile['kasi_pem_nip']))
                                <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $villageProfile['kasi_pem_nip'] }}</p>
                            @endif
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['kasi_pem_role'] ?? 'Kasi Pemerintahan & Trantib' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['kasi_pem_tupoksi'] ?? '-' }}</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 group-hover:text-blue-700">
                                <span>Lihat TUPOKSI</span>
                                <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
                            </span>
                        </div>
                    </div>

                    {{-- 4. Kasi Kesra --}}
                    <div @click="openTupoksiModal({
                            name: {{ json_encode($villageProfile['kasi_kesra_name'] ?? 'Kasi Kesra') }},
                            nip: {{ json_encode($villageProfile['kasi_kesra_nip'] ?? '') }},
                            role: {{ json_encode(strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA')) }},
                            unit: 'Seksi Sosial & Kesra',
                            photo: {{ json_encode($kasiKesraPhotoUrl) }},
                            initial: 'KK',
                            tupoksi: {{ json_encode($villageProfile['kasi_kesra_tupoksi'] ?? '') }}
                         })"
                         class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ $kasiKesraPhotoUrl }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Sosial & Kesra</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_kesra_name'] ?? 'Kasi Kesra' }}</h4>
                            @if(!empty($villageProfile['kasi_kesra_nip']))
                                <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $villageProfile['kasi_kesra_nip'] }}</p>
                            @endif
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['kasi_kesra_role'] ?? 'Kasi Pelayanan & Kesra' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['kasi_kesra_tupoksi'] ?? '-' }}</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 group-hover:text-blue-700">
                                <span>Lihat TUPOKSI</span>
                                <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
                            </span>
                        </div>
                    </div>

                    {{-- 5. Kasi Ekbang --}}
                    <div @click="openTupoksiModal({
                            name: {{ json_encode($villageProfile['kasi_ekbang_name'] ?? 'Kasi Ekbang') }},
                            nip: {{ json_encode($villageProfile['kasi_ekbang_nip'] ?? '') }},
                            role: {{ json_encode(strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG')) }},
                            unit: 'Seksi Perekonomian',
                            photo: {{ json_encode($kasiEkbangPhotoUrl) }},
                            initial: 'KE',
                            tupoksi: {{ json_encode($villageProfile['kasi_ekbang_tupoksi'] ?? '') }}
                         })"
                         class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                        <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ $kasiEkbangPhotoUrl }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Perekonomian</span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_ekbang_name'] ?? 'Kasi Ekbang' }}</h4>
                            @if(!empty($villageProfile['kasi_ekbang_nip']))
                                <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $villageProfile['kasi_ekbang_nip'] }}</p>
                            @endif
                            <p class="text-[11px] font-semibold text-slate-600">{{ $villageProfile['kasi_ekbang_role'] ?? 'Kasi Pemberdayaan & Ekbang' }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['kasi_ekbang_tupoksi'] ?? '-' }}</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 group-hover:text-blue-700">
                                <span>Lihat TUPOKSI</span>
                                <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Anggota & Staf Tambahan --}}
                    @foreach($villageProfile['sotk_members'] ?? [] as $m)
                        @php
                            $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                            $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                        @endphp
                        <div @click="openTupoksiModal({
                                name: {{ json_encode($m['name'] ?? 'Staf') }},
                                nip: {{ json_encode($m['nip'] ?? '') }},
                                role: {{ json_encode(strtoupper($m['position'] ?? 'STAF')) }},
                                unit: 'Staf Pelaksana Kelurahan',
                                photo: {{ json_encode($mPhoto) }},
                                initial: {{ json_encode($mInitial) }},
                                tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                             })"
                             class="p-4 bg-slate-50/70 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                            <div class="w-14 h-14 rounded-full border-2 border-slate-700 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                                @if(!empty($m['photo']))
                                    <img src="{{ asset('storage/' . $m['photo']) }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-extrabold text-sm uppercase">
                                        {{ $mInitial }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-700">Staf Pelaksana</span>
                                <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $m['name'] }}</h4>
                                @if(!empty($m['nip']))
                                    <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $m['nip'] }}</p>
                                @endif
                                <p class="text-[11px] font-semibold text-slate-700">{{ $m['position'] ?? 'Staf' }}</p>
                                <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $m['tupoksi'] ?? '-' }}</p>
                                <span class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 group-hover:text-blue-700">
                                    <span>Lihat TUPOKSI</span>
                                    <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    {{-- MODAL INTERAKTIF: RINCIAN TUGAS POKOK & FUNGSI (TUPOKSI) --}}
    <div x-show="showTupoksiModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs" 
         style="display: none;"
         @keydown.escape.window="showTupoksiModal = false">
        
        {{-- Backdrop Click to Close --}}
        <div class="fixed inset-0" @click="showTupoksiModal = false"></div>

        {{-- Dialog Box --}}
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 z-10 space-y-5 animate-in fade-in zoom-in-95 duration-200"
             @click.stop>
            
            {{-- Header Modal: Foto & Profil Pejabat --}}
            <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3.5">
                    <div class="w-16 h-16 rounded-2xl border-2 border-slate-200 overflow-hidden bg-slate-100 shrink-0 shadow-xs flex items-center justify-center">
                        <template x-if="selectedOfficer.photo">
                            <img :src="selectedOfficer.photo" :alt="selectedOfficer.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!selectedOfficer.photo">
                            <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white font-extrabold text-lg uppercase" x-text="selectedOfficer.initial || 'ST'"></div>
                        </template>
                    </div>
                    <div class="space-y-0.5">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-900 text-white font-extrabold text-[9px] uppercase tracking-wider shadow-2xs" x-text="selectedOfficer.role"></span>
                        <h4 class="font-extrabold text-slate-900 text-base leading-snug" x-text="selectedOfficer.name"></h4>
                        <template x-if="selectedOfficer.nip">
                            <p class="text-xs text-slate-500 font-mono font-medium" x-text="'NIP. ' + selectedOfficer.nip"></p>
                        </template>
                        <p class="text-[11px] text-slate-500 font-medium" x-text="selectedOfficer.unit"></p>
                    </div>
                </div>
                <button type="button" @click="showTupoksiModal = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            {{-- Body: Rincian TUPOKSI --}}
            <div class="space-y-2.5">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs shadow-2xs">
                        <i class="fas fa-clipboard-check"></i>
                    </span>
                    <span>Uraian Tugas Pokok & Fungsi (TUPOKSI)</span>
                </div>
                <div class="bg-slate-50/90 rounded-2xl p-4 sm:p-5 border border-slate-200/80 max-h-60 overflow-y-auto custom-scrollbar">
                    <template x-if="selectedOfficer.tupoksi && selectedOfficer.tupoksi.trim() !== ''">
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line font-normal" x-text="selectedOfficer.tupoksi"></p>
                    </template>
                    <template x-if="!selectedOfficer.tupoksi || selectedOfficer.tupoksi.trim() === ''">
                        <div class="text-center py-4 text-slate-400 text-xs italic">
                            <i class="fas fa-info-circle mr-1 text-slate-300"></i>
                            Belum ada uraian tugas khusus yang dicantumkan untuk jabatan ini.
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                <span class="text-[11px] text-slate-400">
                    Pemerintah Kelurahan Semampir
                </span>
                <button type="button" @click="showTupoksiModal = false" 
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-check text-emerald-400 text-xs"></i>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>
</section>

@endsection
