@extends('layouts.app')

@section('title', 'Visi & Misi - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $rawVision = $villageProfile['vision'] ?? 'Terwujudnya Pelayanan Publik Kelurahan Semampir yang Transparan, Akuntabel, Berbasis Digital, dan Berkelanjutan Demi Kesejahteraan Masyarakat.';
    $cleanVision = trim(strip_tags($rawVision));
    if (empty($cleanVision)) {
        $cleanVision = 'Terwujudnya Pelayanan Publik Kelurahan Semampir yang Transparan, Akuntabel, Berbasis Digital, dan Berkelanjutan Demi Kesejahteraan Masyarakat.';
    }

    $rawMission = $villageProfile['mission'] ?? '';
    preg_match_all('/<li>(.*?)<\/li>/is', $rawMission, $matches);
    $parsedMissions = !empty($matches[1]) ? $matches[1] : [];

    if (empty($parsedMissions)) {
        $cleanRaw = trim(strip_tags($rawMission));
        if (!empty($cleanRaw)) {
            $lines = preg_split('/(\r\n|\n|\r)/', $cleanRaw);
            foreach ($lines as $line) {
                $trimmed = trim(preg_replace('/^\d+[\.\)]\s*/', '', trim($line)));
                if (!empty($trimmed)) {
                    $parsedMissions[] = $trimmed;
                }
            }
        }
    }

    if (empty($parsedMissions)) {
        $parsedMissions = [
            'Meningkatkan kualitas pelayanan administrasi kependudukan secara cepat dan tepat sasaran.',
            'Mendorong transparansi pengelolaan informasi dan dana pembangunan kelurahan.',
            'Mengembangkan pemberdayaan ekonomi warga berbasis kemitraan daerah.'
        ];
    }

    $villageName = $villageProfile['village_name'] ?? 'Kelurahan Semampir';
    $subdistrict = $villageProfile['subdistrict'] ?? 'Kecamatan Kraksaan';
@endphp

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200/90 relative min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-1">Visi & Misi Pembangunan</h1>
                <p class="text-xs sm:text-sm text-slate-500">Arah kebijakan, cita-cita, dan komitmen pelayanan Pemerintah {{ $villageName }}.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200/90 hover:bg-slate-100 text-slate-700 hover:text-slate-900 rounded-xl font-bold text-xs transition-all shadow-xs shrink-0 active:scale-95">
                <i class="fas fa-arrow-left text-slate-400 text-[11px]"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        {{-- Main Container Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-6 sm:p-8 space-y-7">
            
            {{-- Header Title Inside Card --}}
            <div class="border-b border-slate-100 pb-5">
                <span class="inline-flex items-center gap-2 text-slate-700 text-[11px] font-bold uppercase tracking-wider px-3 py-1 bg-slate-100 rounded-full border border-slate-200 mb-2.5">
                    <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                    Pedoman Pembangunan Kelurahan
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                    Visi & Misi {{ $villageName }}, {{ $subdistrict }}
                </h2>
            </div>

            {{-- 1. VISI CARD (Clean & Elegant, Non-Dark) --}}
            <div class="bg-slate-50/80 rounded-2xl p-6 sm:p-7 border border-slate-200/90 border-l-4 border-l-slate-700 relative overflow-hidden">
                <div class="flex items-center gap-2 text-slate-700 font-extrabold text-xs uppercase tracking-wider mb-2.5">
                    <i class="fas fa-bullseye text-slate-700"></i>
                    <span>VISI {{ strtoupper($villageName) }}</span>
                </div>
                <div class="text-base sm:text-lg font-bold text-slate-900 leading-relaxed font-serif italic">
                    “{{ $cleanVision }}”
                </div>
            </div>

            {{-- 2. MISI LIST (Clean Numbered Items) --}}
            <div class="space-y-4 pt-1">
                <h3 class="font-extrabold text-sm sm:text-base text-slate-900 flex items-center gap-2 pb-2.5 border-b border-slate-100">
                    <i class="fas fa-list-check text-slate-600"></i>
                    <span>MISI PEMBANGUNAN {{ strtoupper(str_replace('Kelurahan ', '', $villageName)) }}</span>
                </h3>

                <div class="space-y-3">
                    @foreach($parsedMissions as $idx => $misi)
                        <div class="flex items-start gap-3.5 p-4 rounded-xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-slate-300 transition-all">
                            <span class="w-7 h-7 rounded-lg bg-slate-700 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-2xs">
                                {{ $idx + 1 }}
                            </span>
                            <div class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed pt-0.5">
                                {!! $misi !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</section>

@endsection
