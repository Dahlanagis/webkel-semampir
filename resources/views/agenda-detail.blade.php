@extends('layouts.app')

@section('title', $agenda->title . ' - Agenda ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full pt-8 pb-20 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Breadcrumb & Back --}}
        <div class="flex items-center justify-between gap-4">
            <nav class="flex items-center gap-2 text-xs text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition">Beranda</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <a href="{{ route('agenda') }}" class="hover:text-slate-900 transition">Agenda Kegiatan</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span class="text-slate-900 font-bold truncate max-w-[200px]">{{ $agenda->title }}</span>
            </nav>

            <a href="{{ route('agenda') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-xs transition shadow-2xs shrink-0">
                <i class="fas fa-arrow-left text-slate-400 text-xs"></i>
                <span>Kembali ke Daftar Agenda</span>
            </a>
        </div>

        {{-- Main Detail Card --}}
        @php
            $eventDate = \Carbon\Carbon::parse($agenda->date);
            $badge = $agenda->status_badge;
        @endphp
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200 space-y-8">
            
            {{-- Top Header Block --}}
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="flex items-start gap-4 sm:gap-5">
                    <div class="flex flex-col items-center justify-center w-16 h-18 bg-slate-900 text-white rounded-2xl shadow-xs shrink-0">
                        <span class="text-2xl font-black leading-none">{{ $eventDate->format('d') }}</span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider mt-1 text-amber-400">{{ $eventDate->translatedFormat('M Y') }}</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($agenda->category)
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $agenda->category->name }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $badge['class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                <span>{{ $badge['label'] }}</span>
                            </span>
                        </div>

                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 leading-tight">
                            {{ $agenda->title }}
                        </h1>
                    </div>
                </div>
            </div>

            {{-- Metadata Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-5 bg-slate-50/70 rounded-2xl border border-slate-200 text-xs">
                <!-- Tanggal -->
                <div class="space-y-1">
                    <span class="text-slate-400 text-[10px] uppercase font-bold flex items-center gap-1.5">
                        <i class="far fa-calendar-alt text-amber-500"></i>
                        <span>Tanggal Kegiatan</span>
                    </span>
                    <p class="font-bold text-slate-900">{{ $eventDate->translatedFormat('l, d F Y') }}</p>
                </div>

                <!-- Waktu -->
                <div class="space-y-1">
                    <span class="text-slate-400 text-[10px] uppercase font-bold flex items-center gap-1.5">
                        <i class="far fa-clock text-amber-500"></i>
                        <span>Waktu / Pukul</span>
                    </span>
                    <p class="font-bold text-slate-900">
                        @php
                            $start = trim($agenda->time_start ?? '08:00');
                            $cleanS = preg_replace('/(?i)\s*wib\b/', '', $start);
                            if (!empty($agenda->time_end)) {
                                $cleanE = preg_replace('/(?i)\s*wib\b/', '', trim($agenda->time_end));
                                echo $cleanS . ' - ' . $cleanE . ' WIB';
                            } else {
                                echo $cleanS . ' WIB';
                            }
                        @endphp
                    </p>
                </div>

                <!-- Tempat / Lokasi -->
                <div class="space-y-1">
                    <span class="text-slate-400 text-[10px] uppercase font-bold flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-rose-500"></i>
                        <span>Lokasi / Tempat</span>
                    </span>
                    <p class="font-bold text-slate-900">{{ $agenda->location }}</p>
                </div>

                <!-- Penyelenggara -->
                <div class="space-y-1">
                    <span class="text-slate-400 text-[10px] uppercase font-bold flex items-center gap-1.5">
                        <i class="fas fa-users text-blue-500"></i>
                        <span>Penyelenggara</span>
                    </span>
                    <p class="font-bold text-slate-900">{{ $agenda->organizer ?? 'Kelurahan Semampir' }}</p>
                </div>
            </div>

            {{-- Deskripsi Kegiatan --}}
            <div class="space-y-3">
                <h3 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-align-left text-slate-400 text-xs"></i>
                    <span>Rincian & Informasi Kegiatan</span>
                </h3>

                @if($agenda->description)
                    <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line p-5 bg-white border border-slate-200 rounded-2xl">
                        {{ $agenda->description }}
                    </div>
                @else
                    <div class="p-5 bg-slate-50 text-slate-500 rounded-2xl border border-slate-200 text-xs italic">
                        Tidak ada rincian atau keterangan tambahan untuk agenda kegiatan ini.
                    </div>
                @endif
            </div>

            @if($agenda->coordinator)
                <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200/80 flex items-center gap-3 text-xs text-amber-900">
                    <i class="fas fa-info-circle text-amber-600 text-base shrink-0"></i>
                    <div>
                        <span class="font-bold block">Narahubung & Koordinator Acara:</span>
                        <span>{{ $agenda->coordinator }}</span>
                    </div>
                </div>
            @endif

        </div>

        {{-- Agenda Terkait / Mendatang Lainnya --}}
        @if($relatedAgendas->count() > 0)
            <div class="pt-6 space-y-4">
                <h3 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">
                    Agenda Kegiatan Mendatang Lainnya
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($relatedAgendas as $item)
                        @php
                            $relDate = \Carbon\Carbon::parse($item->date);
                        @endphp
                        <a href="{{ route('agenda.show', $item->slug) }}" class="p-4 bg-white rounded-2xl border border-slate-200 hover:border-slate-400 shadow-2xs hover:shadow-xs transition flex items-start gap-3.5 group">
                            <div class="flex flex-col items-center justify-center w-11 h-12 bg-slate-100 rounded-xl text-slate-800 shrink-0 group-hover:bg-slate-900 group-hover:text-white transition">
                                <span class="text-sm font-black">{{ $relDate->format('d') }}</span>
                                <span class="text-[9px] font-bold uppercase">{{ $relDate->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-slate-900 group-hover:text-amber-600 transition truncate">{{ $item->title }}</h4>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5"><i class="fas fa-map-marker-alt text-rose-500 text-[9px] mr-1"></i>{{ $item->location }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</section>

@endsection
