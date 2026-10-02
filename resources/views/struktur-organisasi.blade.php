@extends('layouts.app')

@section('title', 'Struktur Organisasi (SOTK) - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $headPhotoUrl = !empty($villageProfile['head_photo']) ? (str_starts_with($villageProfile['head_photo'], 'http') || str_starts_with($villageProfile['head_photo'], 'data:image') ? $villageProfile['head_photo'] : asset('storage/' . ltrim($villageProfile['head_photo'], '/')) . '?v=' . time()) : null;
    $sekelPhotoUrl = !empty($villageProfile['sekel_photo']) ? asset('storage/' . $villageProfile['sekel_photo']) : null;
    $kasiPemPhotoUrl = !empty($villageProfile['kasi_pem_photo']) ? asset('storage/' . $villageProfile['kasi_pem_photo']) : null;
    $kasiKesraPhotoUrl = !empty($villageProfile['kasi_kesra_photo']) ? asset('storage/' . $villageProfile['kasi_kesra_photo']) : null;
    $kasiEkbangPhotoUrl = !empty($villageProfile['kasi_ekbang_photo']) ? asset('storage/' . $villageProfile['kasi_ekbang_photo']) : null;

    // Filter aparatur dan staf berdasarkan hierarki induk (parent_key)
    $allMembers = $villageProfile['sotk_members'] ?? [];
    $sekelMembers = [];
    $kasiPemMembers = [];
    $kasiKesraMembers = [];
    $kasiEkbangMembers = [];
    $otherMembers = [];

    foreach ($allMembers as $m) {
        $pk = $m['parent_key'] ?? '';
        $pos = strtolower($m['position'] ?? '');
        $name = strtolower($m['name'] ?? '');

        if ($pk === 'sekel' || str_contains($pos, 'keuangan') || str_contains($pos, 'perencanaan') || str_contains($name, 'imam mashari') || str_contains($name, 'seniri')) {
            $sekelMembers[] = $m;
        } elseif ($pk === 'kasi_pem' || str_contains($pos, 'pemerintahan') || str_contains($name, 'ferdi')) {
            $kasiPemMembers[] = $m;
        } elseif ($pk === 'kasi_kesra' || str_contains($pos, 'thl') || str_contains($name, 'abdullah') || str_contains($pos, 'kesra') || str_contains($pos, 'trantib')) {
            $kasiKesraMembers[] = $m;
        } elseif ($pk === 'kasi_ekbang' || str_contains($pos, 'ekbang') || str_contains($name, 'fenny') || str_contains($pos, 'perekonomian')) {
            $kasiEkbangMembers[] = $m;
        } else {
            $otherMembers[] = $m;
        }
    }
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
                    Bagan alur hierarki kelembagaan resmi dan tata kerja aparatur Pemerintah Kelurahan Semampir.
                </p>
            </div>

            {{-- Controls / Switcher Mode --}}
            <div class="flex items-center gap-3 shrink-0">
                <div class="inline-flex p-1 bg-slate-200/80 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 shadow-2xs">
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
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200/70 text-[10px] font-extrabold uppercase tracking-wider shadow-2xs">
                        <i class="fas fa-shield-halved text-[9px]"></i>
                        <span>Bagan Tata Kerja Resmi Kelurahan Semampir</span>
                    </span>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                        Struktur Organisasi & Tata Kerja (SOTK)
                    </h2>
                    <p class="text-xs text-slate-500 flex items-center justify-center gap-1.5">
                        <i class="fas fa-mouse-pointer text-blue-500"></i>
                        <span>Klik pada kartu pejabat untuk melihat rincian <strong>Tugas Pokok & Fungsi (TUPOKSI)</strong>.</span>
                    </p>
                    <div class="pt-1 flex items-center justify-center gap-2 text-[11px] text-slate-400 lg:hidden">
                        <i class="fas fa-arrows-left-right text-slate-400"></i>
                        <span>Geser layar ke samping untuk melihat bagan lengkap</span>
                    </div>
                </div>

                {{-- Org Chart Canvas (overflow-x-auto for responsiveness) --}}
                <div class="overflow-x-auto pb-8 custom-scrollbar">
                    <div class="min-w-[920px] flex flex-col items-center py-6">

                        {{-- 1. TOP NODE (Chief / Lurah) --}}
                        <div class="flex flex-col items-center z-10 relative">
                            <div @click="openTupoksiModal({
                                    name: {{ json_encode($villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.') }},
                                    nip: {{ json_encode($villageProfile['head_nip'] ?? '') }},
                                    role: 'LURAH SEMAMPIR',
                                    unit: 'Kepala Pemerintahan Kelurahan',
                                    photo: {{ json_encode($headPhotoUrl) }},
                                    initial: 'LH',
                                    tupoksi: {{ json_encode($villageProfile['lurah_tupoksi'] ?? 'Penyelenggara urusan pemerintahan, ketenteraman dan ketertiban umum, pemberdayaan masyarakat, pelayanan publik, dan pemeliharaan prasarana fasilitas umum.') }}
                                 })"
                                 class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-60 sm:w-64 transition-all duration-200 group cursor-pointer">
                                
                                {{-- Foto Lingkaran Lurah --}}
                                <div class="w-20 h-20 rounded-full border-2 border-slate-800 p-0.5 mb-2.5 bg-slate-50 shrink-0 shadow-2xs overflow-hidden group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                    @if(!empty($headPhotoUrl))
                                        <img src="{{ $headPhotoUrl }}" 
                                             alt="Lurah Semampir" 
                                             class="w-full h-full object-cover rounded-full">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300 rounded-full">
                                            <svg class="w-full h-full text-slate-300 pt-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <h3 class="font-bold text-xs sm:text-sm text-slate-900 leading-snug">
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
                                            name: {{ json_encode($villageProfile['sekel_name'] ?? 'Sidiq, SH. MM.') }},
                                            nip: {{ json_encode($villageProfile['sekel_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN')) }},
                                            unit: 'Sekretariat Kelurahan',
                                            photo: {{ json_encode($sekelPhotoUrl) }},
                                            initial: 'SK',
                                            tupoksi: {{ json_encode($villageProfile['sekel_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                            @if(!empty($sekelPhotoUrl))
                                                <img src="{{ $sekelPhotoUrl }}" 
                                                     alt="Sekretaris Kelurahan" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                                    <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['sekel_name'] ?? 'Sidiq, SH. MM.' }}
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
                                            name: {{ json_encode($villageProfile['kasi_pem_name'] ?? 'Margareta, S.E') }},
                                            nip: {{ json_encode($villageProfile['kasi_pem_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB')) }},
                                            unit: 'Seksi Pemerintahan',
                                            photo: {{ json_encode($kasiPemPhotoUrl) }},
                                            initial: 'KP',
                                            tupoksi: {{ json_encode($villageProfile['kasi_pem_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                            @if(!empty($kasiPemPhotoUrl))
                                                <img src="{{ $kasiPemPhotoUrl }}" 
                                                     alt="Kasi Pemerintahan" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                                    <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_pem_name'] ?? 'Margareta, S.E' }}
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
                                            name: {{ json_encode($villageProfile['kasi_kesra_name'] ?? 'Herman, S.E') }},
                                            nip: {{ json_encode($villageProfile['kasi_kesra_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA')) }},
                                            unit: 'Seksi Sosial & Kesra',
                                            photo: {{ json_encode($kasiKesraPhotoUrl) }},
                                            initial: 'KK',
                                            tupoksi: {{ json_encode($villageProfile['kasi_kesra_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                            @if(!empty($kasiKesraPhotoUrl))
                                                <img src="{{ $kasiKesraPhotoUrl }}" 
                                                     alt="Kasi Kesra" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                                    <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_kesra_name'] ?? 'Herman, S.E' }}
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
                                            name: {{ json_encode($villageProfile['kasi_ekbang_name'] ?? 'Sabar, S.Sos') }},
                                            nip: {{ json_encode($villageProfile['kasi_ekbang_nip'] ?? '') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG')) }},
                                            unit: 'Seksi Perekonomian',
                                            photo: {{ json_encode($kasiEkbangPhotoUrl) }},
                                            initial: 'KE',
                                            tupoksi: {{ json_encode($villageProfile['kasi_ekbang_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-blue-500 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer">
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                            @if(!empty($kasiEkbangPhotoUrl))
                                                <img src="{{ $kasiEkbangPhotoUrl }}" 
                                                     alt="Kasi Ekbang" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                                    <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                            {{ $villageProfile['kasi_ekbang_name'] ?? 'Sabar, S.Sos' }}
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
                                                <div class="w-16 h-16 rounded-full border-2 border-slate-700 overflow-hidden shadow-2xs mb-2 bg-slate-50 shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                                    @if(!empty($mPhoto))
                                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                                            <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                            </svg>
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

            {{-- MODE 2: DAFTAR PEGAWAI & APARATUR (DIRECTORY LIST TERSTRUKTUR) --}}
            <div x-show="viewMode === 'daftar'" x-transition class="space-y-6" style="display: none;">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Aparatur & Staf Kelurahan</h3>
                        <p class="text-xs text-slate-500">Daftar terstruktur urutan jabatan, nama, NIP, serta tugas pokok seluruh jajaran Kelurahan Semampir.</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full w-fit">
                        Total {{ 5 + count($villageProfile['sotk_members'] ?? []) }} Aparatur
                    </span>
                </div>

                {{-- 1. PIMPINAN UTAMA KELURAHAN --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">1. Pimpinan Utama Kelurahan</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div @click="openTupoksiModal({
                                name: {{ json_encode($villageProfile['head_name'] ?? 'Latif Hasan Asyari, S.H') }},
                                nip: {{ json_encode($villageProfile['head_nip'] ?? '19750612 201001 1 004') }},
                                role: 'LURAH SEMAMPIR',
                                unit: 'Kepala Pemerintahan Kelurahan',
                                photo: {{ json_encode($headPhotoUrl) }},
                                initial: 'LH',
                                tupoksi: {{ json_encode($villageProfile['lurah_tupoksi'] ?? 'Penyelenggara utama urusan pemerintahan, ketertiban umum, pelayanan publik, dan pembinaan wilayah.') }}
                             })"
                             class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                            <div class="w-14 h-14 rounded-full border-2 border-slate-900 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                @if(!empty($headPhotoUrl))
                                    <img src="{{ $headPhotoUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                        <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-900 text-white">Lurah</span>
                                <h4 class="font-bold text-xs text-slate-900 mt-1 truncate">{{ $villageProfile['head_name'] ?? 'Latif Hasan Asyari, S.H' }}</h4>
                                @if(!empty($villageProfile['head_nip']))
                                    <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $villageProfile['head_nip'] }}</p>
                                @endif
                                <p class="text-[11px] font-semibold text-slate-600">Kepala Pemerintahan Kelurahan</p>
                                <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $villageProfile['lurah_tupoksi'] ?? 'Pimpinan utama pemerintahan kelurahan.' }}</p>
                                <span class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 group-hover:text-blue-700">
                                    <span>Lihat TUPOKSI Lengkap</span>
                                    <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-0.5 transition-transform"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. SEKRETARIAT KELURAHAN --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">2. Sekretariat Kelurahan</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Sekel --}}
                        <div @click="openTupoksiModal({
                                name: {{ json_encode($villageProfile['sekel_name'] ?? 'Sidiq, SH. MM.') }},
                                nip: {{ json_encode($villageProfile['sekel_nip'] ?? '19710312 200906 1 001') }},
                                role: {{ json_encode(strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN')) }},
                                unit: 'Sekretariat Kelurahan',
                                photo: {{ json_encode($sekelPhotoUrl) }},
                                initial: 'SK',
                                tupoksi: {{ json_encode($villageProfile['sekel_tupoksi'] ?? '') }}
                             })"
                             class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                @if(!empty($sekelPhotoUrl))
                                    <img src="{{ $sekelPhotoUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                        <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-extrabold">Sekretaris</span>
                                <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['sekel_name'] ?? 'Sidiq, SH. MM.' }}</h4>
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

                        {{-- Subag Keuangan & Perencanaan --}}
                        @foreach($sekelMembers as $m)
                            @php
                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                            @endphp
                            <div @click="openTupoksiModal({
                                    name: {{ json_encode($m['name'] ?? 'Subag') }},
                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                    role: {{ json_encode(strtoupper($m['position'] ?? 'SUBAG')) }},
                                    unit: 'Sekretariat Kelurahan',
                                    photo: {{ json_encode($mPhoto) }},
                                    initial: {{ json_encode($mInitial) }},
                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                 })"
                                 class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                                <div class="w-14 h-14 rounded-full border-2 border-slate-700 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                    @if(!empty($mPhoto))
                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                            <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-800">Sub-Bagian</span>
                                    <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $m['name'] }}</h4>
                                    @if(!empty($m['nip']))
                                        <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $m['nip'] }}</p>
                                    @endif
                                    <p class="text-[11px] font-semibold text-slate-700">{{ $m['position'] ?? 'Subag' }}</p>
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

                {{-- 3. SEKSI PEMERINTAHAN --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">3. Seksi Pemerintahan</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Kasi Pem --}}
                        <div @click="openTupoksiModal({
                                name: {{ json_encode($villageProfile['kasi_pem_name'] ?? 'Margareta, S.E') }},
                                nip: {{ json_encode($villageProfile['kasi_pem_nip'] ?? '19600330 201101 2 006') }},
                                role: {{ json_encode(strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB')) }},
                                unit: 'Seksi Pemerintahan',
                                photo: {{ json_encode($kasiPemPhotoUrl) }},
                                initial: 'KP',
                                tupoksi: {{ json_encode($villageProfile['kasi_pem_tupoksi'] ?? '') }}
                             })"
                             class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                @if(!empty($kasiPemPhotoUrl))
                                    <img src="{{ $kasiPemPhotoUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                        <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold">Kepala Seksi</span>
                                <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_pem_name'] ?? 'Margareta, S.E' }}</h4>
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

                        {{-- Staf Pem --}}
                        @foreach($kasiPemMembers as $m)
                            @php
                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                            @endphp
                            <div @click="openTupoksiModal({
                                    name: {{ json_encode($m['name'] ?? 'Staf') }},
                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                    role: {{ json_encode(strtoupper($m['position'] ?? 'STAF')) }},
                                    unit: 'Seksi Pemerintahan',
                                    photo: {{ json_encode($mPhoto) }},
                                    initial: {{ json_encode($mInitial) }},
                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                 })"
                                 class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                                <div class="w-14 h-14 rounded-full border-2 border-slate-700 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                    @if(!empty($mPhoto))
                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                            <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
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

                {{-- 4. SEKSI SOSIAL & KESEJAHTERAAN RAKYAT --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">4. Seksi Sosial & Kesejahteraan Rakyat</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Kasi Kesra --}}
                        <div @click="openTupoksiModal({
                                name: {{ json_encode($villageProfile['kasi_kesra_name'] ?? 'Herman, S.E') }},
                                nip: {{ json_encode($villageProfile['kasi_kesra_nip'] ?? '19741001 200801 1 013') }},
                                role: {{ json_encode(strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA')) }},
                                unit: 'Seksi Sosial & Kesra',
                                photo: {{ json_encode($kasiKesraPhotoUrl) }},
                                initial: 'KK',
                                tupoksi: {{ json_encode($villageProfile['kasi_kesra_tupoksi'] ?? '') }}
                             })"
                             class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                @if(!empty($kasiKesraPhotoUrl))
                                    <img src="{{ $kasiKesraPhotoUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                        <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-extrabold">Kepala Seksi</span>
                                <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_kesra_name'] ?? 'Herman, S.E' }}</h4>
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

                        {{-- Staf Kesra (Abdullah) --}}
                        @foreach($kasiKesraMembers as $m)
                            @php
                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                            @endphp
                            <div @click="openTupoksiModal({
                                    name: {{ json_encode($m['name'] ?? 'Staf') }},
                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                    role: {{ json_encode(strtoupper($m['position'] ?? 'STAF PELAKSANA (THL)')) }},
                                    unit: 'Seksi Sosial & Kesra',
                                    photo: {{ json_encode($mPhoto) }},
                                    initial: {{ json_encode($mInitial) }},
                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                 })"
                                 class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                                <div class="w-14 h-14 rounded-full border-2 border-slate-700 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                    @if(!empty($mPhoto))
                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                            <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-700">Staf Pelaksana (THL)</span>
                                    <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $m['name'] }}</h4>
                                    @if(!empty($m['nip']))
                                        <p class="text-[10px] text-slate-500 font-mono font-medium">NIP. {{ $m['nip'] }}</p>
                                    @else
                                        <p class="text-[10px] text-slate-400 italic">Tenaga Harian Lepas</p>
                                    @endif
                                    <p class="text-[11px] font-semibold text-slate-700">{{ $m['position'] ?? 'Staf Pelaksana (THL)' }}</p>
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

                {{-- 5. SEKSI PEREKONOMIAN & PEMBANGUNAN --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">5. Seksi Perekonomian & Pembangunan</h4>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Kasi Ekbang --}}
                        <div @click="openTupoksiModal({
                                name: {{ json_encode($villageProfile['kasi_ekbang_name'] ?? 'Sabar, S.Sos') }},
                                nip: {{ json_encode($villageProfile['kasi_ekbang_nip'] ?? '19690206 199103 1 004') }},
                                role: {{ json_encode(strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG')) }},
                                unit: 'Seksi Perekonomian',
                                photo: {{ json_encode($kasiEkbangPhotoUrl) }},
                                initial: 'KE',
                                tupoksi: {{ json_encode($villageProfile['kasi_ekbang_tupoksi'] ?? '') }}
                             })"
                             class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                @if(!empty($kasiEkbangPhotoUrl))
                                    <img src="{{ $kasiEkbangPhotoUrl }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                        <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 font-extrabold">Kepala Seksi</span>
                                <h4 class="font-bold text-xs text-slate-900 mt-1 truncate capitalize">{{ $villageProfile['kasi_ekbang_name'] ?? 'Sabar, S.Sos' }}</h4>
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

                        {{-- Staf Ekbang (Fenny) --}}
                        @foreach($kasiEkbangMembers as $m)
                            @php
                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                            @endphp
                            <div @click="openTupoksiModal({
                                    name: {{ json_encode($m['name'] ?? 'Staf') }},
                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                    role: {{ json_encode(strtoupper($m['position'] ?? 'STAF PELAKSANA')) }},
                                    unit: 'Seksi Perekonomian',
                                    photo: {{ json_encode($mPhoto) }},
                                    initial: {{ json_encode($mInitial) }},
                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                 })"
                                 class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                                <div class="w-14 h-14 rounded-full border-2 border-slate-700 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                    @if(!empty($mPhoto))
                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                            <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
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

                {{-- 6. LAIN-LAIN JIKA ADA --}}
                @if(!empty($otherMembers))
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">6. Staf & Kelompok Jabatan Fungsional Lainnya</h4>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($otherMembers as $m)
                                @php
                                    $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                    $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                                @endphp
                                <div @click="openTupoksiModal({
                                        name: {{ json_encode($m['name'] ?? 'Staf') }},
                                        nip: {{ json_encode($m['nip'] ?? '') }},
                                        role: {{ json_encode(strtoupper($m['position'] ?? 'STAF')) }},
                                        unit: 'Staf Kelurahan',
                                        photo: {{ json_encode($mPhoto) }},
                                        initial: {{ json_encode($mInitial) }},
                                        tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                     })"
                                     class="p-4 bg-slate-50/80 hover:bg-white rounded-2xl border border-slate-200 hover:border-blue-500 flex items-start gap-3.5 transition shadow-2xs hover:shadow-md cursor-pointer group">
                                    <div class="w-14 h-14 rounded-full border-2 border-slate-700 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                        @if(!empty($mPhoto))
                                            <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                                <svg class="w-full h-full text-slate-300 pt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-slate-200 text-slate-700">Staf</span>
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
                @endif

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
                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                <svg class="w-full h-full text-slate-300 pt-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
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
