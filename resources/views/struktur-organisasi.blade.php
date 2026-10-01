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

                {{-- Org Chart Canvas (Format Standar Resmi Pemerintahan Bersih & Rapi) --}}
                <div class="overflow-x-auto pb-8 custom-scrollbar">
                    <div class="min-w-[1220px] max-w-[1360px] mx-auto py-6 px-4 rounded-2xl bg-slate-50/50 border border-slate-200 relative">
                        
                        {{-- ==================================================== --}}
                        {{-- TIER 1: LURAH SEMAMPIR (PIMPINAN TERTINGGI)          --}}
                        {{-- ==================================================== --}}
                        <div class="flex flex-col items-center z-30 relative">
                            <div @click="openTupoksiModal({
                                    name: {{ json_encode($villageProfile['head_name'] ?? 'Latif Hasan Asyari, S.H') }},
                                    nip: {{ json_encode($villageProfile['head_nip'] ?? '19750612 201001 1 004') }},
                                    role: 'LURAH SEMAMPIR',
                                    unit: 'Kepala Pemerintahan Kelurahan',
                                    photo: {{ json_encode($headPhotoUrl) }},
                                    initial: 'LH',
                                    tupoksi: {{ json_encode($villageProfile['lurah_tupoksi'] ?? 'Memimpin penyelenggaraan pemerintahan di Kelurahan Semampir.') }}
                                 })"
                                 class="bg-white rounded-xl p-4 border-2 border-blue-900 shadow-sm hover:shadow-md flex flex-col items-center text-center w-72 transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                
                                {{-- Badge Jabatan Pimpinan --}}
                                <div class="mb-2">
                                    <span class="inline-flex items-center gap-1.5 bg-blue-900 text-white font-bold text-[9.5px] uppercase px-3.5 py-1 rounded-full tracking-wider shadow-2xs">
                                        <i class="fas fa-landmark text-[9px] text-amber-300"></i>
                                        <span>LURAH SEMAMPIR</span>
                                    </span>
                                </div>

                                {{-- Avatar Frame --}}
                                <div class="w-20 h-20 rounded-full border-2 border-slate-300 p-0.5 mb-2 bg-slate-50 shrink-0 shadow-2xs overflow-hidden group-hover:scale-105 transition-transform duration-200">
                                    <img src="{{ $headPhotoUrl }}" 
                                         alt="Lurah Semampir" 
                                         onerror="this.onerror=null; this.src='{{ asset('images/sotk/lurah.png') }}';"
                                         class="w-full h-full object-cover rounded-full">
                                </div>

                                <h3 class="font-bold text-sm text-slate-900 leading-snug group-hover:text-blue-900 transition-colors">
                                    {{ $villageProfile['head_name'] ?? 'Latif Hasan Asyari, S.H' }}
                                </h3>

                                @if(!empty($villageProfile['head_nip']))
                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['head_nip'] }}</p>
                                @endif

                                <p class="text-[10.5px] text-slate-600 mt-1 font-medium">Kepala Pemerintahan Kelurahan</p>

                                <span class="mt-2.5 inline-flex items-center gap-1.5 px-3.5 py-1 bg-blue-50 group-hover:bg-blue-900 group-hover:text-white text-blue-900 text-[10px] font-bold rounded-lg border border-blue-200 transition-colors shadow-2xs">
                                    <i class="fas fa-clipboard-list text-[9px]"></i>
                                    <span>Lihat TUPOKSI</span>
                                </span>
                            </div>

                            {{-- Garis Lurus Turun dari Lurah --}}
                            <div style="width: 2.5px; height: 32px; background-color: #1e3a8a;"></div>
                        </div>

                        {{-- ==================================================== --}}
                        {{-- TIER 2 & 3: 4 PILAR DEPARTEMEN DENGAN STAF MASING-MASING --}}
                        {{-- ==================================================== --}}
                        <div class="w-full relative mt-0">
                            
                            {{-- Rel Garis Horizontal Standar Struktur Organisasi (Dari Pusat Kolom 1 ke Kolom 4) --}}
                            <div class="absolute top-0 z-10" style="left: 15.5%; right: 11.5%; height: 2.5px; background-color: #1e3a8a;"></div>

                            {{-- 4 Pilar Utama Sejajar --}}
                            <div class="w-full flex items-start">

                                {{-- ================================================ --}}
                                {{-- KOLOM 1: SEKRETARIAT KELURAHAN (SEKEL + 2 SUBAG) --}}
                                {{-- ================================================ --}}
                                <div class="w-[31%] px-2.5 flex flex-col items-center relative">
                                    
                                    {{-- Garis Drop Vertikal Masuk ke Sekel --}}
                                    <div style="width: 2.5px; height: 28px; background-color: #1e3a8a;"></div>

                                    {{-- Kartu: Sekretaris Kelurahan --}}
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['sekel_name'] ?? 'Sidiq, SH. MM.') }},
                                            nip: {{ json_encode($villageProfile['sekel_nip'] ?? '19710312 200906 1 001') }},
                                            role: {{ json_encode(strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN')) }},
                                            unit: 'Sekretariat Kelurahan',
                                            photo: {{ json_encode($sekelPhotoUrl) }},
                                            initial: 'SK',
                                            tupoksi: {{ json_encode($villageProfile['sekel_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-xl p-3.5 border-2 border-slate-300 hover:border-blue-900 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full max-w-[245px] transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                        
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-300 p-0.5 mb-2 bg-slate-50 shrink-0 overflow-hidden group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $sekelPhotoUrl }}" 
                                                 alt="Sekretaris Kelurahan" 
                                                 class="w-full h-full object-cover rounded-full">
                                        </div>

                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize group-hover:text-blue-900 transition-colors">
                                            {{ $villageProfile['sekel_name'] ?? 'Sidiq, SH. MM.' }}
                                        </h5>

                                        @if(!empty($villageProfile['sekel_nip']))
                                            <p class="text-[9.5px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['sekel_nip'] }}</p>
                                        @endif

                                        <div class="mt-1.5">
                                            <span class="bg-blue-900 text-white font-bold text-[8.5px] uppercase px-2.5 py-0.5 rounded-full tracking-wider inline-block">
                                                {{ strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN') }}
                                            </span>
                                        </div>
                                        <span class="text-[9.5px] text-slate-500 mt-1 font-medium">Sekretariat Kelurahan</span>

                                        <span class="mt-2.5 inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 group-hover:bg-blue-900 group-hover:text-white text-slate-700 text-[9px] font-bold rounded-lg border border-slate-200 transition-colors shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[8.5px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
                                    </div>

                                    {{-- Cabang Hierarki Langsung ke 2 Subag di Bawah Sekel --}}
                                    <div class="w-full flex flex-col items-center relative mt-0">
                                        
                                        {{-- Garis Tegak Turun dari Sekel --}}
                                        <div style="width: 2px; height: 22px; background-color: #475569;"></div>

                                        {{-- Garis Cabang Horizontal Menghubungkan 2 Subag --}}
                                        <div class="w-full relative">
                                            <div class="grid grid-cols-2 gap-3 w-full relative">
                                                {{-- Garis Rel Horizontal Cabang (dari tengah kolom kiri 25% ke tengah kolom kanan 75%) --}}
                                                <div class="absolute top-0" style="left: 25%; right: 25%; height: 2px; background-color: #475569;"></div>

                                                @forelse($sekelMembers as $m)
                                                    @php
                                                        $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                                        $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                                                    @endphp
                                                    <div class="flex flex-col items-center relative">
                                                        {{-- Garis Turun ke Kartu Subag --}}
                                                        <div style="width: 2px; height: 16px; background-color: #475569;"></div>

                                                        <div @click="openTupoksiModal({
                                                                name: {{ json_encode($m['name'] ?? 'Subag') }},
                                                                nip: {{ json_encode($m['nip'] ?? '') }},
                                                                role: {{ json_encode(strtoupper($m['position'] ?? 'SUBAG')) }},
                                                                unit: 'Sekretariat Kelurahan',
                                                                photo: {{ json_encode($mPhoto) }},
                                                                initial: {{ json_encode($mInitial) }},
                                                                tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                                             })"
                                                             class="bg-slate-50/70 hover:bg-white rounded-xl p-3 border border-slate-300 hover:border-slate-400 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                                            
                                                            <div class="w-12 h-12 rounded-full border border-slate-300 overflow-hidden shadow-2xs mb-1.5 bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                                                @if(!empty($mPhoto))
                                                                    <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                                @else
                                                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs uppercase">
                                                                        {{ $mInitial }}
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <h6 class="font-bold text-[11px] text-slate-900 leading-tight capitalize truncate w-full group-hover:text-blue-900 transition-colors">
                                                                {{ $m['name'] }}
                                                            </h6>

                                                            @if(!empty($m['nip']))
                                                                <p class="text-[8.5px] text-slate-500 font-mono mt-0.5 font-medium leading-none">NIP. {{ $m['nip'] }}</p>
                                                            @endif

                                                            <div class="mt-1">
                                                                <span class="bg-slate-700 text-white font-bold text-[8px] uppercase px-2 py-0.5 rounded-full tracking-wider inline-block">
                                                                    {{ $m['position'] ?? 'SUBAG' }}
                                                                </span>
                                                            </div>

                                                            <span class="mt-2 inline-flex items-center gap-1 text-[8.5px] font-bold text-blue-700 group-hover:text-blue-900">
                                                                <span>TUPOKSI</span>
                                                                <i class="fas fa-arrow-right text-[7px] group-hover:translate-x-0.5 transition-transform"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-span-2 text-center text-xs text-slate-400 py-3 italic">
                                                        Belum ada sub-bagian
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- ================================================ --}}
                                {{-- KOLOM 2: SEKSI PEMERINTAHAN (KASI PEM + FERDI)   --}}
                                {{-- ================================================ --}}
                                <div class="w-[23%] px-2.5 flex flex-col items-center relative">
                                    
                                    {{-- Garis Drop Vertikal Masuk ke Kasi Pem --}}
                                    <div style="width: 2.5px; height: 28px; background-color: #1e3a8a;"></div>

                                    {{-- Kartu: Kasi Pemerintahan --}}
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['kasi_pem_name'] ?? 'Margareta, S.E') }},
                                            nip: {{ json_encode($villageProfile['kasi_pem_nip'] ?? '19600330 201101 2 006') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB')) }},
                                            unit: 'Seksi Pemerintahan',
                                            photo: {{ json_encode($kasiPemPhotoUrl) }},
                                            initial: 'KP',
                                            tupoksi: {{ json_encode($villageProfile['kasi_pem_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-xl p-3.5 border-2 border-slate-300 hover:border-blue-900 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full max-w-[245px] transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                        
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-300 p-0.5 mb-2 bg-slate-50 shrink-0 overflow-hidden group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $kasiPemPhotoUrl }}" 
                                                 alt="Kasi Pemerintahan" 
                                                 class="w-full h-full object-cover rounded-full">
                                        </div>

                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize group-hover:text-blue-900 transition-colors">
                                            {{ $villageProfile['kasi_pem_name'] ?? 'Margareta, S.E' }}
                                        </h5>

                                        @if(!empty($villageProfile['kasi_pem_nip']))
                                            <p class="text-[9.5px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_pem_nip'] }}</p>
                                        @endif

                                        <div class="mt-1.5">
                                            <span class="bg-blue-900 text-white font-bold text-[8.5px] uppercase px-2.5 py-0.5 rounded-full tracking-wider inline-block">
                                                {{ strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB') }}
                                            </span>
                                        </div>
                                        <span class="text-[9.5px] text-slate-500 mt-1 font-medium">Seksi Pemerintahan</span>

                                        <span class="mt-2.5 inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 group-hover:bg-blue-900 group-hover:text-white text-slate-700 text-[9px] font-bold rounded-lg border border-slate-200 transition-colors shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[8.5px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
                                    </div>

                                    {{-- Garis Sambungan Langsung Tegak ke Staf Pelaksana --}}
                                    <div style="width: 2px; height: 24px; background-color: #475569;"></div>

                                    {{-- Subordinate(s) di Bawah Kasi Pem --}}
                                    <div class="w-full max-w-[245px]">
                                        @forelse($kasiPemMembers as $m)
                                            @php
                                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                                            @endphp
                                            <div @click="openTupoksiModal({
                                                    name: {{ json_encode($m['name'] ?? 'Staf Pelaksana') }},
                                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                                    role: {{ json_encode(strtoupper($m['position'] ?? 'STAF PELAKSANA')) }},
                                                    unit: 'Seksi Pemerintahan',
                                                    photo: {{ json_encode($mPhoto) }},
                                                    initial: {{ json_encode($mInitial) }},
                                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                                 })"
                                                 class="bg-slate-50/70 hover:bg-white rounded-xl p-3 border border-slate-300 hover:border-slate-400 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                                
                                                <div class="w-12 h-12 rounded-full border border-slate-300 overflow-hidden shadow-2xs mb-1.5 bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                                    @if(!empty($mPhoto))
                                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs uppercase">
                                                            {{ $mInitial }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <h6 class="font-bold text-xs text-slate-900 leading-tight capitalize group-hover:text-blue-900 transition-colors">
                                                    {{ $m['name'] }}
                                                </h6>

                                                @if(!empty($m['nip']))
                                                    <p class="text-[9px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $m['nip'] }}</p>
                                                @endif

                                                <div class="mt-1">
                                                    <span class="bg-slate-700 text-white font-bold text-[8px] uppercase px-2 py-0.5 rounded-full tracking-wider inline-block">
                                                        {{ $m['position'] ?? 'STAF PELAKSANA' }}
                                                    </span>
                                                </div>

                                                <span class="text-[9.5px] text-slate-500 mt-1 font-medium">Staf Pendukung</span>

                                                <span class="mt-2 inline-flex items-center gap-1 text-[8.5px] font-bold text-blue-700 group-hover:text-blue-900">
                                                    <span>Lihat TUPOKSI</span>
                                                    <i class="fas fa-arrow-right text-[7px] group-hover:translate-x-0.5 transition-transform"></i>
                                                </span>
                                            </div>
                                        @empty
                                            <div class="text-center text-xs text-slate-400 py-3 italic bg-white rounded-xl border border-dashed border-slate-200">
                                                Belum ada staf pelaksana
                                            </div>
                                        @endforelse
                                    </div>

                                </div>

                                {{-- ================================================ --}}
                                {{-- KOLOM 3: SEKSI KESRA (KASI KESRA + ABDULLAH)     --}}
                                {{-- ================================================ --}}
                                <div class="w-[23%] px-2.5 flex flex-col items-center relative">
                                    
                                    {{-- Garis Drop Vertikal Masuk ke Kasi Kesra --}}
                                    <div style="width: 2.5px; height: 28px; background-color: #1e3a8a;"></div>

                                    {{-- Kartu: Kasi Kesra --}}
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['kasi_kesra_name'] ?? 'Herman, S.E') }},
                                            nip: {{ json_encode($villageProfile['kasi_kesra_nip'] ?? '19741001 200801 1 013') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA')) }},
                                            unit: 'Seksi Sosial & Kesra',
                                            photo: {{ json_encode($kasiKesraPhotoUrl) }},
                                            initial: 'KK',
                                            tupoksi: {{ json_encode($villageProfile['kasi_kesra_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-xl p-3.5 border-2 border-slate-300 hover:border-blue-900 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full max-w-[245px] transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                        
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-300 p-0.5 mb-2 bg-slate-50 shrink-0 overflow-hidden group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $kasiKesraPhotoUrl }}" 
                                                 alt="Kasi Kesra" 
                                                 class="w-full h-full object-cover rounded-full">
                                        </div>

                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize group-hover:text-blue-900 transition-colors">
                                            {{ $villageProfile['kasi_kesra_name'] ?? 'Herman, S.E' }}
                                        </h5>

                                        @if(!empty($villageProfile['kasi_kesra_nip']))
                                            <p class="text-[9.5px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_kesra_nip'] }}</p>
                                        @endif

                                        <div class="mt-1.5">
                                            <span class="bg-blue-900 text-white font-bold text-[8.5px] uppercase px-2.5 py-0.5 rounded-full tracking-wider inline-block">
                                                {{ strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA') }}
                                            </span>
                                        </div>
                                        <span class="text-[9.5px] text-slate-500 mt-1 font-medium">Seksi Sosial & Kesra</span>

                                        <span class="mt-2.5 inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 group-hover:bg-blue-900 group-hover:text-white text-slate-700 text-[9px] font-bold rounded-lg border border-slate-200 transition-colors shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[8.5px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
                                    </div>

                                    {{-- Garis Sambungan Langsung Tegak ke Abdullah --}}
                                    <div style="width: 2px; height: 24px; background-color: #475569;"></div>

                                    {{-- Subordinate(s) di Bawah Kasi Kesra --}}
                                    <div class="w-full max-w-[245px]">
                                        @forelse($kasiKesraMembers as $m)
                                            @php
                                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                                            @endphp
                                            <div @click="openTupoksiModal({
                                                    name: {{ json_encode($m['name'] ?? 'Staf Pelaksana') }},
                                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                                    role: {{ json_encode(strtoupper($m['position'] ?? 'STAF PELAKSANA (THL)')) }},
                                                    unit: 'Seksi Sosial & Kesra',
                                                    photo: {{ json_encode($mPhoto) }},
                                                    initial: {{ json_encode($mInitial) }},
                                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                                 })"
                                                 class="bg-slate-50/70 hover:bg-white rounded-xl p-3 border border-slate-300 hover:border-slate-400 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                                
                                                <div class="w-12 h-12 rounded-full border border-slate-300 overflow-hidden shadow-2xs mb-1.5 bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                                    @if(!empty($mPhoto))
                                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs uppercase">
                                                            {{ $mInitial }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <h6 class="font-bold text-xs text-slate-900 leading-tight capitalize group-hover:text-blue-900 transition-colors">
                                                    {{ $m['name'] }}
                                                </h6>

                                                @if(!empty($m['nip']))
                                                    <p class="text-[9px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $m['nip'] }}</p>
                                                @else
                                                    <p class="text-[9px] text-slate-400 italic mt-0.5">Tenaga Harian Lepas</p>
                                                @endif

                                                <div class="mt-1">
                                                    <span class="bg-slate-700 text-white font-bold text-[8px] uppercase px-2 py-0.5 rounded-full tracking-wider inline-block">
                                                        {{ $m['position'] ?? 'STAF PELAKSANA (THL)' }}
                                                    </span>
                                                </div>

                                                <span class="text-[9.5px] text-slate-500 mt-1 font-medium">Staf Lapangan & Trantib</span>

                                                <span class="mt-2 inline-flex items-center gap-1 text-[8.5px] font-bold text-blue-700 group-hover:text-blue-900">
                                                    <span>Lihat TUPOKSI</span>
                                                    <i class="fas fa-arrow-right text-[7px] group-hover:translate-x-0.5 transition-transform"></i>
                                                </span>
                                            </div>
                                        @empty
                                            <div class="text-center text-xs text-slate-400 py-3 italic bg-white rounded-xl border border-dashed border-slate-200">
                                                Belum ada staf pelaksana
                                            </div>
                                        @endforelse
                                    </div>

                                </div>

                                {{-- ================================================ --}}
                                {{-- KOLOM 4: SEKSI EKBANG (KASI EKBANG + FENNY)       --}}
                                {{-- ================================================ --}}
                                <div class="w-[23%] px-2.5 flex flex-col items-center relative">
                                    
                                    {{-- Garis Drop Vertikal Masuk ke Kasi Ekbang --}}
                                    <div style="width: 2.5px; height: 28px; background-color: #1e3a8a;"></div>

                                    {{-- Kartu: Kasi Ekbang --}}
                                    <div @click="openTupoksiModal({
                                            name: {{ json_encode($villageProfile['kasi_ekbang_name'] ?? 'Sabar, S.Sos') }},
                                            nip: {{ json_encode($villageProfile['kasi_ekbang_nip'] ?? '19690206 199103 1 004') }},
                                            role: {{ json_encode(strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG')) }},
                                            unit: 'Seksi Perekonomian',
                                            photo: {{ json_encode($kasiEkbangPhotoUrl) }},
                                            initial: 'KE',
                                            tupoksi: {{ json_encode($villageProfile['kasi_ekbang_tupoksi'] ?? '') }}
                                         })"
                                         class="bg-white rounded-xl p-3.5 border-2 border-slate-300 hover:border-blue-900 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full max-w-[245px] transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                        
                                        <div class="w-16 h-16 rounded-full border-2 border-slate-300 p-0.5 mb-2 bg-slate-50 shrink-0 overflow-hidden group-hover:scale-105 transition-transform duration-200">
                                            <img src="{{ $kasiEkbangPhotoUrl }}" 
                                                 alt="Kasi Ekbang" 
                                                 class="w-full h-full object-cover rounded-full">
                                        </div>

                                        <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize group-hover:text-blue-900 transition-colors">
                                            {{ $villageProfile['kasi_ekbang_name'] ?? 'Sabar, S.Sos' }}
                                        </h5>

                                        @if(!empty($villageProfile['kasi_ekbang_nip']))
                                            <p class="text-[9.5px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_ekbang_nip'] }}</p>
                                        @endif

                                        <div class="mt-1.5">
                                            <span class="bg-blue-900 text-white font-bold text-[8.5px] uppercase px-2.5 py-0.5 rounded-full tracking-wider inline-block">
                                                {{ strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG') }}
                                            </span>
                                        </div>
                                        <span class="text-[9.5px] text-slate-500 mt-1 font-medium">Seksi Perekonomian</span>

                                        <span class="mt-2.5 inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 group-hover:bg-blue-900 group-hover:text-white text-slate-700 text-[9px] font-bold rounded-lg border border-slate-200 transition-colors shadow-2xs">
                                            <i class="fas fa-clipboard-list text-[8.5px]"></i>
                                            <span>Lihat TUPOKSI</span>
                                        </span>
                                    </div>

                                    {{-- Garis Sambungan Langsung Tegak ke Fenny --}}
                                    <div style="width: 2px; height: 24px; background-color: #475569;"></div>

                                    {{-- Subordinate(s) di Bawah Kasi Ekbang --}}
                                    <div class="w-full max-w-[245px]">
                                        @forelse($kasiEkbangMembers as $m)
                                            @php
                                                $mPhoto = !empty($m['photo']) ? asset('storage/' . $m['photo']) : null;
                                                $mInitial = strtoupper(substr($m['name'] ?? 'S', 0, 2));
                                            @endphp
                                            <div @click="openTupoksiModal({
                                                    name: {{ json_encode($m['name'] ?? 'Staf Pelaksana') }},
                                                    nip: {{ json_encode($m['nip'] ?? '') }},
                                                    role: {{ json_encode(strtoupper($m['position'] ?? 'STAF PELAKSANA')) }},
                                                    unit: 'Seksi Perekonomian',
                                                    photo: {{ json_encode($mPhoto) }},
                                                    initial: {{ json_encode($mInitial) }},
                                                    tupoksi: {{ json_encode($m['tupoksi'] ?? '') }}
                                                 })"
                                                 class="bg-slate-50/70 hover:bg-white rounded-xl p-3 border border-slate-300 hover:border-slate-400 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-full transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                                
                                                <div class="w-12 h-12 rounded-full border border-slate-300 overflow-hidden shadow-2xs mb-1.5 bg-white shrink-0 group-hover:scale-105 transition-transform duration-200 flex items-center justify-center">
                                                    @if(!empty($mPhoto))
                                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs uppercase">
                                                            {{ $mInitial }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <h6 class="font-bold text-xs text-slate-900 leading-tight capitalize group-hover:text-blue-900 transition-colors">
                                                    {{ $m['name'] }}
                                                </h6>

                                                @if(!empty($m['nip']))
                                                    <p class="text-[9px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $m['nip'] }}</p>
                                                @endif

                                                <div class="mt-1">
                                                    <span class="bg-slate-700 text-white font-bold text-[8px] uppercase px-2 py-0.5 rounded-full tracking-wider inline-block">
                                                        {{ $m['position'] ?? 'STAF PELAKSANA' }}
                                                    </span>
                                                </div>

                                                <span class="text-[9.5px] text-slate-500 mt-1 font-medium">Staf Pendukung</span>

                                                <span class="mt-2 inline-flex items-center gap-1 text-[8.5px] font-bold text-blue-700 group-hover:text-blue-900">
                                                    <span>Lihat TUPOKSI</span>
                                                    <i class="fas fa-arrow-right text-[7px] group-hover:translate-x-0.5 transition-transform"></i>
                                                </span>
                                            </div>
                                        @empty
                                            <div class="text-center text-xs text-slate-400 py-3 italic bg-white rounded-xl border border-dashed border-slate-200">
                                                Belum ada staf pelaksana
                                            </div>
                                        @endforelse
                                    </div>

                                </div>

                            </div>

                            {{-- Anggota Tambahan Lainnya (jika ada yang belum terkategori) --}}
                            @if(!empty($otherMembers) && count($otherMembers) > 0)
                                <div class="pt-10 relative flex flex-col items-center">
                                    <div style="width: 4px; height: 32px; background-color: #94a3b8; border-radius: 9999px;" class="mb-3"></div>
                                    <span class="text-[10px] font-extrabold uppercase px-3 py-1 bg-slate-100 text-slate-600 rounded-full border border-slate-200 mb-4 tracking-wider">
                                        Staf & Jabatan Fungsional Lainnya
                                    </span>
                                    <div class="flex flex-wrap justify-center gap-4">
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
                                                 class="bg-white rounded-2xl p-3.5 border-2 border-slate-200 hover:border-blue-600 shadow-2xs hover:shadow-md flex flex-col items-center text-center w-48 transition-all duration-200 group cursor-pointer hover:-translate-y-0.5">
                                                <div class="w-12 h-12 rounded-full border border-slate-700 overflow-hidden shadow-2xs mb-1.5 bg-slate-50 shrink-0 flex items-center justify-center">
                                                    @if(!empty($mPhoto))
                                                        <img src="{{ $mPhoto }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="font-extrabold text-xs text-slate-700">{{ $mInitial }}</span>
                                                    @endif
                                                </div>
                                                <h6 class="font-bold text-xs text-slate-900 leading-tight capitalize">{{ $m['name'] }}</h6>
                                                @if(!empty($m['nip']))
                                                    <p class="text-[9px] text-slate-500 font-mono mt-0.5">NIP. {{ $m['nip'] }}</p>
                                                @endif
                                                <span class="bg-slate-800 text-white font-bold text-[8px] uppercase px-2 py-0.5 rounded-full mt-1.5">{{ $m['position'] ?? 'STAF' }}</span>
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
                            <div class="w-14 h-14 rounded-full border-2 border-slate-900 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <img src="{{ $headPhotoUrl }}" 
                                     onerror="this.onerror=null; this.src='{{ asset('images/sotk/lurah.png') }}';"
                                     class="w-full h-full object-cover">
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
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <img src="{{ $sekelPhotoUrl }}" class="w-full h-full object-cover">
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
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-extrabold text-sm uppercase">
                                            {{ $mInitial }}
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
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <img src="{{ $kasiPemPhotoUrl }}" class="w-full h-full object-cover">
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
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <img src="{{ $kasiKesraPhotoUrl }}" class="w-full h-full object-cover">
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
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-extrabold text-sm uppercase">
                                            {{ $mInitial }}
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
                            <div class="w-14 h-14 rounded-full border-2 border-slate-800 overflow-hidden bg-white shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <img src="{{ $kasiEkbangPhotoUrl }}" class="w-full h-full object-cover">
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
                                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-700 font-extrabold text-sm uppercase">
                                                {{ $mInitial }}
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
