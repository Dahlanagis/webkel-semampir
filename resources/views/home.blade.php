@extends('layouts.app')

@section('title', 'Beranda - Portal Resmi ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div x-data="{ 
    // Modal States
    recapModalOpen: false,
    maklumatModalOpen: false,
    lightboxOpen: false,
    statModalOpen: false,
    showDetailStats: false,
    activePhoto: null,
    videoModalOpen: false,
    activeVideo: null,
    activeStat: 'penduduk',
    activeGalleryTab: 'foto',

    // Methods
    openPhotoLightbox(photo) {
        this.activePhoto = photo;
        this.lightboxOpen = true;
    },
    openVideoPlayer(video) {
        this.activeVideo = video;
        this.videoModalOpen = true;
    },
    getVideoEmbed(video) {
        if(!video) return '';
        let url = video.youtube_id || video.video_url || video.id || '';
        let match = ('' + url).match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^&?\/\s]{11})/i);
        let id = match ? match[1] : url;
        return 'https://www.youtube.com/embed/' + id + '?autoplay=1';
    },
    getVideoWatchUrl(video) {
        if(!video) return '#';
        let url = video.youtube_id || video.video_url || video.id || '';
        let match = ('' + url).match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^&?\/\s]{11})/i);
        let id = match ? match[1] : url;
        return 'https://www.youtube.com/watch?v=' + id;
    }
}" 
class="relative overflow-x-hidden w-full max-w-full">

    <!-- ========================================================================= -->
    <!-- 1. HERO SLIDER BANNER                                                     -->
    <!-- ========================================================================= -->
    <section 
        x-data="{ 
            currentSlide: 0, 
            slides: {{ $sliderPosts->count() > 0 ? $sliderPosts->count() + 1 : 1 }},
            init() {
                if(this.slides > 1) {
                    setInterval(() => {
                        this.currentSlide = (this.currentSlide + 1) % this.slides;
                    }, 5000);
                }
            }
        }"
        class="relative bg-slate-900 min-h-[500px] lg:min-h-[580px] flex items-center overflow-hidden w-full max-w-full"
    >
        <!-- Slide 0: Static Hero (Selamat Datang) -->
        <div x-show="currentSlide === 0" 
             x-transition:enter="transition ease-in-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in-out duration-1000"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 w-full h-full"
             style="display: block;"
        >
            <img src="{{ !empty($villageProfile['hero_image']) ? asset('storage/' . $villageProfile['hero_image']) : 'https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=1600&q=80' }}"
                 alt="Banner Selamat Datang"
                 class="absolute inset-0 w-full h-full object-cover object-center">
            
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/60 to-transparent"></div>

            <div class="relative z-10 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center py-16">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold uppercase tracking-wider mb-4 shadow-sm backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-slate-400 animate-pulse"></span>
                        <span>PORTAL RESMI KELURAHAN</span>
                    </div>
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black leading-tight tracking-tight mb-4 drop-shadow-lg text-white">
                        Selamat Datang di <br/>
                        <span class="text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.5)]">{{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}</span>
                    </h1>
                    <p class="text-white text-base sm:text-lg leading-relaxed mb-8 font-semibold drop-shadow-md max-w-lg">
                        Pusat pelayanan kependudukan mandiri, informasi publik, & transparansi APBD. Kami siap melayani Anda dengan sepenuh hati.
                    </p>
                </div>
            </div>
        </div>

        @if($sliderPosts->count() > 0)
            <!-- Slides 1..N: News Slider Posts -->
            @foreach($sliderPosts as $index => $post)
                <div x-show="currentSlide === {{ $index + 1 }}" 
                     x-transition:enter="transition ease-in-out duration-1000"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in-out duration-1000"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 w-full h-full"
                     style="display: none;"
                >
                    <!-- Background Image -->
                    <img src="{{ str_starts_with($post->image, 'http') ? $post->image : asset('storage/' . $post->image) }}"
                         alt="{{ $post->title }}"
                         class="absolute inset-0 w-full h-full object-cover object-center">
                    
                    <!-- Dark Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-900/60 to-transparent"></div>

                    <!-- Slide Content Overlay -->
                    <div class="relative z-10 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center py-16">
                        <div class="max-w-3xl">
                            <!-- Badge -->
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold uppercase tracking-wider mb-4 shadow-sm backdrop-blur-md">
                                <span class="w-2 h-2 rounded-full bg-{{ $post->category->color_code ?? 'slate' }}-400 animate-pulse"></span>
                                <span>{{ $post->category->name ?? 'INFORMASI' }}</span>
                            </div>

                            <!-- Slide Title -->
                            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black leading-tight tracking-tight mb-4 drop-shadow-lg text-white">
                                {{ $post->title }}
                            </h1>

                            <!-- Slide Subtitle / Excerpt -->
                            <p class="text-white text-base sm:text-lg leading-relaxed mb-8 font-semibold drop-shadow-md max-w-xl line-clamp-2">
                                {{ Str::limit(strip_tags($post->content), 120) }}
                            </p>
                            
                            <a href="{{ route('berita.detail', $post->slug) }}" class="inline-flex items-center gap-2 px-5 py-2 bg-slate-600 hover:bg-slate-500 text-white text-sm font-semibold rounded-full shadow-md transition mt-4">
                                Baca Selengkapnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Indicators -->
            <div class="absolute bottom-8 left-0 right-0 z-20 flex justify-center gap-2">
                <!-- Indicator for Slide 0 -->
                <button @click="currentSlide = 0" 
                        :class="{'w-8 bg-slate-500': currentSlide === 0, 'w-2 bg-white/50 hover:bg-white/80': currentSlide !== 0}"
                        class="h-2 rounded-full transition-all duration-300"></button>
                
                <!-- Indicators for News Slides -->
                @foreach($sliderPosts as $index => $post)
                    <button @click="currentSlide = {{ $index + 1 }}" 
                            :class="{'w-8 bg-slate-500': currentSlide === {{ $index + 1 }}, 'w-2 bg-white/50 hover:bg-white/80': currentSlide !== {{ $index + 1 }}}"
                            class="h-2 rounded-full transition-all duration-300"></button>
                @endforeach
            </div>
        @endif
    </section>

    <!-- ========================================================================= -->
    <!-- 2. SEKSI SAMBUTAN LURAH / KEPALA INSTANSI                                 -->
    <!-- ========================================================================= -->
    <section class="w-full py-16 lg:py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="w-full grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-20 items-center">
                
                <!-- Left: Foto Lurah / Pimpinan -->
                <div class="w-full lg:col-span-1 flex justify-center">
                    <div class="relative w-56 sm:w-64 lg:w-72">
                        <img src="{{ !empty($villageProfile['head_photo']) ? (str_starts_with($villageProfile['head_photo'], 'http') || str_starts_with($villageProfile['head_photo'], 'data:image') ? $villageProfile['head_photo'] : asset('storage/' . ltrim($villageProfile['head_photo'], '/')) . '?v=' . time()) : asset('images/sotk/lurah.png') }}"
                             alt="Foto Kepala {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}"
                             onerror="this.onerror=null; this.src='{{ asset('images/sotk/lurah.png') }}';"
                             class="w-full aspect-[4/5] object-cover object-top rounded-3xl shadow-sm bg-slate-100">

                        <!-- Floating Name Badge -->
                        <div class="absolute -bottom-6 inset-x-4 sm:inset-x-6 bg-white py-4 px-3 sm:py-5 sm:px-4 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] text-center">
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 leading-tight">{{ $villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.' }}</h3>
                            <p class="text-[9px] sm:text-[10px] text-slate-600 font-bold uppercase tracking-wider mt-1 sm:mt-2">Kepala {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Sambutan Resmi -->
                <div class="w-full lg:col-span-2 space-y-5 text-center min-w-0 mt-8 lg:mt-0">
                    <h4 class="text-slate-600 text-xs sm:text-sm font-bold uppercase tracking-wide">
                        SAMBUTAN KEPALA KELURAHAN
                    </h4>

                    <h2 class="text-3xl sm:text-4xl lg:text-[42px] font-extrabold text-slate-900 leading-[1.15] tracking-tight">
                        {{ $villageProfile['welcome_title'] ?? 'Komitmen Pelayanan Publik yang Transparan, Cepat, & Responsif' }}
                    </h2>

                    <div class="text-slate-500 text-sm sm:text-base leading-relaxed space-y-4 max-w-3xl mx-auto pt-2 prose prose-slate max-w-none prose-p:text-slate-500 prose-p:leading-relaxed text-center">
                        {!! $villageProfile['welcome_text'] ?? '<p>Melalui sistem portal terpadu ini, Pemerintah Kelurahan Semampir berkomitmen penuh dalam mewujudkan pelayanan publik modern yang berbasis transparansi, kemudahan akses dokumen mandiri, dan akuntabilitas pengelolaan anggaran.</p><p>Kami terus berinovasi untuk memberikan pelayanan terbaik bagi warga Kraksaan tanpa kerumitan administrasi, ramah, akuntabel, dan 100% bebas dari segala bentuk pungutan liar.</p>' !!}
                    </div>

                    <div class="pt-4 flex flex-wrap items-center justify-start gap-4">
                        <a href="{{ route('visi-misi') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition-colors shadow-sm"
                           style="background-color: #334155 !important; color: #ffffff !important;"
                           onmouseover="this.style.backgroundColor='#1e293b'"
                           onmouseout="this.style.backgroundColor='#334155'">
                            <span>Visi & Misi Kami</span>
                            <i class="fas fa-arrow-right text-xs ml-1 text-white"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 3. BANNER HIGHLIGHT & STATISTIK TRANSPARANSI (Glassmorphism Hijau Gelap)  -->
    <!-- ========================================================================= -->
    <section id="stat-transparansi" class="py-16 sm:py-20 bg-slate-50 border-y border-slate-200 text-slate-900 relative w-full max-w-full">
        <!-- Background subtle glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-7xl h-96 bg-slate-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col gap-12 lg:gap-20">
            
            <!-- Action Buttons Banner Highlight (Modern Abu Tua / Dark Charcoal Theme) -->
            <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 lg:p-10 text-white shadow-2xl border"
                 style="background: linear-gradient(145deg, #22262e 0%, #181b22 50%, #121418 100%); border-color: rgba(255, 255, 255, 0.08); box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);">
                <!-- Glowing Ambient Gradients (Warm & Subtle Neutral) -->
                <div class="absolute -top-24 -left-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-slate-400/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <!-- Subtle Grid Background Overlay -->
                <div class="absolute inset-0 opacity-5 pointer-events-none bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px]"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    <!-- Left Column: Title & Information -->
                    <div class="lg:col-span-6 space-y-4 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/15 backdrop-blur-md text-[11px] font-extrabold uppercase tracking-wider text-slate-200 shadow-sm">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            <span>AKSES CEPAT LAYANAN DIGITAL</span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                            Layanan & Transparansi <br class="hidden sm:inline"/>
                            <span class="text-white">
                                {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}
                            </span>
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl mx-auto lg:mx-0">
                            Akses mudah standar pelayanan publik (SOP), unduh formulir administrasi resmi, serta pantau keterbukaan realisasi anggaran dan transparansi APBD TA {{ $villageProfile['apbd']['year'] ?? '2026' }}.
                        </p>

                        <!-- Feature Badges -->
                        <div class="pt-1 flex flex-wrap items-center justify-center lg:justify-start gap-2.5 text-[11px] font-semibold text-slate-300">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 border border-white/10 backdrop-blur-sm">
                                <i class="fas fa-check-circle text-emerald-400 text-xs"></i> 100% Bebas Biaya
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 border border-white/10 backdrop-blur-sm">
                                <i class="fas fa-bolt text-amber-400 text-xs"></i> Unduh Mandiri 24 Jam
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 border border-white/10 backdrop-blur-sm">
                                <i class="fas fa-shield-alt text-emerald-400 text-xs"></i> Akuntabel & Resmi
                            </span>
                        </div>
                    </div>

                    <!-- Right Column: Two Interactive Action Hub Cards -->
                    <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Card 1: Pusat Unduhan Dokumen -->
                        <a href="{{ route('dokumen') }}" 
                           class="group relative overflow-hidden rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between"
                           style="background: linear-gradient(145deg, #2a2f3a 0%, #1f232b 100%); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-sky-600 to-cyan-500 text-white flex items-center justify-center text-xl shadow-lg shadow-sky-500/20 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <span class="w-8 h-8 rounded-full bg-white/10 group-hover:bg-white group-hover:text-slate-900 text-white flex items-center justify-center text-xs transition-all duration-300 group-hover:translate-x-1">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-white group-hover:text-sky-300 transition-colors">
                                    Pusat Unduhan
                                </h3>
                                <p class="text-xs text-slate-300 mt-1 line-clamp-2 leading-relaxed">
                                    SOP pelayanan, blangko surat permohonan, & regulasi publik.
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-white/10 flex items-center text-[11px] font-extrabold text-sky-300 group-hover:text-sky-200">
                                <span>Buka Dokumen</span>
                                <i class="fas fa-chevron-right text-[10px] ml-1.5 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </a>

                        <!-- Card 2: Transparansi APBD -->
                        <a href="{{ route('transparansi') }}" 
                           class="group relative overflow-hidden rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between"
                           style="background: linear-gradient(145deg, #2a2f3a 0%, #1f232b 100%); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center text-xl shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <span class="w-8 h-8 rounded-full bg-amber-400/20 group-hover:bg-amber-400 group-hover:text-slate-900 text-amber-300 flex items-center justify-center text-xs transition-all duration-300 group-hover:translate-x-1">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-white group-hover:text-amber-300 transition-colors">
                                    Transparansi APBD
                                </h3>
                                <p class="text-xs text-slate-300 mt-1 line-clamp-2 leading-relaxed">
                                    Realisasi pagu anggaran & alokasi Dana Desa TA {{ $villageProfile['apbd']['year'] ?? '2026' }}.
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-amber-400/20 flex items-center text-[11px] font-extrabold text-amber-300 group-hover:text-amber-200">
                                <span>Lihat Laporan Anggaran</span>
                                <i class="fas fa-chevron-right text-[10px] ml-1.5 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </a>

                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- PUSAT DATA & INFORMASI KELURAHAN (STATISTIK WILAYAH)               -->
            <!-- ================================================================= -->
            <div class="w-full space-y-6 sm:space-y-8">
                
                <!-- Section Header (Bersih, Rapi & Sejajar) -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-slate-200">
                    <div>
                        <div class="inline-flex items-center gap-2 mb-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-[11px] font-extrabold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>Pusat Data & Informasi Kelurahan</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Pusat Data & Informasi {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed max-w-2xl">
                            Informasi terpadu indikator wilayah, kependudukan, transparansi anggaran kelurahan, dan penyaluran bantuan sosial bagi masyarakat.
                        </p>
                    </div>

                    <!-- Tombol Portal Transparansi -->
                    <div class="flex items-center shrink-0">
                        <a href="{{ route('transparansi') }}"
                           class="group relative inline-flex items-center gap-2.5 px-5 py-2.5 sm:py-3 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all duration-200 border border-emerald-600/40">
                            <i class="fas fa-chart-pie text-emerald-200"></i>
                            <span>Portal Transparansi & APBD</span>
                            <i class="fas fa-arrow-right text-[10px] text-emerald-200 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- 1. GRID 4 KARTU RINGKASAN UTAMA (CLEAN STATISTIC CARDS) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    
                    <!-- Kartu 1: Total Penduduk -->
                    <div @click="statModalOpen = true; activeStat = 'penduduk'"
                         class="cursor-pointer group relative rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-400 p-5 transition-all duration-200 flex flex-col justify-between hover:-translate-y-0.5">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                                    Total Penduduk
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-baseline gap-1.5">
                                    <span>{{ $regStat ? number_format($regStat->total_penduduk, 0, ',', '.') : ($stats['penduduk'] ?? '8.425') }}</span>
                                    <span class="text-xs font-semibold text-slate-400">Jiwa</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Laki-laki {{ $regStat ? number_format($regStat->jumlah_laki_laki, 0, ',', '.') : '4.180' }} • Perempuan {{ $regStat ? number_format($regStat->jumlah_perempuan, 0, ',', '.') : '4.245' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700 group-hover:text-emerald-800">
                            <span>Lihat Rincian Penduduk</span>
                            <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>

                    <!-- Kartu 2: Kepala Keluarga (KK) -->
                    <div @click="statModalOpen = true; activeStat = 'kk'"
                         class="cursor-pointer group relative rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-400 p-5 transition-all duration-200 flex flex-col justify-between hover:-translate-y-0.5">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                                    Kepala Keluarga (KK)
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-baseline gap-1.5">
                                    <span>{{ $regStat ? number_format($regStat->jumlah_kk, 0, ',', '.') : ($stats['kk'] ?? '2.640') }}</span>
                                    <span class="text-xs font-semibold text-slate-400">KK</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Rata-rata {{ $regStat ? number_format($regStat->rata_rata_jiwa_per_kk, 1, ',', '.') : '1,9' }} jiwa per keluarga
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700 group-hover:text-emerald-800">
                            <span>Lihat Rincian KK</span>
                            <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>

                    <!-- Kartu 3: Anggaran Kelurahan -->
                    <div @click="statModalOpen = true; activeStat = 'anggaran'"
                         class="cursor-pointer group relative rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-400 p-5 transition-all duration-200 flex flex-col justify-between hover:-translate-y-0.5">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                                    Anggaran Kelurahan
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                    Rp 1,58 M
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Serapan kegiatan mencapai 73,0% (TA {{ $villageProfile['apbd']['year'] ?? '2026' }})
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700 group-hover:text-emerald-800">
                            <span>Lihat Rincian Anggaran</span>
                            <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>

                    <!-- Kartu 4: Penerima Bantuan Sosial -->
                    <div @click="statModalOpen = true; activeStat = 'bansos'"
                         class="cursor-pointer group relative rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-400 p-5 transition-all duration-200 flex flex-col justify-between hover:-translate-y-0.5">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                                    Penerima Bansos
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-baseline gap-1.5">
                                    <span>842</span>
                                    <span class="text-xs font-semibold text-slate-400">Keluarga</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    31,9% dari total keluarga terdaftar
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700 group-hover:text-emerald-800">
                            <span>Lihat Data Bansos</span>
                            <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>

                </div>

                <!-- TOMBOL TOGGLE SELENGKAPNYA (DETAIL WILAYAH, ANGGARAN & BANSOS) -->
                <div class="pt-2 flex flex-col items-center justify-center">
                    <button type="button" 
                            @click="showDetailStats = !showDetailStats"
                            class="group inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white hover:bg-emerald-50/70 border border-slate-200/90 hover:border-emerald-300 text-xs sm:text-sm font-bold text-slate-700 hover:text-emerald-800 shadow-2xs hover:shadow-md transition-all duration-200 cursor-pointer">
                        <span class="w-6 h-6 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i class="fas fa-layer-group text-[10px]"></i>
                        </span>
                        <span x-text="showDetailStats ? 'Tutup Rincian Informasi' : 'Lihat Selengkapnya (Wilayah, Anggaran & Bansos)'">
                            Lihat Selengkapnya (Wilayah, Anggaran & Bansos)
                        </span>
                        <i class="fas text-[11px] text-slate-400 group-hover:text-emerald-600 transition-transform duration-200"
                           :class="showDetailStats ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <p x-show="!showDetailStats" class="text-[11px] text-slate-400 mt-2">
                        Klik untuk melihat rincian pembagian RT/RW, realisasi anggaran, dan rincian kuota bansos
                    </p>
                </div>

                <!-- 2. SEKSI INFORMASI DETAIL (3 SEKSI UTAMA) - DISEMBUNYIKAN DI SELENGKAPNYA -->
                <div x-show="showDetailStats" x-collapse x-cloak>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 pt-3">
                        
                        <!-- Seksi 1: Informasi Wilayah (RT/RW & Sarpras) -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-2xs hover:shadow-md transition-shadow flex flex-col justify-between space-y-5">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center text-base">
                                        <i class="fas fa-map-location-dot"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm sm:text-base text-slate-900">Informasi Wilayah</h3>
                                        <p class="text-[11px] text-slate-400">Pembagian RT/RW & fasilitas lingkungan</p>
                                    </div>
                                </div>

                                <div class="space-y-2.5 text-xs">
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Jumlah Rukun Tetangga (RT)</span>
                                        <span class="font-black text-slate-900">{{ $regStat ? $regStat->jumlah_rt : '32' }} RT</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Jumlah Rukun Warga (RW)</span>
                                        <span class="font-black text-slate-900">{{ $regStat ? $regStat->jumlah_rw : '08' }} RW</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Luas Wilayah Administrasi</span>
                                        <span class="font-black text-slate-900">{{ $regStat ? number_format($regStat->luas_wilayah, 2, ',', '.') . ' km²' : '3,82 km²' }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Fasilitas & Sarana Publik</span>
                                        <span class="font-bold text-slate-800 text-[11px]">Sekolah, Masjid & Balai RW</span>
                                    </div>
                                </div>
                            </div>

                            <button type="button" @click="statModalOpen = true; activeStat = 'rtrw'"
                                    class="w-full py-2.5 px-4 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-map-marked-alt text-xs"></i>
                                <span>Lihat Detail Wilayah & RT/RW</span>
                            </button>
                        </div>

                        <!-- Seksi 2: Transparansi Anggaran Kelurahan -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-2xs hover:shadow-md transition-shadow flex flex-col justify-between space-y-5">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center text-base">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm sm:text-base text-slate-900">Transparansi Anggaran</h3>
                                        <p class="text-[11px] text-slate-400">Realisasi penggunaan anggaran kelurahan</p>
                                    </div>
                                </div>

                                <div class="space-y-2.5 text-xs">
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Total Pagu Anggaran TA {{ $villageProfile['apbd']['year'] ?? '2026' }}</span>
                                        <span class="font-black text-slate-900">Rp 1.580.000.000</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 font-medium">Realisasi Anggaran</span>
                                            <span class="font-black text-emerald-700">Rp 1.120.000.000 (73,0%)</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                                            <div class="bg-emerald-600 h-full rounded-full" style="width: 73%"></div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Fokus Penggunaan</span>
                                        <span class="font-bold text-slate-800 text-[11px]">Layanan Warga & Pembangunan</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Status Laporan</span>
                                        <span class="font-bold text-emerald-700 text-[11px]">Terverifikasi & Transparan</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('transparansi') }}"
                               class="w-full py-2.5 px-4 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-chart-pie text-xs"></i>
                                <span>Buka Laporan Anggaran Kelurahan</span>
                            </a>
                        </div>

                        <!-- Seksi 3: Rincian Penerima Bantuan Sosial (Bansos) -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-2xs hover:shadow-md transition-shadow flex flex-col justify-between space-y-5">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center text-base">
                                        <i class="fas fa-hand-holding-heart"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm sm:text-base text-slate-900">Penerima Bantuan Sosial</h3>
                                        <p class="text-[11px] text-slate-400">Jaring pengaman sosial warga penerima manfaat</p>
                                    </div>
                                </div>

                                <div class="space-y-2.5 text-xs">
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Program Keluarga Harapan (PKH)</span>
                                        <span class="font-black text-slate-900">285 Keluarga</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Bantuan Sembako / BPNT</span>
                                        <span class="font-black text-slate-900">340 Keluarga</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Bantuan Langsung Tunai (BLT)</span>
                                        <span class="font-black text-slate-900">120 Keluarga</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Bantuan Daerah & Khusus</span>
                                        <span class="font-black text-slate-900">97 Penerima</span>
                                    </div>
                                </div>
                            </div>

                            <button type="button" @click="statModalOpen = true; activeStat = 'bansos'"
                                    class="w-full py-2.5 px-4 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-users-viewfinder text-xs"></i>
                                <span>Lihat Rincian Bantuan Sosial</span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 4. INFO BERITA TERKINI & SIDEBAR MAKLUMAT PELAYANAN                       -->
    <!-- ========================================================================= -->
    <section class="py-16 sm:py-20 bg-slate-50 border-b border-slate-200 w-full max-w-full">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="w-full grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Left: Berita Terkini (lg:col-span-2) -->
                <div class="w-full lg:col-span-2 min-w-0 flex flex-col justify-between space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-2 mb-2">
                                <div class="w-1.5 h-4 bg-slate-800 rounded-full"></div>
                                <span class="text-xs font-bold text-slate-700 tracking-wider uppercase">
                                    KABAR KELURAHAN
                                </span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                Berita & Informasi Terbaru
                            </h3>
                        </div>

                        <a href="{{ route('berita') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all group shrink-0">
                            <span>Lihat Semua Berita</span>
                            <i class="fas fa-arrow-right text-[10px] text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all"></i>
                        </a>
                    </div>

                    <!-- News Grid (2 Columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8 items-stretch">

                        @if($latestPosts->isEmpty())
                            <div class="col-span-1 sm:col-span-2 p-8 bg-white border border-slate-200 rounded-2xl text-center text-slate-500 text-sm shadow-sm flex flex-col items-center justify-center">
                                <i class="fas fa-newspaper text-3xl text-slate-300 mb-3"></i>
                                Belum ada berita atau informasi terbaru saat ini.
                            </div>
                        @else
                            @foreach($latestPosts as $post)

                        <article class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:border-slate-300 transition-all duration-300 hover:-translate-y-1.5 group flex flex-col justify-between w-full min-w-0 h-full">
                            <div>
                                <!-- Image Thumbnail (Fixed Height) -->
                                <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-900 w-full shrink-0">
                                    <img src="{{ asset($post->image_url ?? ($post->image ? 'storage/'.$post->image : 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?auto=format&fit=crop&w=800&q=80')) }}"
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>

                                    <span class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider border border-white/10 shadow-sm">
                                        {{ $post->category?->name ?? 'BERITA SEMAMPIR' }}
                                    </span>
                                </div>

                                <!-- Body -->
                                <div class="p-5 space-y-2.5">
                                    <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400">
                                        <i class="far fa-calendar-alt text-slate-500"></i>
                                        <span>{{ isset($post->published_at) ? \Carbon\Carbon::parse($post->published_at)->format('d M Y') : '15 Aug 2026' }}</span>
                                    </div>

                                    <h4 class="font-extrabold text-base text-slate-900 group-hover:text-slate-700 transition leading-snug line-clamp-2">
                                        <a href="{{ route('berita.detail', $post->slug ?? '#') }}">{{ $post->title }}</a>
                                    </h4>

                                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">
                                        {{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 110) }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <a href="{{ route('berita.detail', $post->slug ?? '#') }}" class="font-bold text-slate-700 group-hover:text-slate-900 inline-flex items-center gap-1.5 transition-colors">
                                    <span>Baca Selengkapnya</span>
                                </a>
                                <span class="w-7 h-7 rounded-full bg-slate-100 group-hover:bg-slate-800 group-hover:text-white text-slate-400 flex items-center justify-center text-[9px] transition-all group-hover:translate-x-0.5">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </article>
                        @endforeach
                        @endif
                    </div>
                </div>

                <!-- Right: Maklumat Pelayanan Sidebar (lg:col-span-1) -->
                <div class="w-full lg:col-span-1 min-w-0 h-full flex flex-col">
                    <div class="bg-gradient-to-br from-[#1e222a] via-[#16181f] to-[#121418] text-white border border-white/10 rounded-3xl p-6 sm:p-7 shadow-xl relative overflow-hidden flex flex-col justify-between h-full space-y-6 group">
                        <!-- Ambient Subtle Gold Glow -->
                        <div class="absolute -top-16 -right-16 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -right-4 -bottom-4 text-white/[0.03] text-8xl pointer-events-none">
                            <i class="fas fa-shield-alt"></i>
                        </div>

                        <div class="space-y-5 relative z-10">
                            <!-- Top Icon & Official Badge -->
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-600 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg shadow-amber-500/20">
                                    <i class="fas fa-award"></i>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/15 border border-amber-400/25 text-amber-300 text-[10px] font-black uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    RESMI & SAH
                                </span>
                            </div>

                            <div class="space-y-2">
                                <span class="text-amber-400 font-extrabold text-[10px] uppercase tracking-wider">
                                    STANDAR MUTU PELAYANAN
                                </span>
                                <h3 class="text-xl font-black text-white leading-snug">
                                    Maklumat Pelayanan Publik Resmi
                                </h3>
                                <p class="text-xs text-slate-300 leading-relaxed font-medium">
                                    Komitmen penuh seluruh jajaran aparatur {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }} dalam memberikan hak pelayanan terbaik bagi seluruh warga.
                                </p>
                            </div>

                            <!-- Charter Quote Block -->
                            <div class="relative bg-white/5 border border-white/10 rounded-2xl p-4 text-xs text-slate-200 leading-relaxed italic border-l-4 border-l-amber-400 shadow-inner">
                                <p class="relative z-10 pl-1 font-normal line-clamp-3">
                                    "{!! strip_tags($villageProfile['maklumat_text'] ?? 'Dengan ini kami menyatakan sanggup menyelenggarakan pelayanan sesuai standar yang ditetapkan dan siap menerima sanksi apabila melanggar.') !!}"
                                </p>
                            </div>

                            @if(!empty($villageProfile['maklumat_file']))
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-400/10 border border-amber-400/20 text-[11px] font-semibold text-amber-300">
                                    <i class="fas fa-paperclip text-amber-400"></i>
                                    <span>Piagam Resmi & Dokumen SK Terlampir</span>
                                </div>
                            @endif
                        </div>

                        <div class="relative z-10 pt-2">
                            <button type="button" @click="maklumatModalOpen = true"
                                    class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 text-xs font-black rounded-xl transition-all duration-300 flex items-center justify-center gap-2 group/btn">
                                <i class="fas fa-file-contract text-sm"></i>
                                <span>Lihat Naskah Maklumat Resmi</span>
                                <i class="fas fa-arrow-right text-[10px] group-hover/btn:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- 5.5 AGENDA KEGIATAN MENDATANG (MINIMALIST CLEAN & ELEGAN) --}}
    @if(isset($upcomingAgendas) && $upcomingAgendas->count() > 0)
    <section class="py-12 sm:py-16 bg-slate-50/60 border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-5 border-b border-slate-200">
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <i class="far fa-calendar-alt text-slate-600"></i>
                        <span>Jadwal & Agenda Resmi Kelurahan</span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Agenda Kegiatan Mendatang
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                        Rangkaian kegiatan resmi kedinasan, pelayanan masyarakat, dan agenda publik di lingkungan {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}.
                    </p>
                </div>

                <!-- Tombol CTA Semua Agenda -->
                <div class="shrink-0">
                    <a href="{{ route('agenda') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition shadow-xs">
                        <span>Lihat Semua Agenda</span>
                        <i class="fas fa-arrow-right text-[11px]"></i>
                    </a>
                </div>
            </div>

            <!-- Grid Kartu Agenda -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 items-stretch">
                @foreach($upcomingAgendas as $agenda)
                    @php
                        $agDate = \Carbon\Carbon::parse($agenda->date);
                        
                        // Normalisasi waktu agar rapi dan tanpa duplikasi "WIB WIB"
                        $formattedTime = null;
                        if (!empty($agenda->time_start)) {
                            $cleanStart = trim(preg_replace('/(?i)\s*wib\b/', '', $agenda->time_start));
                            if (!empty($agenda->time_end)) {
                                $cleanEnd = trim(preg_replace('/(?i)\s*wib\b/', '', $agenda->time_end));
                                $formattedTime = $cleanStart . ' - ' . $cleanEnd . ' WIB';
                            } else {
                                $formattedTime = $cleanStart . ' WIB';
                            }
                        }

                        // Badge status minimalis dan halus
                        $badgeClass = match($agenda->status) {
                            'ongoing' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'completed' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200/90',
                        };
                        $badgeDot = match($agenda->status) {
                            'ongoing' => 'bg-amber-500 animate-pulse',
                            'completed' => 'bg-slate-400',
                            'cancelled' => 'bg-rose-500',
                            default => 'bg-emerald-500',
                        };
                        $badgeLabel = match($agenda->status) {
                            'ongoing' => 'Sedang Berlangsung',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            default => 'Akan Datang',
                        };
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200 hover:border-slate-300 p-6 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between group">
                        <div class="space-y-4">
                            <!-- Baris Atas: Mini Date Badge + Hari & Waktu + Status -->
                            <div class="flex items-start justify-between gap-3 pb-3.5 border-b border-slate-100">
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Mini Calendar Tile Halus -->
                                    <div class="w-11 h-12 rounded-xl bg-slate-100/90 border border-slate-200/90 flex flex-col items-center justify-center shrink-0">
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 leading-none">
                                            {{ $agDate->translatedFormat('M') }}
                                        </span>
                                        <span class="text-base font-black text-slate-900 leading-none mt-1">
                                            {{ $agDate->format('d') }}
                                        </span>
                                    </div>

                                    <!-- Hari & Jam Terstruktur -->
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-slate-800 leading-tight">
                                            {{ $agDate->translatedFormat('l, d F Y') }}
                                        </div>
                                        @if($formattedTime)
                                            <div class="text-[11px] font-medium text-slate-500 flex items-center gap-1.5 mt-1">
                                                <i class="far fa-clock text-slate-400 text-[10px]"></i>
                                                <span>{{ $formattedTime }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeClass }} shrink-0 tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badgeDot }}"></span>
                                    <span>{{ $badgeLabel }}</span>
                                </span>
                            </div>

                            <!-- Judul & Kategori -->
                            <div class="space-y-2 pt-0.5">
                                @if($agenda->category)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold text-[10px] uppercase tracking-wider">
                                        <i class="fas fa-tag text-[8px] text-slate-400"></i>
                                        <span>{{ $agenda->category->name }}</span>
                                    </span>
                                @endif

                                <h3 class="font-extrabold text-base sm:text-lg text-slate-900 group-hover:text-slate-700 transition leading-snug line-clamp-2">
                                    <a href="{{ route('agenda.show', $agenda->slug) }}">
                                        {{ $agenda->title }}
                                    </a>
                                </h3>

                                <!-- Kotak Lokasi Halus & Rapi -->
                                <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-slate-50 border border-slate-100/90 text-xs text-slate-700">
                                    <i class="fas fa-location-dot text-slate-500 text-xs shrink-0"></i>
                                    <span class="font-medium truncate" title="{{ $agenda->location }}">{{ $agenda->location }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer: Penyelenggara & Tombol Rincian Berbentuk Pill -->
                        <div class="pt-4 mt-5 border-t border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-slate-500 truncate pr-2">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-500 text-[10px] shrink-0">
                                    <i class="fas fa-users-gear"></i>
                                </span>
                                <span class="truncate font-semibold text-slate-600 max-w-[140px] sm:max-w-[170px]" title="{{ $agenda->organizer ?? 'Kelurahan Semampir' }}">
                                    {{ $agenda->organizer ?? 'Kelurahan Semampir' }}
                                </span>
                            </div>

                            <a href="{{ route('agenda.show', $agenda->slug) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-2xs hover:shadow transition-all group-hover:translate-x-0.5 shrink-0">
                                <span>Rincian</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    <!-- ========================================================================= -->
    <!-- ========================================================================= -->
    <!-- 6. DOKUMENTASI TERPADU (GALERI FOTO & VIDEO KEGIATAN) - PERSIS DLH          -->
    <!-- ========================================================================= -->
    <style>
        .dlh-gallery-scroll {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            padding-bottom: 16px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .dlh-gallery-scroll::-webkit-scrollbar {
            display: none;
        }
        .dlh-gallery-item {
            flex: 0 0 calc(33.333% - 12px);
            border-radius: 1.25rem;
            overflow: hidden;
            position: relative;
            height: 320px;
            cursor: pointer;
        }
        @media (max-width: 991px) {
            .dlh-gallery-item {
                flex: 0 0 calc(50% - 9px);
            }
        }
        @media (max-width: 575px) {
            .dlh-gallery-item {
                flex: 0 0 88%;
            }
        }
        .dlh-gallery-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 64px;
            height: 64px;
            background: rgba(251,191,36,.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 1.5rem;
            z-index: 15;
            transition: all .3s;
            box-shadow: 0 6px 24px rgba(0,0,0,.3);
        }
        .dlh-gallery-item:hover .dlh-gallery-play-btn {
            transform: translate(-50%, -50%) scale(1.12);
        }
        .dlh-tab-btn {
            font-weight: 700;
            font-size: 0.875rem;
            color: #475569;
            background-color: #f1f5f9;
            border-radius: 0.75rem;
            padding: 10px 24px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .dlh-tab-btn:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
        .dlh-tab-btn.active {
            background-color: #334155 !important;
            color: #ffffff !important;
            border-color: #1e293b !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.35);
        }
    </style>

    <section class="py-14 sm:py-16 bg-white border-t border-slate-200 text-slate-900 w-full max-w-full block clear-both">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
            
            <!-- HEADER (LABEL VISUAL, TITLE & DESKRIPSI - ABU TUA) -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center gap-2 mb-2 text-slate-700">
                    <span class="inline-block w-5 h-[2px] bg-slate-700"></span>
                    <span class="text-xs font-black uppercase tracking-[2px]">VISUAL</span>
                    <span class="inline-block w-5 h-[2px] bg-slate-700"></span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Galeri Dokumentasi
                </h2>
                <p class="text-slate-500 max-w-lg mx-auto text-xs sm:text-sm mt-2 leading-relaxed">
                    Potret aktivitas pelayanan publik, gotong royong warga, pembangunan, dan program kerja Kelurahan Semampir.
                </p>
            </div>

            <!-- TABS FOTO & VIDEO (TEMA ABU TUA / SLATE) -->
            <div class="flex items-center justify-center gap-3 mb-8">
                <button type="button"
                        @click="activeGalleryTab = 'foto'"
                        class="dlh-tab-btn"
                        :class="{ 'active': activeGalleryTab === 'foto' }"
                        :style="activeGalleryTab === 'foto' ? 'background-color: #334155 !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #475569 !important;'">
                    <i class="fas fa-camera text-sm"></i>
                    <span>Galeri Foto</span>
                </button>

                <button type="button"
                        @click="activeGalleryTab = 'video'"
                        class="dlh-tab-btn"
                        :class="{ 'active': activeGalleryTab === 'video' }"
                        :style="activeGalleryTab === 'video' ? 'background-color: #334155 !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #475569 !important;'">
                    <i class="fas fa-play text-xs"></i>
                    <span>Galeri Video</span>
                </button>
            </div>

            <!-- TAB 1: GALERI FOTO (EXACT 3 ITEMS PER ROW DENGAN SCROLL HORISONTAL - ABU TUA) -->
            <div x-show="activeGalleryTab === 'foto'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="dlh-gallery-scroll">
                    @forelse($photoGalleries as $g)
                        @php
                            $imgs = $g->images ?? [];
                            if (!is_array($imgs)) {
                                $imgs = !empty($imgs) ? [$imgs] : [];
                            }
                            $imgs = array_values(array_filter($imgs, fn($i) => !empty($i)));
                            $count = count($imgs);
                            if ($count === 0 && !empty($g->image)) {
                                $count = 1;
                            }
                            $thumbSrc = $g->image_url ?? ($g->image ? (str_starts_with($g->image, 'http') ? $g->image : asset('storage/' . ltrim($g->image, '/'))) : 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=600&h=400&fit=crop');
                        @endphp
                        <div class="dlh-gallery-item group bg-slate-900 shadow-md hover:shadow-2xl transition-all duration-300"
                             @click="openPhotoLightbox({{ json_encode($g) }})">
                            
                            <!-- Foto Cover -->
                            <img src="{{ $thumbSrc }}" 
                                 alt="{{ $g->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                            
                            <!-- Gradient Overlay (Abu Tua Gelap / Slate) -->
                            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(15,23,42,.95) 0%, rgba(15,23,42,.35) 55%, transparent 100%);"></div>

                            @if($count > 1)
                                <!-- Badge Album di Pojok Kiri Atas -->
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-white text-[11px] font-bold shadow-md"
                                          style="background: linear-gradient(135deg, #334155, #475569);">
                                        <i class="fas fa-images"></i> ALBUM ({{ $count }} FOTO)
                                    </span>
                                </div>
                                <!-- Indikator Album di Pojok Kanan Atas -->
                                <div class="absolute top-3 right-3 z-10">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-white text-[11px] font-bold border border-white/20 shadow-md backdrop-blur-md"
                                          style="background: rgba(15,23,42,0.75);">
                                        <i class="fas fa-folder text-amber-400 text-[10px]"></i> {{ $count }}
                                    </span>
                                </div>
                            @endif

                            <!-- Konten Bawah (Kategori, Jumlah Foto, Judul) -->
                            <div class="absolute bottom-0 left-0 right-0 p-5 z-10 text-left">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white text-[11px] font-bold backdrop-blur-md"
                                          style="background: rgba(51,65,85,0.95);">
                                        <i class="fas fa-tag text-[9px]"></i> {{ $g->category ?? 'Kegiatan Lapangan' }}
                                    </span>
                                    @if($count > 1)
                                        <span class="text-white/80 text-xs inline-flex items-center gap-1">
                                            <i class="fas fa-camera text-[10px]"></i> {{ $count }} Foto Dokumentasi
                                        </span>
                                    @endif
                                </div>
                                <h6 class="text-white font-bold text-base leading-snug drop-shadow m-0">{{ $g->title }}</h6>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 w-full text-center py-10 font-semibold">Belum ada foto galeri.</p>
                    @endforelse

                    <!-- KARTU LIHAT SEMUA FOTO KETIKA DIGESER KE SAMPING (ABU TUA) -->
                    <a href="{{ route('galeri', ['type' => 'foto']) }}"
                       class="dlh-gallery-item flex flex-col items-center justify-center text-center p-6 transition-all duration-300 group text-decoration-none"
                       style="min-width: 260px; background: linear-gradient(145deg, #0f172a 0%, #1e293b 50%, #334155 100%); border: 2px dashed rgba(148, 163, 184, 0.4); border-radius: 1.25rem;">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl mb-3 shadow-lg group-hover:scale-110 transition-transform"
                             style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);">
                            <i class="fas fa-images"></i>
                        </div>
                        <h5 class="text-white font-bold text-lg mb-1.5">Lihat Semua Foto</h5>
                        <p class="text-white/70 text-xs mb-4 leading-relaxed">Jelajahi seluruh album dokumentasi resmi kegiatan Kelurahan Semampir</p>
                        <span class="px-4 py-2 rounded-full font-bold text-xs text-slate-900 bg-white shadow-sm inline-flex items-center gap-1.5 group-hover:bg-slate-100 transition-colors">
                            <span>Buka Semua Foto</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </a>
                </div>
            </div>

            <!-- TAB 2: GALERI VIDEO (EXACT 3 ITEMS PER ROW DENGAN SCROLL HORISONTAL - ABU TUA) -->
            <div x-show="activeGalleryTab === 'video'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="dlh-gallery-scroll">
                    @forelse($videoGalleries as $v)
                        @php
                            $ytId = $v->youtube_id;
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $ytId ?? '', $matches)) {
                                $ytId = $matches[1];
                            }
                            $videoThumb = $v->image ? (str_starts_with($v->image, 'http') ? $v->image : asset('storage/' . ltrim($v->image, '/'))) : ($ytId ? "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg" : 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=600&h=400&fit=crop');
                        @endphp
                        <div class="dlh-gallery-item group bg-slate-900 shadow-md hover:shadow-2xl transition-all duration-300"
                             @click="openVideoPlayer({{ json_encode($v) }})">
                            
                            <!-- Thumbnail Video -->
                            <img src="{{ $videoThumb }}" 
                                 alt="{{ $v->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                            
                            <!-- Gradient Overlay (Abu Tua Gelap / Slate) -->
                            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(15,23,42,.95) 0%, rgba(15,23,42,.35) 55%, transparent 100%);"></div>

                            <!-- Tombol Play Bulat Kuning Emas di Tengah -->
                            <div class="dlh-gallery-play-btn">
                                <i class="fas fa-play pl-1"></i>
                            </div>

                            <!-- Konten Bawah (Kategori Merah Video & Judul) -->
                            <div class="absolute bottom-0 left-0 right-0 p-5 z-10 text-left">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white text-[11px] font-bold mb-2 backdrop-blur-md"
                                      style="background: rgba(220,38,38,0.9);">
                                    <i class="fas fa-play-circle text-[9px]"></i> {{ $v->category ?? 'VIDEO' }}
                                </span>
                                <h6 class="text-white font-bold text-base leading-snug drop-shadow m-0">{{ $v->title }}</h6>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 w-full text-center py-10 font-semibold">Belum ada video galeri.</p>
                    @endforelse

                    <!-- KARTU LIHAT SEMUA VIDEO KETIKA DIGESER KE SAMPING (ABU TUA) -->
                    <a href="{{ route('galeri', ['type' => 'video']) }}"
                       class="dlh-gallery-item flex flex-col items-center justify-center text-center p-6 transition-all duration-300 group text-decoration-none"
                       style="min-width: 260px; background: linear-gradient(145deg, #0f172a 0%, #1e293b 50%, #334155 100%); border: 2px dashed rgba(148, 163, 184, 0.4); border-radius: 1.25rem;">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-rose-500 text-2xl mb-3 shadow-lg group-hover:scale-110 transition-transform"
                             style="background: rgba(220,38,38,0.2); border: 1px solid rgba(220,38,38,0.3);">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <h5 class="text-white font-bold text-lg mb-1.5">Lihat Semua Video</h5>
                        <p class="text-white/70 text-xs mb-4 leading-relaxed">Jelajahi seluruh dokumentasi video resmi kegiatan Kelurahan Semampir</p>
                        <span class="px-4 py-2 rounded-full font-bold text-xs text-slate-900 bg-white shadow-sm inline-flex items-center gap-1.5 group-hover:bg-slate-100 transition-colors">
                            <span>Buka Semua Video</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 7. LINK TERKAIT (HORIZONTAL SCROLLING SLIDER)                             -->
    <!-- ========================================================================= -->
    <section class="py-8 sm:py-10 bg-slate-50/60 border-t border-slate-200 w-full max-w-full block clear-both overflow-hidden">
        <style>
            .link-terkait-scroll::-webkit-scrollbar { display: none; }
        </style>
        <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="text-center space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-[10px] font-bold uppercase tracking-wider">
                    <span>Sinergi & Link Terkait</span>
                </div>
                <h3 class="text-base sm:text-xl font-black text-slate-900 tracking-tight">Link Terkait</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 max-w-xl mx-auto">
                    Akses cepat portal resmi instansi pemerintah daerah dan layanan publik terkait
                </p>
            </div>

            <!-- Horizontal Scroll Container with Alpine.js Controls -->
            <div x-data="{
                    canScrollLeft: false,
                    canScrollRight: false,
                    updateScroll() {
                        const el = this.$refs.slider;
                        if (!el) return;
                        this.canScrollLeft = el.scrollLeft > 15;
                        this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 15);
                    },
                    scroll(dir) {
                        const el = this.$refs.slider;
                        if (!el) return;
                        const scrollDist = el.clientWidth * 0.75;
                        el.scrollBy({ left: dir * scrollDist, behavior: 'smooth' });
                    }
                 }"
                 x-init="$nextTick(() => { updateScroll(); }); window.addEventListener('resize', () => updateScroll())"
                 class="relative max-w-5xl mx-auto w-full group/slider">

                <!-- Tombol Navigasi Kiri -->
                <button type="button" 
                        @click="scroll(-1)" 
                        x-show="canScrollLeft"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-75"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-75"
                        class="absolute -left-3 sm:-left-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white shadow-md border border-slate-200 text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 flex items-center justify-center transition-all cursor-pointer hover:scale-105"
                        aria-label="Geser ke kiri">
                    <i class="fas fa-chevron-left text-[11px]"></i>
                </button>

                <!-- Tombol Navigasi Kanan -->
                <button type="button" 
                        @click="scroll(1)" 
                        x-show="canScrollRight"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-75"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-75"
                        class="absolute -right-3 sm:-right-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white shadow-md border border-slate-200 text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 flex items-center justify-center transition-all cursor-pointer hover:scale-105"
                        aria-label="Geser ke kanan">
                    <i class="fas fa-chevron-right text-[11px]"></i>
                </button>

                <!-- Slider Track -->
                <div x-ref="slider"
                     @scroll.passive="updateScroll()"
                     class="link-terkait-scroll flex items-center gap-3 overflow-x-auto scroll-smooth py-2 px-1 snap-x snap-mandatory"
                     style="scrollbar-width: none; -ms-overflow-style: none;">
                    @forelse($relatedLinks ?? [] as $link)
                        <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer"
                           class="snap-start shrink-0 w-[240px] sm:w-[260px] group relative flex items-center gap-3 p-2.5 sm:p-3 bg-white hover:bg-emerald-50/40 border border-slate-200 hover:border-emerald-400 rounded-xl transition-all duration-200 shadow-2xs hover:shadow-sm hover:-translate-y-0.5">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center p-1 group-hover:bg-white group-hover:border-emerald-200 transition-colors">
                                @php
                                    $linkLogo = $link['logo'] ?? null;
                                    $linkLogoUrl = null;
                                    if (!empty($linkLogo)) {
                                        if (str_starts_with($linkLogo, 'http://') || str_starts_with($linkLogo, 'https://') || str_starts_with($linkLogo, 'data:image')) {
                                            $linkLogoUrl = $linkLogo;
                                        } else {
                                            $linkLogoUrl = asset('storage/' . ltrim($linkLogo, '/'));
                                        }
                                    }
                                @endphp
                                @if(!empty($linkLogoUrl))
                                    <img src="{{ $linkLogoUrl }}" 
                                         alt="{{ $link['name'] ?? 'Link Terkait' }}" 
                                         onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';"
                                         class="max-h-full max-w-full object-contain filter group-hover:scale-105 transition-transform duration-200">
                                @else
                                    <img src="{{ asset('images/logo.png') }}" 
                                         alt="{{ $link['name'] ?? 'Link Terkait' }}" 
                                         class="max-h-full max-w-full object-contain filter group-hover:scale-105 transition-transform duration-200">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 pr-1">
                                <h4 class="font-bold text-xs text-slate-800 group-hover:text-emerald-700 transition leading-snug line-clamp-2">
                                    {{ $link['name'] ?? '-' }}
                                </h4>
                            </div>
                            <div class="shrink-0 text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all">
                                <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </div>
                        </a>
                    @empty
                        <div class="w-full py-6 text-center text-slate-400">
                            <p class="text-xs font-semibold">Belum ada daftar link terkait yang dikonfigurasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- MODAL POPUPS (ALPINE.JS)                                                  -->
    <!-- ========================================================================= -->

    <!-- 1. Modal Rekap Bulanan -->
    <div x-show="recapModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 space-y-6 text-white max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="text-lg font-bold flex items-center gap-2 text-slate-400">
                    <i class="fas fa-table"></i>
                    <span>Tabel Detail Rekapitulasi Pelayanan Bulanan (2026)</span>
                </h3>
                <button type="button" @click="recapModalOpen = false" class="text-slate-400 hover:text-white p-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600 border border-slate-800">
                    <thead class="bg-slate-950 text-slate-200 font-bold uppercase border-b border-slate-200">
                        <tr>
                            <th class="p-3 border-r border-slate-800">Bulan</th>
                            <th class="p-3 border-r border-slate-800">Permohonan Surat</th>
                            <th class="p-3 border-r border-slate-800">Surat Keluar</th>
                            <th class="p-3 border-r border-slate-800">Aduan Masuk</th>
                            <th class="p-3">Persentase Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($stats['monthly'] ?? [] as $row)
                        <tr class="hover:bg-slate-800/50">
                            <td class="p-3 font-bold text-white border-r border-slate-800">{{ $row['month'] }}</td>
                            <td class="p-3 border-r border-slate-800 text-slate-400 font-semibold">{{ $row['permohonan'] }} Dokumen</td>
                            <td class="p-3 border-r border-slate-800 text-amber-300 font-semibold">{{ $row['surat_keluar'] }} Dokumen</td>
                            <td class="p-3 border-r border-slate-800 font-semibold">{{ $row['pengaduan'] }} Aduan</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded bg-slate-500/20 text-slate-300 font-extrabold text-[10px]">
                                    {{ $row['pct'] }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-800">
                <button type="button" @click="recapModalOpen = false" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Modal Maklumat Pelayanan (Desain Piagam Resmi Eksekutif) -->
    <div x-show="maklumatModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-3xl w-full overflow-hidden shadow-2xl flex flex-col max-h-[94vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-base border border-amber-500/20 shadow-sm">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 leading-tight">Naskah Maklumat Pelayanan Publik</h3>
                        <p class="text-[10px] text-slate-400 font-semibold">Komitmen Mutu & Integritas Penyelenggaraan Pelayanan</p>
                    </div>
                </div>
                <button type="button" @click="maklumatModalOpen = false" class="w-8 h-8 rounded-full bg-white hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center text-xs transition border border-slate-200 shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body (Kanvas Piagam Resmi) -->
            <div class="p-4 sm:p-7 overflow-y-auto space-y-4 bg-slate-100/50">
                <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-md p-4 sm:p-7">
                    <!-- Subtle Watermark Shield -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
                        <i class="fas fa-shield-alt text-[260px]"></i>
                    </div>

                    <!-- Container Piagam Bersih, Minimalis & Modern -->
                    <div class="relative z-10 border border-slate-200 rounded-2xl p-3.5 sm:p-5 bg-white shadow-xs">
                        
                        <!-- Header Maklumat Bersih & Ringkas -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                @if(file_exists(public_path('storage/settings/logo_probolinggo.png')))
                                    <img src="{{ asset('storage/settings/logo_probolinggo.png') }}" 
                                         alt="Logo Probolinggo" 
                                         class="w-9 h-9 sm:w-10 sm:h-10 object-contain shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-base shrink-0">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">
                                        Maklumat Pelayanan Publik
                                    </h3>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }} • {{ $villageProfile['subdistrict'] ?? 'Kecamatan Kraksaan' }}
                                    </p>
                                </div>
                            </div>

                            @if(!empty($villageProfile['maklumat_nomor_sk']))
                                <span class="self-start sm:self-center inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-50 text-slate-600 font-mono text-[11px] font-semibold border border-slate-200">
                                    <i class="fas fa-stamp text-amber-600 text-xs"></i>
                                    <span>SK: {{ $villageProfile['maklumat_nomor_sk'] }}</span>
                                </span>
                            @endif
                        </div>

                        @if(!empty($villageProfile['maklumat_file']) && ($villageProfile['maklumat_file_type'] ?? '') !== 'pdf')
                            <!-- FOTO PIAGAM MAKLUMAT TAMPIL LANGSUNG DI SINI -->
                            <div class="my-3 space-y-3" x-data="{ showText: false, isZoomed: false }">
                                <div class="relative bg-white rounded-2xl border-2 border-amber-500/35 p-2 sm:p-3 shadow-md group">
                                    <!-- Main Photo View -->
                                    <div class="relative overflow-hidden rounded-xl bg-slate-100/70 cursor-pointer flex items-center justify-center border border-slate-200/80"
                                         @click="isZoomed = !isZoomed">
                                        <img src="{{ asset('storage/' . $villageProfile['maklumat_file']) }}" 
                                             alt="Piagam Maklumat Pelayanan Publik" 
                                             :class="isZoomed ? 'max-h-none' : 'max-h-[50vh] sm:max-h-[60vh]'"
                                             class="w-full object-contain rounded-lg transition-all duration-300">
                                        
                                        <!-- Hover Hint -->
                                        <div class="absolute inset-0 bg-slate-950/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                            <span class="px-3.5 py-1.5 rounded-xl bg-slate-950/80 text-white text-xs font-bold shadow-xl backdrop-blur-sm flex items-center gap-2">
                                                <i class="fas fa-search-plus text-amber-400"></i>
                                                <span x-text="isZoomed ? 'Klik untuk Perkecil' : 'Klik untuk Memperbesar Gambar'"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Caption & Action Toolbar -->
                                    <div class="pt-2 px-1 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5 text-xs bg-amber-50/40 rounded-xl mt-2 p-2.5 border border-amber-200/50">
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 text-xs leading-snug">
                                                {{ $villageProfile['maklumat_caption'] ?? 'Dokumen Piagam Penetapan Standar Maklumat Pelayanan Publik Kelurahan Semampir' }}
                                            </p>
                                            <p class="text-[10px] text-slate-500 mt-0.5">
                                                Piagam fisik resmi yang ditandatangani dan berlaku mengikat bagi seluruh penyelenggaraan pelayanan.
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                            <button type="button" 
                                                    @click="isZoomed = !isZoomed"
                                                    class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold rounded-lg transition inline-flex items-center gap-1.5 shadow-2xs">
                                                <i :class="isZoomed ? 'fa-compress' : 'fa-expand'" class="fas text-[11px] text-amber-600"></i>
                                                <span x-text="isZoomed ? 'Perkecil' : 'Perbesar'"></span>
                                            </button>
                                            <a href="{{ asset('storage/' . $villageProfile['maklumat_file']) }}" 
                                               download 
                                               class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition inline-flex items-center gap-1.5 shadow-sm">
                                                <i class="fas fa-download text-[11px]"></i>
                                                <span>Unduh</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Collapsible Naskah Teks Digital -->
                                <div class="text-center pt-0.5">
                                    <button type="button" 
                                            @click="showText = !showText"
                                            class="text-[11px] font-bold text-slate-500 hover:text-slate-800 transition inline-flex items-center gap-1.5 py-1 px-3 rounded-lg hover:bg-slate-100">
                                        <i :class="showText ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-[9px] text-amber-600"></i>
                                        <span x-text="showText ? 'Sembunyikan Naskah Teks Digital' : 'Tampilkan Naskah Teks Digital'"></span>
                                    </button>
                                </div>

                                <div x-show="showText" x-cloak x-transition class="relative bg-slate-50/90 border border-slate-200 rounded-xl p-4 sm:p-5 shadow-inner text-center">
                                    <i class="fas fa-quote-left text-amber-500/25 text-xl absolute top-2.5 left-3 pointer-events-none"></i>
                                    <div class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed italic px-3 sm:px-6 prose prose-sm max-w-none text-center">
                                        {!! !empty($villageProfile['maklumat_text']) ? $villageProfile['maklumat_text'] : '"Dengan ini, kami seluruh ASN dan Pegawai Pemerintah ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir') . ' menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai ketentuan perundang-undangan yang berlaku apabila pelayanan tidak sesuai janji."' !!}
                                    </div>
                                    <i class="fas fa-quote-right text-amber-500/25 text-xl absolute bottom-2.5 right-3 pointer-events-none"></i>
                                </div>

                                <!-- Kaki Verifikasi Singkat -->
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-200/80 text-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full border-2 border-dashed border-amber-500 bg-amber-50 text-amber-700 flex items-center justify-center font-black rotate-[-10deg] shadow-sm shrink-0">
                                            <span class="text-[7px] uppercase tracking-tighter text-center leading-tight">KOMITMEN<br/>RESMI</span>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider">KEPATUHAN STANDAR</span>
                                            <span class="text-[11px] font-black text-emerald-600 flex items-center gap-1">
                                                <i class="fas fa-check-circle text-xs"></i> UU No. 25 Tahun 2009
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <span class="text-[11px] font-semibold text-slate-600">
                                            Ditetapkan oleh: <strong class="text-slate-900">{{ $villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.' }}</strong>
                                        </span>
                                        <span class="block text-[10px] text-slate-400">Kepala {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- TAMPILAN DEFAULT KETIKA BELUM ADA FOTO / UPLOAD PDF -->
                            <!-- Isi Naskah Piagam -->
                            <div class="relative bg-slate-50/80 border border-slate-200/80 rounded-xl p-4 sm:p-5 shadow-inner mb-5 text-center">
                                <i class="fas fa-quote-left text-amber-500/25 text-2xl absolute top-2.5 left-3 pointer-events-none"></i>
                                <div class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed italic px-3 sm:px-6 prose prose-sm max-w-none text-center">
                                    {!! !empty($villageProfile['maklumat_text']) ? $villageProfile['maklumat_text'] : '"Dengan ini, kami seluruh ASN dan Pegawai Pemerintah ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir') . ' menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai ketentuan perundang-undangan yang berlaku apabila pelayanan tidak sesuai janji."' !!}
                                </div>
                                <i class="fas fa-quote-right text-amber-500/25 text-2xl absolute bottom-2.5 right-3 pointer-events-none"></i>
                            </div>

                            @if(!empty($villageProfile['maklumat_file']) && ($villageProfile['maklumat_file_type'] ?? '') === 'pdf')
                                <div class="mb-5 p-3.5 bg-white rounded-xl border border-amber-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-left">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-lg border border-rose-100 shrink-0">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">{{ $villageProfile['maklumat_file_name'] ?? 'Piagam_Maklumat_Resmi.pdf' }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $villageProfile['maklumat_file_size'] ?? 'Dokumen Resmi' }} • Surat Keputusan Pengesahan</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $villageProfile['maklumat_file']) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition inline-flex items-center justify-center gap-1.5 shadow-sm shrink-0">
                                        <i class="fas fa-download text-[10px]"></i>
                                        <span>Unduh PDF</span>
                                    </a>
                                </div>
                            @endif

                            <!-- Bagian Kaki & Pengesahan -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-5 pt-3 border-t border-slate-200/80 text-xs">
                                <!-- Cap Verifikasi Komitmen -->
                                <div class="flex items-center gap-3 self-start sm:self-center">
                                    <div class="w-12 h-12 rounded-full border-2 border-dashed border-amber-500 bg-amber-50 text-amber-700 flex items-center justify-center font-black rotate-[-10deg] shadow-sm shrink-0">
                                        <span class="text-[8px] uppercase tracking-tighter text-center leading-tight">KOMITMEN<br/>RESMI</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider">KEPATUHAN STANDAR</span>
                                        <span class="text-[11px] font-black text-emerald-600 flex items-center gap-1">
                                            <i class="fas fa-check-circle text-xs"></i> UU No. 25 Tahun 2009
                                        </span>
                                    </div>
                                </div>

                                <!-- Tanda Tangan Lurah -->
                                <div class="text-left sm:text-right w-full sm:w-auto">
                                    <div class="text-[11px] text-slate-500 font-medium">Kraksaan, {{ date('Y') }}</div>
                                    <div class="text-xs font-bold text-slate-700 mt-0.5">Kepala {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}</div>
                                    <div class="text-sm font-black text-slate-900 mt-4 underline underline-offset-4 decoration-amber-500 decoration-2">
                                        {{ $villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.' }}
                                    </div>
                                    <div class="text-[10px] font-mono text-slate-500 mt-0.5">
                                        NIP. {{ $villageProfile['head_nip'] ?? '19750612 201001 1 004' }}
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-[11px] font-medium text-slate-500 flex items-center gap-1.5 text-center sm:text-left">
                    <i class="fas fa-info-circle text-amber-500"></i>
                    <span>Berlaku mengikat untuk seluruh aparatur dan unit pelayanan publik kelurahan.</span>
                </span>
                <button type="button" @click="maklumatModalOpen = false" 
                        class="w-full sm:w-auto px-6 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Modal Photo Lightbox -->
    <div x-show="lightboxOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md">
        
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full overflow-hidden text-white shadow-2xl flex flex-col">
            
            <!-- KUNCI UKURAN KONSISTEN: Ubah tinggi menggunakan h-80 (atau h-96 untuk layar lebih besar) dan maksimalkan lebar max-w-2xl pada modal di atas -->
            <div class="relative h-72 sm:h-96 w-full overflow-hidden bg-black flex items-center justify-center shrink-0">
                <img :src="activePhoto ? (activePhoto.image_url || ('/storage/' + activePhoto.image)) : ''"
                     :alt="activePhoto ? activePhoto.title : ''"
                     class="w-full h-full object-cover">
                
                <button type="button" @click="lightboxOpen = false" class="absolute top-4 right-4 bg-slate-950/70 hover:bg-slate-950 text-white w-9 h-9 rounded-full flex items-center justify-center transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 h-36 flex flex-col justify-between shrink-0">
                <div>
                    <h4 class="font-bold text-base text-white line-clamp-1" x-text="activePhoto ? activePhoto.title : ''"></h4>
                    <p class="text-xs text-slate-400 line-clamp-2 mt-1" x-text="activePhoto ? (activePhoto.description || activePhoto.date) : ''"></p>
                </div>
                <div class="flex justify-end mt-2">
                    <a :href="activePhoto ? (activePhoto.image_url || ('/storage/' + activePhoto.image)) : '#'" download target="_blank"
                       class="px-4 py-2 text-white font-bold text-xs rounded-xl flex items-center gap-2 transition shadow-sm"
                       style="background-color: #334155 !important;">
                        <i class="fas fa-download"></i>
                        <span>Unduh Foto</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Video Player (Ukuran & Proporsi Identik dengan Modal Foto) --}}
    <div x-show="videoModalOpen"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4"
         style="display: none;">
        
        <!-- Wadah Utama: max-w-2xl w-full rounded-3xl (PERSIS SAMA SEPERTI MODAL FOTO) -->
        <div class="relative bg-slate-900 text-white rounded-3xl overflow-hidden max-w-2xl w-full border border-slate-800 shadow-2xl flex flex-col"
             >
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-4 bg-slate-950 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="bg-rose-600 text-white text-[10px] font-black uppercase px-2.5 py-0.5 rounded-md flex items-center gap-1">
                        <i class="fab fa-youtube"></i> VIDEO
                    </span>
                    <span class="text-slate-400 text-xs font-bold truncate max-w-[320px]" x-text="activeVideo ? activeVideo.title : 'Pemutar Video'"></span>
                </div>

                <button type="button" @click="videoModalOpen = false" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-white transition flex items-center justify-center focus:outline-none">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Modal Video Viewport: Kunci tinggi h-72 sm:h-96 (PERSIS SAMA DENGAN FOTO) --}}
            <div class="relative bg-black h-72 sm:h-96 flex items-center justify-center overflow-hidden w-full shrink-0">
                <template x-if="videoModalOpen && activeVideo">
                    <iframe class="w-full h-full border-0"
                            :src="getVideoEmbed(activeVideo)"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </template>
            </div>

            {{-- Modal Footer: Kunci tinggi h-36 dengan padding p-6 (PERSIS SAMA DENGAN FOOTER FOTO) --}}
            <div class="p-6 h-36 flex flex-col justify-between shrink-0 bg-slate-900 border-t border-slate-800">
                <div>
                    <h3 x-text="activeVideo ? activeVideo.title : ''" class="font-bold text-base text-white line-clamp-1"></h3>
                    <p class="text-xs text-slate-400 line-clamp-2 mt-1" x-text="activeVideo ? (activeVideo.date || (activeVideo.created_at ? activeVideo.created_at : '')) : ''"></p>
                </div>
                <div class="flex justify-end mt-2">
                    <a :href="getVideoWatchUrl(activeVideo)" 
                       target="_blank"
                       class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl flex items-center gap-2 transition">
                        <i class="fab fa-youtube"></i>
                        <span>Tonton di YouTube</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- 5. Modal Detail Statistik -->
    <div x-show="statModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 text-slate-800 max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center text-lg font-bold">
                        <i :class="{
                            'fas fa-users': activeStat === 'penduduk',
                            'fas fa-address-card': activeStat === 'kk',
                            'fas fa-map-signs': activeStat === 'rtrw',
                            'fas fa-chart-area': activeStat === 'wilayah',
                            'fas fa-coins text-amber-600 bg-amber-50': activeStat === 'anggaran',
                            'fas fa-hand-holding-heart text-emerald-600 bg-emerald-50': activeStat === 'bansos'
                        }"></i>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 leading-snug"
                            x-text="{
                                'penduduk': 'Rincian Demografi & Jumlah Penduduk',
                                'kk': 'Data Kepala Keluarga (KK)',
                                'rtrw': 'Pembagian Wilayah Rukun Warga & Tetangga',
                                'wilayah': 'Luas & Batas Administrasi Wilayah',
                                'anggaran': 'Transparansi Realisasi Pagu Dana TA 2026',
                                'bansos': 'Data Penerima Bantuan Sosial (Bansos) & Kuota'
                            }[activeStat]"></h3>
                        <p class="text-xs text-slate-500">{{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}, {{ $villageProfile['subdistrict'] ?? 'Kecamatan Kraksaan' }}</p>
                    </div>
                </div>
                <button type="button" @click="statModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- 1. Detail Penduduk -->
            <div x-show="activeStat === 'penduduk'" class="space-y-4">
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-4">
                        <span class="text-xs font-semibold text-blue-600 block">Laki-Laki</span>
                        <span class="text-2xl font-black text-blue-950 mt-1 block">{{ $villageProfile['demographics']['male'] ?? '0' }}</span>
                    </div>
                    <div class="bg-pink-50/60 border border-pink-100 rounded-2xl p-4">
                        <span class="text-xs font-semibold text-pink-600 block">Perempuan</span>
                        <span class="text-2xl font-black text-pink-950 mt-1 block">{{ $villageProfile['demographics']['female'] ?? '0' }}</span>
                    </div>
                </div>
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs text-slate-600 space-y-2">
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="font-medium">Usia Produktif (15 - 64 thn)</span>
                        <span class="font-bold text-slate-800">{{ $villageProfile['demographics']['productive_count'] ?? '0' }} Jiwa</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="font-medium">Anak-anak (0 - 14 thn)</span>
                        <span class="font-bold text-slate-800">{{ $villageProfile['demographics']['child_count'] ?? '0' }} Jiwa</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="font-medium">Lansia (65+ thn)</span>
                        <span class="font-bold text-slate-800">{{ $villageProfile['demographics']['elderly_count'] ?? '0' }} Jiwa</span>
                    </div>
                </div>
            </div>

            <!-- 2. Detail KK -->
            <div x-show="activeStat === 'kk'" class="space-y-4">
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs text-slate-600 space-y-2.5">
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                        <span>Total Kepala Keluarga (KK) Terdaftar</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $villageProfile['stats']['kk'] ?? '2.640' }} KK</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                        <span>Rata-rata Jiwa / KK</span>
                        <span class="font-bold text-slate-900">{{ $villageProfile['demographics']['avg_family_size'] ?? '3.2' }} Jiwa</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span>Kepadatan Penduduk</span>
                        <span class="font-bold text-slate-900">{{ $villageProfile['demographics']['density'] ?? '3.438' }} Jiwa/km²</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 italic">
                    * Data dihimpun berdasarkan rekapitulasi data kependudukan kelurahan terkini.
                </p>
            </div>

            <!-- 3. Detail RT / RW -->
            <div x-show="activeStat === 'rtrw'" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 bg-slate-50/60 border border-slate-100 rounded-2xl text-center">
                        <span class="text-2xl font-black text-slate-800 block">{{ $villageProfile['territory']['rw'] ?? '08' }}</span>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Rukun Warga (RW)</span>
                    </div>
                    <div class="p-4 bg-slate-50/60 border border-slate-100 rounded-2xl text-center">
                        <span class="text-2xl font-black text-slate-800 block">{{ $villageProfile['territory']['rt'] ?? '32' }}</span>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Rukun Tetangga (RT)</span>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                    Seluruh pengantar pengurusan administrasi warga diawali dari ketua RT dan RW setempat sesuai domisili tempat tinggal sebelum diajukan ke kantor kelurahan.
                </p>
            </div>

            <!-- 4. Detail Luas Wilayah -->
            <div x-show="activeStat === 'wilayah'" class="space-y-4">
                <div class="bg-slate-50/60 border border-slate-100 rounded-2xl p-4 text-center">
                    <span class="text-2xl font-black text-slate-800 block">{{ $stats['luas'] ?? '3.82 km²' }}</span>
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Total Luas Wilayah</span>
                </div>
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2 text-xs">
                    <h5 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2">Batas Administrasi Wilayah:</h5>
                    <div class="grid grid-cols-2 gap-2 text-slate-600">
                        <div class="p-2.5 bg-white rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-bold">UTARA</span>
                            <span class="font-medium text-slate-800">{{ $villageProfile['territory']['north'] ?? '-' }}</span>
                        </div>
                        <div class="p-2.5 bg-white rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-bold">TIMUR</span>
                            <span class="font-medium text-slate-800">{{ $villageProfile['territory']['east'] ?? '-' }}</span>
                        </div>
                        <div class="p-2.5 bg-white rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-bold">SELATAN</span>
                            <span class="font-medium text-slate-800">{{ $villageProfile['territory']['south'] ?? '-' }}</span>
                        </div>
                        <div class="p-2.5 bg-white rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-bold">BARAT</span>
                            <span class="font-medium text-slate-800">{{ $villageProfile['territory']['west'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Detail Pagu Anggaran -->
            <div x-show="activeStat === 'anggaran'" class="space-y-4">
                <div class="bg-emerald-50/60 border border-emerald-200 rounded-2xl p-4 text-center">
                    <span class="text-[10px] uppercase font-bold text-emerald-800 tracking-wider">Total Pagu Anggaran Kelurahan TA {{ $villageProfile['apbd']['year'] ?? '2026' }}</span>
                    <div class="text-2xl font-black text-emerald-950 mt-1">Rp 1.580.000.000</div>
                    <span class="text-xs font-semibold text-emerald-700 mt-1 block">Realisasi Serapan: 73,0% (Rp 1.120.000.000)</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-medium text-slate-700">1. Penyelenggaraan Pemerintahan Kelurahan</span>
                        <span class="font-bold text-slate-900">75.3%</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-medium text-slate-700">2. Pelaksanaan Pembangunan Lingkungan</span>
                        <span class="font-bold text-slate-900">73.9%</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-medium text-slate-700">3. Pembinaan Kemasyarakatan</span>
                        <span class="font-bold text-slate-900">73.1%</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-medium text-slate-700">4. Pemberdayaan Masyarakat</span>
                        <span class="font-bold text-slate-900">66.9%</span>
                    </div>
                </div>
                <a href="{{ route('transparansi') }}" class="block text-center w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow transition">
                    Buka Laporan Transparansi Anggaran Lengkap &rarr;
                </a>
            </div>

            <!-- 6. Detail Bansos (Metrik Khusus) -->
            <div x-show="activeStat === 'bansos'" class="space-y-4">
                <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-4 text-center">
                    <span class="text-[10px] uppercase font-bold text-emerald-800 tracking-wider">Total Keluarga Penerima Manfaat (KPM) Bansos</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-950 mt-1">842 Keluarga</div>
                    <span class="text-[11px] font-semibold text-emerald-700">31.9% dari Total KK | Rp 1,84 Miliar Tersalurkan</span>
                </div>

                <div class="grid grid-cols-2 gap-2.5 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span class="text-emerald-700">BLT Kelurahan</span>
                            <span class="text-slate-900">120 KPM</span>
                        </div>
                        <p class="text-[10px] text-slate-500">Bantuan Langsung Tunai</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800">Tersalurkan 100%</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span class="text-emerald-700">PKH</span>
                            <span class="text-slate-900">285 KPM</span>
                        </div>
                        <p class="text-[10px] text-slate-500">Program Keluarga Harapan</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800">Tahap Berjalan</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span class="text-emerald-700">Bantuan Sembako / BPNT</span>
                            <span class="text-slate-900">340 KPM</span>
                        </div>
                        <p class="text-[10px] text-slate-500">Bantuan Pangan Non-Tunai</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800">Tersalurkan</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span class="text-emerald-700">Bantuan Daerah</span>
                            <span class="text-slate-900">97 KPM</span>
                        </div>
                        <p class="text-[10px] text-slate-500">Bantuan Sosial Khusus</p>
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800">Tersalurkan</span>
                    </div>
                </div>

                <a href="{{ route('transparansi') }}" class="block text-center w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow transition">
                    Lihat Data Bantuan Sosial di Portal Transparansi &rarr;
                </a>
            </div>

            <!-- Footer Modal -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="button" @click="statModalOpen = false" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>

@endsection
