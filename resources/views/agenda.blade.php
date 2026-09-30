@extends('layouts.app')

@section('title', 'Agenda Kegiatan - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full pt-8 pb-20 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-2">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-slate-900 transition">Beranda</a>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <span class="text-slate-600 font-semibold">Informasi Publik</span>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <span class="text-slate-900 font-bold">Agenda Kegiatan</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shadow-xs">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <span>Agenda & Jadwal Kegiatan</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Jadwal resmi rapat kedinasan, sosialisasi, kerja bakti, posyandu, dan agenda kegiatan kemasyarakatan Kelurahan Semampir.
                </p>
            </div>

            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-xs transition-all shadow-xs w-fit">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        {{-- Filter Toolbar & Tabs --}}
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200 space-y-5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                {{-- Tabs Filter --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
                    <a href="{{ route('agenda', array_merge(request()->except('tab', 'page'), ['tab' => 'upcoming'])) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ ($tab ?? 'upcoming') === 'upcoming' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                        <i class="fas fa-hourglass-start text-[11px]"></i>
                        <span>Akan Datang</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ ($tab ?? 'upcoming') === 'upcoming' ? 'bg-slate-800 text-slate-200' : 'bg-slate-200 text-slate-700' }}">{{ $upcomingCount }}</span>
                    </a>

                    <a href="{{ route('agenda', array_merge(request()->except('tab', 'page'), ['tab' => 'this_month'])) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ ($tab ?? '') === 'this_month' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                        <i class="fas fa-calendar-day text-[11px]"></i>
                        <span>Bulan Ini</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ ($tab ?? '') === 'this_month' ? 'bg-slate-800 text-slate-200' : 'bg-slate-200 text-slate-700' }}">{{ $thisMonthCount }}</span>
                    </a>

                    <a href="{{ route('agenda', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ ($tab ?? '') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                        <i class="fas fa-list text-[11px]"></i>
                        <span>Semua Agenda</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ ($tab ?? '') === 'all' ? 'bg-slate-800 text-slate-200' : 'bg-slate-200 text-slate-700' }}">{{ $totalCount }}</span>
                    </a>

                    <a href="{{ route('agenda', array_merge(request()->except('tab', 'page'), ['tab' => 'completed'])) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ ($tab ?? '') === 'completed' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                        <i class="fas fa-check-circle text-[11px]"></i>
                        <span>Selesai Dilaksanakan</span>
                    </a>
                </div>

                {{-- Search & Category Filter Form --}}
                <form method="GET" action="{{ route('agenda') }}" class="flex items-center gap-2.5 w-full lg:w-auto">
                    <input type="hidden" name="tab" value="{{ $tab ?? 'upcoming' }}">
                    
                    @if($categories->count() > 0)
                        <select name="category" onchange="this.form.submit()" 
                                class="py-2 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-slate-900 cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <div class="relative flex-1 lg:w-64">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / lokasi acara..." 
                               class="w-full pl-8 pr-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-slate-900">
                        <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>

                    <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Cari
                    </button>
                </form>
            </div>

            {{-- Grid of Agenda Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pt-2">
                @forelse($agendas as $agenda)
                    @php
                        $eventDate = \Carbon\Carbon::parse($agenda->date);
                        $badge = $agenda->status_badge;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200 hover:border-slate-400 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                        
                        <div class="p-5 space-y-4">
                            {{-- Header Card: Date Tile + Status --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-center justify-center w-12 h-14 bg-slate-900 text-white rounded-xl shadow-xs shrink-0 group-hover:bg-amber-500 transition-colors duration-200">
                                        <span class="text-lg font-black leading-none">{{ $eventDate->format('d') }}</span>
                                        <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5">{{ $eventDate->translatedFormat('M') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-bold text-slate-700 block leading-tight">
                                            {{ $eventDate->translatedFormat('l, d F Y') }}
                                        </span>
                                        @if($agenda->time_start)
                                            <span class="text-[10px] text-slate-500 font-semibold flex items-center gap-1 mt-0.5">
                                                <i class="far fa-clock text-[9px] text-amber-500"></i>
                                                {{ $agenda->time_start }} {{ $agenda->time_end ? '- ' . $agenda->time_end : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold border shrink-0 {{ $badge['class'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </div>

                            {{-- Title & Category --}}
                            <div class="space-y-1.5">
                                @if($agenda->category)
                                    <span class="inline-block text-[9px] font-extrabold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $agenda->category->name }}
                                    </span>
                                @endif
                                <h3 class="font-extrabold text-sm text-slate-900 group-hover:text-amber-600 transition leading-snug">
                                    {{ $agenda->title }}
                                </h3>
                                @if($agenda->description)
                                    <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $agenda->description }}
                                    </p>
                                @endif
                            </div>

                            {{-- Event Details: Location & Organizer --}}
                            <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs text-slate-600">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-map-marker-alt text-rose-500 text-[11px] mt-0.5 shrink-0"></i>
                                    <span class="font-medium text-[11px] text-slate-700 leading-tight">{{ $agenda->location }}</span>
                                </div>
                                @if($agenda->organizer)
                                    <div class="flex items-center gap-2 text-[10px] text-slate-500">
                                        <i class="fas fa-users text-slate-400 text-[10px] shrink-0"></i>
                                        <span class="truncate">Penyelenggara: <strong class="text-slate-700 font-semibold">{{ $agenda->organizer }}</strong></span>
                                    </div>
                                @endif
                                @if($agenda->coordinator)
                                    <div class="flex items-center gap-2 text-[10px] text-slate-500">
                                        <i class="fas fa-user-tie text-slate-400 text-[10px] shrink-0"></i>
                                        <span class="truncate">Narahubung: {{ $agenda->coordinator }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="bg-slate-50 px-5 py-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-medium">Kegiatan Kelurahan</span>
                            <a href="{{ route('agenda.show', $agenda->slug) }}" class="font-bold text-slate-900 hover:text-amber-600 transition flex items-center gap-1 group/btn">
                                <span>Lihat Rincian</span>
                                <i class="fas fa-arrow-right text-[9px] group-hover/btn:translate-x-0.5 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="max-w-md mx-auto space-y-3">
                            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h3 class="font-extrabold text-base text-slate-800">Tidak Ada Agenda Kegiatan</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Saat ini tidak ada agenda kegiatan kelurahan yang sesuai dengan kategori atau kriteria yang Anda cari. Silakan pilih tab atau bersihkan filter pencarian.
                            </p>
                            @if(request('q') || request('category') || request('tab') !== 'upcoming')
                                <a href="{{ route('agenda') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-xs mt-2">
                                    <i class="fas fa-redo-alt text-xs"></i>
                                    <span>Tampilkan Semua Agenda</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            @if($agendas->hasPages())
                <div class="pt-6 border-t border-slate-100">
                    {{ $agendas->links() }}
                </div>
            @endif

        </div>

    </div>
</section>

@endsection
