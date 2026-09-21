@extends('layouts.app')

@section('title', $post->title . ' - ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('meta_description', $post->excerpt)

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Detail Berita & Informasi</h1>
                <p class="text-sm text-slate-500">Informasi resmi Pemerintah Kelurahan Patokan, Kecamatan Kraksaan.</p>
            </div>
            <a href="{{ route('berita') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm shrink-0">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Berita</span>
            </a>
        </div>

        {{-- 2-Column Layout --}}
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

            {{-- KOLOM KIRI: Main Article Card & Info (70%) --}}
            <div class="w-full lg:w-2/3 space-y-6">

                {{-- Article Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    
                    {{-- Toolbar / Meta Header --}}
                    <div class="bg-white px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider">
                                {{ $post->category->name ?? 'Informasi' }}
                            </span>
                            <span class="text-xs text-slate-400 font-semibold">
                                <i class="far fa-calendar-alt text-emerald-600 mr-1"></i>
                                {{ $post->published_at ? $post->published_at->locale('id')->isoFormat('dddd, D MMMM Y') : $post->created_at->isoFormat('D MMMM Y') }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-3 text-xs text-slate-500 font-medium">
                            @if($post->author)
                                <span><i class="fas fa-user-circle text-emerald-600 mr-1"></i>{{ $post->author }}</span>
                                <span>•</span>
                            @endif
                            <span><i class="far fa-eye mr-1"></i>{{ number_format($post->views) }} views</span>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="p-5 sm:p-6 pb-4">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                            {{ $post->title }}
                        </h1>
                    </div>

                    {{-- Cover Image --}}
                    @if(!empty($post->image))
                        <div class="px-5 sm:px-6">
                            <div class="aspect-[16/9] w-full rounded-xl overflow-hidden bg-slate-900 border border-slate-100">
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endif

                    {{-- Article Content Body --}}
                    <div class="p-5 sm:p-6 text-slate-700 leading-relaxed text-sm space-y-4">
                        {!! nl2br(e($post->content)) !!}
                    </div>

                    {{-- Share Bar Footer --}}
                    <div class="bg-slate-50 px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-share-alt text-emerald-600"></i> Bagikan Artikel Ini:
                        </span>
                        
                        <div class="flex items-center gap-2">
                            <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank" rel="noopener"
                               class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition shadow-sm flex items-center gap-1.5">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                               class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-sm flex items-center gap-1.5">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                        </div>
                    </div>

                </div>

                {{-- Alert / Info Card --}}
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                        <i class="fas fa-info-circle text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">Disclaimer Informasi Publik</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Artikel dan berita ini diterbitkan secara resmi oleh Pemerintah Kelurahan Patokan untuk tujuan transparansi publik dan penyampaian kabar daerah. Dilarang menyalin atau menduplikasi tanpa mencantumkan sumber resmi.
                        </p>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: Berita Terkait & Bantuan (30%) --}}
            <div class="w-full lg:w-1/3 space-y-6">

                {{-- Card Berita Terkait --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="bg-white px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <h3 class="font-bold text-slate-800 text-sm">Berita Terkait</h3>
                        </div>
                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-100">
                            {{ count($relatedPosts) }} Artikel
                        </span>
                    </div>

                    <div class="p-2 divide-y divide-slate-100">
                        @forelse($relatedPosts as $related)
                            <a href="{{ route('berita.detail', $related->slug) }}" class="p-3 flex gap-3 rounded-xl hover:bg-slate-50 transition group block">
                                <div class="w-16 h-14 rounded-lg overflow-hidden bg-slate-900 shrink-0">
                                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-xs text-slate-800 group-hover:text-emerald-600 transition line-clamp-2 leading-snug">
                                        {{ $related->title }}
                                    </h4>
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        {{ $related->published_at ? $related->published_at->locale('id')->isoFormat('D MMM Y') : '' }}
                                    </p>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400 p-4 text-center">Belum ada berita terkait.</p>
                        @endforelse
                    </div>

                    <div class="bg-slate-50 px-5 py-3 border-t border-slate-100 flex justify-center">
                        <a href="{{ route('berita') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                            <span>Lihat Semua Berita</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

                {{-- Card Bantuan & Informasi (Identical to Standar Pelayanan) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-headset text-emerald-600 bg-emerald-50 w-8 h-8 rounded-lg flex items-center justify-center"></i>
                        <h3 class="font-bold text-slate-800 text-sm">Bantuan & Informasi</h3>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-relaxed mb-4">
                        Butuh konfirmasi atau informasi lebih lanjut mengenai pengumuman & berita kelurahan? Silakan hubungi kami via WhatsApp.
                    </p>
                    <a href="https://wa.me/6281234567890" target="_blank"
                       class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>Hubungi WhatsApp CS</span>
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
