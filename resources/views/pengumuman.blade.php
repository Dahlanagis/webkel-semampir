@extends('layouts.app')

@section('title', 'Pengumuman Publik - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Pengumuman & Informasi Publik</h1>
                <p class="text-sm text-slate-500">Pusat informasi, peringatan, dan pengumuman resmi terbaru dari Kelurahan Semampir.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm shrink-0">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        {{-- Main Full Width Container --}}
        <div class="w-full space-y-6">

            {{-- Filter Pill Header Bar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex items-center gap-2 overflow-x-auto no-scrollbar">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1.5">
                    <i class="fas fa-filter text-slate-600"></i> Kategori:
                </span>

                {{-- Semua Pengumuman --}}
                <a href="{{ route('pengumuman') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0
                          {{ !request('kategori') ? 'bg-slate-600 text-white shadow-md' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
                    <span>Semua Pengumuman</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ !request('kategori') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                        {{ $announcements->total() }}
                    </span>
                </a>

                {{-- Categories --}}
                @foreach($categories as $cat)
                    @php
                        $isActive = request('kategori') === $cat->name;
                    @endphp
                    <a href="{{ route('pengumuman', ['kategori' => $cat->name]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0
                              {{ $isActive ? 'bg-slate-600 text-white shadow-md' : 'text-slate-600 bg-slate-200 hover:bg-slate-300' }}">
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Announcements Grid --}}
            @if($announcements->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($announcements as $ann)
                        <article class="bg-white rounded-2xl shadow-sm border {{ $ann->is_urgent ? 'border-rose-200' : 'border-slate-200' }} overflow-hidden flex flex-col hover:shadow-md transition duration-200 h-full p-5 sm:p-6">
                            
                            {{-- Card Header --}}
                            <div class="flex items-start justify-between gap-4 mb-4 border-b border-slate-100 pb-4">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg leading-snug">
                                        {{ $ann->title }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold mt-2">
                                        <span><i class="far fa-calendar-alt mr-1"></i>{{ $ann->created_at->locale('id')->isoFormat('D MMMM Y') }}</span>
                                        @if($ann->category)
                                            <span>•</span>
                                            <span class="text-slate-500">{{ $ann->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Badge --}}
                                @php
                                    $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                    if($ann->badge_type == 'danger' || $ann->is_urgent) $badgeClass = 'bg-rose-100 text-rose-700 border-rose-200';
                                    elseif($ann->badge_type == 'warning') $badgeClass = 'bg-amber-100 text-amber-700 border-amber-200';
                                    elseif($ann->badge_type == 'info') $badgeClass = 'bg-sky-100 text-sky-700 border-sky-200';
                                @endphp
                                <span class="px-2.5 py-1 border rounded text-[10px] font-black uppercase tracking-wider shrink-0 {{ $badgeClass }}">
                                    {{ $ann->badge_type ?? 'INFO' }}
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="flex-1">
                                <div class="text-sm text-slate-600 leading-relaxed max-w-none prose prose-sm prose-slate">
                                    {!! nl2br(e($ann->content)) !!}
                                </div>
                            </div>

                            {{-- Card Footer --}}
                            @if($ann->link_url)
                                <div class="pt-4 mt-4 border-t border-slate-100">
                                    <a href="{{ $ann->link_url }}" target="_blank"
                                       class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl transition shadow-sm">
                                        <span>Buka Tautan Lampiran</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            @endif

                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8 flex justify-center">
                    {{ $announcements->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-200">
                        <i class="fas fa-bullhorn text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Pengumuman</h3>
                    <p class="text-sm text-slate-500 mb-4">Saat ini tidak ada pengumuman resmi yang diterbitkan.</p>
                </div>
            @endif

        </div>

    </div>
</section>

@endsection
