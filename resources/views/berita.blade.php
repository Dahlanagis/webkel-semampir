@extends('layouts.app')

@section('title', 'Berita & Informasi - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Berita & Kabar Kelurahan</h1>
                <p class="text-sm text-slate-500">Kabar terkini seputar pelayanan, kegiatan, pembangunan, dan pengumuman Kelurahan Semampir.</p>
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
                    <i class="fas fa-filter text-slate-600"></i> Filter Topik:
                </span>

                {{-- Semua Berita --}}
                <a href="{{ route('berita') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0
                          {{ !request('kategori') ? 'bg-slate-600 text-white shadow-md' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
                    <span>Semua Berita</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ !request('kategori') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                        {{ $posts->total() }}
                    </span>
                </a>

                {{-- Categories --}}
                @foreach($categories as $cat)
                    @php
                        $isActive = request('kategori') === $cat->slug;
                    @endphp
                    <a href="{{ route('berita', ['kategori' => $cat->slug]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0
                              {{ $isActive ? 'bg-slate-600 text-white shadow-md' : 'text-slate-600 bg-slate-200 hover:bg-slate-300' }}">
                        <span>{{ $cat->name }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                            {{ $cat->posts_count }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Articles Grid --}}
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <article class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col group hover:shadow-md transition duration-200 h-full">
                            
                            {{-- Thumbnail --}}
                            <a href="{{ route('berita.detail', $post->slug) }}" class="h-48 sm:h-52 relative overflow-hidden bg-slate-900 block w-full shrink-0">
                                <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider absolute top-3 left-3 z-10 shadow-sm">
                                    {{ $post->category->name ?? 'Informasi' }}
                                </span>
                                <img src="{{ $post->image_url }}"
                                     alt="{{ $post->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </a>

                            {{-- Card Body --}}
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 font-semibold mb-2">
                                        <span><i class="far fa-calendar-alt text-slate-600 mr-1"></i>{{ $post->published_at ? $post->published_at->locale('id')->isoFormat('D MMM Y') : $post->created_at->isoFormat('D MMM Y') }}</span>
                                        <span>•</span>
                                        <span><i class="far fa-eye mr-1"></i>{{ number_format($post->views) }} views</span>
                                    </div>

                                    <h3 class="font-bold text-slate-800 text-sm group-hover:text-slate-600 transition line-clamp-2 leading-snug">
                                        <a href="{{ route('berita.detail', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>

                                    <p class="text-xs text-slate-500 line-clamp-2 mt-2 leading-relaxed">
                                        {{ $post->excerpt }}
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <a href="{{ route('berita.detail', $post->slug) }}"
                                       class="text-xs font-bold text-slate-600 hover:text-slate-700 transition flex items-center gap-1.5">
                                        <span>Baca Selengkapnya</span>
                                        <i class="fas fa-chevron-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>

                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6 flex justify-center">
                    {{ $posts->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-200">
                        <i class="fas fa-newspaper text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Berita</h3>
                    <p class="text-sm text-slate-500 mb-4">Belum ada berita atau artikel yang diterbitkan untuk kategori ini.</p>
                    <a href="{{ route('berita') }}" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition inline-block shadow-sm">
                        Lihat Semua Berita
                    </a>
                </div>
            @endif

        </div>

    </div>
</section>

@endsection
