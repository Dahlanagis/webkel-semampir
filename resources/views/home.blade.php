@extends('layouts.app')

@section('title', 'Beranda - Portal Resmi ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div x-data="{ 
    // Modal States
    recapModalOpen: false,
    maklumatModalOpen: false,
    lightboxOpen: false,
    statModalOpen: false,
    activePhoto: null,
    videoModalOpen: false,
    activeVideo: null,
    activeStat: 'penduduk',

    // Methods
    openPhotoLightbox(photo) {
        this.activePhoto = photo;
        this.lightboxOpen = true;
    },
    openVideoPlayer(video) {
        this.activeVideo = video;
        this.videoModalOpen = true;
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
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>PORTAL RESMI KELURAHAN</span>
                    </div>
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black leading-tight tracking-tight mb-4 drop-shadow-lg text-white">
                        Selamat Datang di <br/>
                        <span class="text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.5)]">{{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}</span>
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
                                <span class="w-2 h-2 rounded-full bg-{{ $post->category->color_code ?? 'emerald' }}-400 animate-pulse"></span>
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
                            
                            <a href="{{ route('berita.detail', $post->slug) }}" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-full shadow-md transition mt-4">
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
                        :class="{'w-8 bg-emerald-500': currentSlide === 0, 'w-2 bg-white/50 hover:bg-white/80': currentSlide !== 0}"
                        class="h-2 rounded-full transition-all duration-300"></button>
                
                <!-- Indicators for News Slides -->
                @foreach($sliderPosts as $index => $post)
                    <button @click="currentSlide = {{ $index + 1 }}" 
                            :class="{'w-8 bg-emerald-500': currentSlide === {{ $index + 1 }}, 'w-2 bg-white/50 hover:bg-white/80': currentSlide !== {{ $index + 1 }}}"
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
                        <img src="{{ !empty($villageProfile['head_photo']) ? asset('storage/' . $villageProfile['head_photo']) : asset('images/sotk/lurah.png') }}"
                             alt="Foto Kepala {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}"
                             class="w-full aspect-[4/5] object-cover rounded-3xl shadow-sm bg-slate-100">

                        <!-- Floating Name Badge -->
                        <div class="absolute -bottom-6 inset-x-4 sm:inset-x-6 bg-white py-4 px-3 sm:py-5 sm:px-4 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] text-center">
                            <h3 class="font-bold text-sm sm:text-base text-slate-900 leading-tight">{{ $villageProfile['head_name'] ?? 'Drs. H. Ahmad Sudirman, M.Si' }}</h3>
                            <p class="text-[9px] sm:text-[10px] text-emerald-600 font-bold uppercase tracking-wider mt-1 sm:mt-2">Kepala {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Sambutan Resmi -->
                <div class="w-full lg:col-span-2 space-y-5 text-center lg:text-left min-w-0 mt-8 lg:mt-0">
                    <h4 class="text-emerald-600 text-xs sm:text-sm font-bold uppercase tracking-wide">
                        SAMBUTAN KEPALA KELURAHAN
                    </h4>

                    <h2 class="text-3xl sm:text-4xl lg:text-[42px] font-extrabold text-slate-900 leading-[1.15] tracking-tight">
                        {{ $villageProfile['welcome_title'] ?? 'Komitmen Pelayanan Publik yang Transparan, Cepat, & Responsif' }}
                    </h2>

                    <div class="text-slate-500 text-sm sm:text-base leading-relaxed space-y-4 max-w-3xl mx-auto lg:mx-0 pt-2 prose prose-emerald max-w-none prose-p:text-slate-500 prose-p:leading-relaxed">
                        {!! $villageProfile['welcome_text'] ?? '<p>Melalui sistem portal terpadu ini, Pemerintah Kelurahan Patokan berkomitmen penuh dalam mewujudkan pelayanan publik modern yang berbasis transparansi, kemudahan akses dokumen mandiri, dan akuntabilitas pengelolaan anggaran.</p><p>Kami terus berinovasi untuk memberikan pelayanan terbaik bagi warga Kraksaan tanpa kerumitan administrasi, ramah, akuntabel, dan 100% bebas dari segala bentuk pungutan liar.</p>' !!}
                    </div>

                    <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('visi-misi') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition-colors">
                            <span>Visi & Misi Kami</span>
                            <i class="fas fa-arrow-right text-xs ml-1"></i>
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
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-7xl h-96 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col gap-12 lg:gap-20">
            
            <!-- Action Buttons Banner Highlight -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center lg:text-left">
                    <span class="px-3 py-1 rounded-md bg-emerald-500/20 text-emerald-800 font-extrabold text-[10px] uppercase tracking-wider border border-emerald-400/30">
                        AKSES CEPAT LAYANAN DIGITAL
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Layanan & Transparansi {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Lihat Standar Pelayanan Publik (SOP) atau unduh dokumen transparansi realisasi APBD / Dana Desa 2026.
                    </p>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                    <a href="{{ route('dokumen') }}"
                       class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-folder-open"></i>
                        <span>Pusat Unduhan Dokumen</span>
                    </a>

                    <a href="{{ route('transparansi') }}"
                       class="px-6 py-3.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs sm:text-sm rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-file-pdf"></i>
                        <span>Transparansi APBD</span>
                    </a>
                </div>
            </div>

            <!-- Header Section Statistik -->
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-emerald-400 text-xs font-black uppercase tracking-wider">DATA STATISTIK {{ strtoupper($villageProfile['village_name'] ?? 'KELURAHAN PATOKAN') }}</span>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 mt-2 mb-4 leading-tight tracking-tight">
                    Statistik Wilayah {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}
                </h2>
                <p class="text-sm sm:text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
                    Realisasi kinerja data kependudukan dan penanganan layanan masyarakat {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}.
                </p>
            </div>

            <!-- Grid Cards Statistik Real-Time (10 Cards) -->
            <div class="w-full grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6 items-stretch">
                
                <!-- Card 1: Penduduk -->
                <div @click="statModalOpen = true; activeStat = 'penduduk'" class="cursor-pointer bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 h-full min-h-[120px] hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-1 truncate">{{ $stats['penduduk'] ?? '8.425' }}</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-auto">Jumlah Penduduk</div>
                </div>

                <!-- Card 2: KK -->
                <div @click="statModalOpen = true; activeStat = 'kk'" class="cursor-pointer bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 h-full min-h-[120px] hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base mb-3">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-1 truncate">{{ $stats['kk'] ?? '2.640' }}</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-auto">Kepala Keluarga</div>
                </div>

                <!-- Card 3: RT / RW -->
                <div @click="statModalOpen = true; activeStat = 'rtrw'" class="cursor-pointer bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 h-full min-h-[120px] hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base mb-3">
                        <i class="fas fa-map-signs"></i>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-1 truncate">{{ $stats['rt_rw'] ?? '32 RT / 08 RW' }}</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-auto">Wilayah RT / RW</div>
                </div>
                <!-- Card 4: Luas Wilayah -->
                <div @click="statModalOpen = true; activeStat = 'wilayah'" class="cursor-pointer bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 h-full min-h-[120px] hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base mb-3">
                        <i class="fas fa-chart-area"></i>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-1 truncate">{{ $stats['luas'] ?? '3.82 km²' }}</div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase mt-auto">Luas Wilayah</div>
                </div>
                <!-- Card 5: Kartu Dana Desa Aksen Amber -->
                <div @click="statModalOpen = true; activeStat = 'anggaran'" class="cursor-pointer bg-amber-50/50 border border-amber-300 rounded-2xl sm:rounded-3xl p-4 sm:p-5 h-full min-h-[120px] hover:shadow-md hover:-translate-y-0.5 transition-all relative overflow-hidden group flex flex-col">
                    <div class="w-9 h-9 rounded-xl bg-amber-100/80 text-amber-600 flex items-center justify-center font-bold text-base mb-3 border border-amber-300/50">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="text-lg sm:text-xl font-extrabold text-amber-600 tracking-tight mb-1 truncate">Rp {{ $villageProfile['apbd']['total_budget'] ?? '1.450.000.000' }}</div>
                    <div class="text-[11px] font-extrabold text-amber-600/80 uppercase tracking-wider mt-auto">Pagu Dana TA {{ $villageProfile['apbd']['year'] ?? '2026' }}</div>
                </div>
            </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 4. INFO BERITA TERKINI & SIDEBAR MAKLUMAT PELAYANAN                       -->
    <!-- ========================================================================= -->
    <section class="py-16 sm:py-20 bg-slate-50 border-b border-slate-200 w-full max-w-full">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="w-full grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Left: Berita Terkini (lg:col-span-8) -->
                <div class="w-full lg:col-span-2 min-w-0 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">KABAR KELURAHAN</span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900">Berita & Informasi Terbaru</h3>
                        </div>

                        <a href="{{ route('berita') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            <span>Lihat Semua Berita</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <!-- News Grid (2 Columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">

                        @if($latestPosts->isEmpty())
                            <div class="col-span-1 sm:col-span-2 p-8 bg-white border border-slate-200 rounded-2xl text-center text-slate-500 text-sm shadow-sm flex flex-col items-center justify-center">
                                <i class="fas fa-newspaper text-3xl text-slate-300 mb-3"></i>
                                Belum ada berita atau informasi terbaru saat ini.
                            </div>
                        @else
                            @foreach($latestPosts as $post)

                        <article class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition group flex flex-col justify-between w-full min-w-0 h-full">
                            <div>
                                <!-- Image Thumbnail (Fixed Height) -->
                                <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-900 w-full shrink-0">
                                    <img src="{{ asset($post->image_url ?? ($post->image ? 'storage/'.$post->image : 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?auto=format&fit=crop&w=800&q=80')) }}"
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    
                                    <span class="absolute top-3 left-3 bg-emerald-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-md uppercase tracking-wider shadow">
                                        {{ $post->category?->name ?? 'BERITA PATOKAN' }}
                                    </span>
                                </div>

                                <!-- Body -->
                                <div class="p-5 space-y-3">
                                    <div class="flex items-center gap-2 text-[11px] font-medium text-slate-400">
                                        <i class="fas fa-calendar-alt text-emerald-600"></i>
                                        <span>{{ isset($post->published_at) ? \Carbon\Carbon::parse($post->published_at)->format('d M Y') : '15 Aug 2026' }}</span>
                                    </div>

                                    <h4 class="font-bold text-base text-slate-900 group-hover:text-emerald-700 transition leading-snug line-clamp-2">
                                        <a href="{{ route('berita.detail', $post->slug ?? '#') }}">{{ $post->title }}</a>
                                    </h4>

                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                                        {{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 110) }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-5 pb-5 pt-0">
                                <a href="{{ route('berita.detail', $post->slug ?? '#') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center gap-1.5">
                                    <span>Baca Selengkapnya</span>
                                    <i class="fas fa-chevron-right text-[9px]"></i>
                                </a>
                            </div>
                        </article>
                        @endforeach
                        @endif
                    </div>
                </div>

                <!-- Right: Maklumat Pelayanan Sidebar (lg:col-span-4) -->
                <div class="w-full lg:col-span-1 min-w-0 space-y-6">
                    <div class="bg-gradient-to-br from-white via-slate-50 to-emerald-50 text-slate-900 border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden space-y-6">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 border border-amber-200 flex items-center justify-center font-bold text-xl shadow-sm">
                            <i class="fas fa-award"></i>
                        </div>

                        <div class="space-y-2">
                            <span class="text-amber-600 font-extrabold text-[10px] uppercase tracking-wider">STANDAR MUTU PELAYANAN</span>
                            <h3 class="text-xl font-black text-slate-900 leading-snug">
                                Maklumat Pelayanan Publik Resmi
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                                Komitmen penuh seluruh jajaran aparatur {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }} dalam memberikan hak pelayanan terbaik bagi seluruh warga.
                            </p>
                        </div>

                        <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-4 text-xs text-slate-700 leading-relaxed italic border-l-4 border-l-amber-500 shadow-sm">
                            "Dengan ini kami menyatakan sanggup menyelenggarakan pelayanan sesuai standar yang ditetapkan dan siap menerima sanksi apabila melanggar."
                        </div>

                        <button type="button" @click="maklumatModalOpen = true"
                                class="w-full py-3.5 bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/20 text-xs font-extrabold rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-file-contract"></i>
                            <span>Lihat Naskah Maklumat Resmi</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 5. SINERGI INSTANSI / TAUTAN TERKAIT                                      -->
    <!-- ========================================================================= -->
    <section class="py-14 bg-white border-b border-slate-200 w-full max-w-full block clear-both">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">MITRA KEMITRAAN</span>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900">Sinergi Instansi & Portal Terkait</h3>
            </div>

            <div class="w-full max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($relatedLinks ?? [] as $link)
                <a href="{{ $link['url'] }}" target="_blank"
                   class="p-5 bg-slate-50 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-300 rounded-2xl transition flex flex-col items-center text-center group">
                    <img src="{{ str_starts_with($link['logo'], 'http') ? $link['logo'] : asset('storage/' . $link['logo']) }}" alt="{{ $link['name'] }}" class="h-10 w-auto object-contain mb-3 group-hover:scale-110 transition duration-300">
                    <h4 class="font-bold text-xs text-slate-900 group-hover:text-emerald-700 transition">{{ $link['name'] }}</h4>
                    <p class="text-[10px] text-slate-500 mt-1 line-clamp-1">{{ $link['desc'] }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 6. DOKUMENTASI TERPADU (GALERI FOTO & VIDEO KEGIATAN)                     -->
    <!-- ========================================================================= -->
    <section class="py-16 sm:py-20 bg-slate-50 border-t border-slate-200 text-slate-900 w-full max-w-full block clear-both">
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center space-y-2">
                <span class="text-emerald-400 text-xs font-black uppercase tracking-wider">DOKUMENTASI KEGIATAN</span>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Galeri Foto & Video Kegiatan Kelurahan
                </h2>
            </div>

            <div class="w-full">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-6">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-photo-video text-emerald-400"></i>
                        <span>Dokumentasi Terbaru</span>
                    </h3>
                    <a href="{{ route('galeri') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">
                        Lihat Semua Galeri
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-5">
                    @foreach($galleries as $item)
                    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden group w-full min-w-0 flex flex-col h-full">
                        <div class="relative h-32 sm:h-36 lg:h-40 overflow-hidden shrink-0 w-full bg-slate-100">
                            <img src="{{ asset($item->image_url ?? ($item->image ? 'storage/'.$item->image : 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=800&q=80')) }}"
                                 alt="{{ $item->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            
                            @if($item->type === 'video')
                            <!-- Play Button Overlay -->
                            <button type="button" @click="openVideoPlayer({{ json_encode($item) }})"
                                    class="absolute inset-0 bg-slate-950/40 flex items-center justify-center text-white transition hover:bg-slate-950/60">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-2xl transition transform group-hover:scale-110">
                                    <i class="fas fa-play text-sm sm:text-base pl-0.5 sm:pl-1"></i>
                                </div>
                            </button>
                            <span class="absolute top-2 left-2 bg-rose-600 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow flex items-center gap-1 uppercase">
                                <i class="fas fa-video"></i> Video
                            </span>
                            @else
                            <button type="button" @click="openPhotoLightbox({{ json_encode($item) }})"
                                    class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white gap-2 font-bold text-xs">
                                <i class="fas fa-search-plus text-lg"></i>
                                <span>Perbesar</span>
                            </button>
                            <span class="absolute top-2 left-2 bg-emerald-600 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow flex items-center gap-1 uppercase">
                                <i class="fas fa-camera"></i> Foto
                            </span>
                            @endif
                        </div>
                        <div class="p-3 sm:p-4 space-y-1">
                            <div class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $item->category ?? 'KEGIATAN' }}</div>
                            <h4 class="font-bold text-xs text-slate-900 line-clamp-2">{{ $item->title }}</h4>
                        </div>
                    </div>
                    @endforeach
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
                <h3 class="text-lg font-bold flex items-center gap-2 text-emerald-400">
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
                            <td class="p-3 border-r border-slate-800 text-emerald-400 font-semibold">{{ $row['permohonan'] }} Dokumen</td>
                            <td class="p-3 border-r border-slate-800 text-amber-300 font-semibold">{{ $row['surat_keluar'] }} Dokumen</td>
                            <td class="p-3 border-r border-slate-800 font-semibold">{{ $row['pengaduan'] }} Aduan</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-extrabold text-[10px]">
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

    <!-- 2. Modal Maklumat Pelayanan -->
    <div x-show="maklumatModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        
        <div class="bg-white border border-slate-200 rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 text-slate-800 max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-award text-amber-500"></i>
                    <span>Naskah Maklumat Pelayanan Resmi</span>
                </h3>
                <button type="button" @click="maklumatModalOpen = false" class="text-slate-400 hover:text-slate-700 p-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 text-xs leading-relaxed text-slate-700 border-l-4 border-amber-400 pl-4 bg-amber-50/50 p-4 rounded-r-xl">
                <div class="mb-4 sm:mb-6">
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-emerald-700 tracking-widest">{{ strtoupper($villageProfile['regency'] ?? 'PEMERINTAH KABUPATEN PROBOLINGGO') }} &bull; {{ strtoupper($villageProfile['subdistrict'] ?? 'KECAMATAN KRAKSAAN') }} &bull; {{ strtoupper($villageProfile['village_name'] ?? 'KELURAHAN PATOKAN') }}</span>
                </div>
                
                <h2 class="text-lg sm:text-2xl font-black text-slate-900 mb-6 leading-tight uppercase">Maklumat Pelayanan Publik</h2>
                
                <p class="italic whitespace-pre-wrap">"{{ $maklumatText ?? 'Dengan ini, kami seluruh ASN dan Pegawai Pemerintah Kelurahan Patokan menyatakan sanggup menyelenggarakan pelayanan sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai ketentuan perundang-undangan yang berlaku apabila pelayanan tidak sesuai janji.' }}"</p>
                
                <div class="mt-6 flex items-center justify-center gap-3 text-xs sm:text-sm font-bold text-slate-900">
                    <span>Kepala {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}</span>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200">
                <button type="button" @click="maklumatModalOpen = false" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl">
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
                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl flex items-center gap-2 transition">
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
                            :src="'https://www.youtube.com/embed/' + (activeVideo.youtube_id || activeVideo.id) + '?autoplay=1'"
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
                    <a :href="activeVideo ? ('https://www.youtube.com/watch?v=' + (activeVideo.youtube_id || activeVideo.id)) : '#'" 
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
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                        <i :class="{
                            'fas fa-users': activeStat === 'penduduk',
                            'fas fa-address-card': activeStat === 'kk',
                            'fas fa-map-signs': activeStat === 'rtrw',
                            'fas fa-chart-area': activeStat === 'wilayah',
                            'fas fa-coins text-amber-600 bg-amber-50': activeStat === 'anggaran'
                        }"></i>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 leading-snug"
                            x-text="{
                                'penduduk': 'Rincian Demografi & Jumlah Penduduk',
                                'kk': 'Data Kepala Keluarga (KK)',
                                'rtrw': 'Pembagian Wilayah Rukun Warga & Tetangga',
                                'wilayah': 'Luas & Batas Administrasi Wilayah',
                                'anggaran': 'Transparansi Realisasi Pagu Dana TA 2026'
                            }[activeStat]"></h3>
                        <p class="text-xs text-slate-500">{{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}, {{ $villageProfile['subdistrict'] ?? 'Kecamatan Kraksaan' }}</p>
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
                    * Data dihimpun berdasarkan rekapitulasi Sistem Informasi Administrasi Kependudukan (SIAK) berkala.
                </p>
            </div>

            <!-- 3. Detail RT / RW -->
            <div x-show="activeStat === 'rtrw'" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 bg-emerald-50/60 border border-emerald-100 rounded-2xl text-center">
                        <span class="text-2xl font-black text-emerald-800 block">{{ $villageProfile['territory']['rw'] ?? '08' }}</span>
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Rukun Warga (RW)</span>
                    </div>
                    <div class="p-4 bg-emerald-50/60 border border-emerald-100 rounded-2xl text-center">
                        <span class="text-2xl font-black text-emerald-800 block">{{ $villageProfile['territory']['rt'] ?? '32' }}</span>
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Rukun Tetangga (RT)</span>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                    Seluruh pengantar pengurusan administrasi wajib diawali dari ketua RT dan RW setempat sesuai domisili tempat tinggal sebelum diajukan ke kantor kelurahan.
                </p>
            </div>

            <!-- 4. Detail Luas Wilayah -->
            <div x-show="activeStat === 'wilayah'" class="space-y-4">
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-4 text-center">
                    <span class="text-2xl font-black text-emerald-800 block">{{ $stats['luas'] ?? '3.82 km²' }}</span>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Total Luas Wilayah</span>
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
                <div class="bg-amber-50/50 border border-amber-200 rounded-2xl p-4 text-center">
                    <span class="text-[10px] uppercase font-bold text-amber-700 tracking-wider">Total Pagu Anggaran Kelurahan TA {{ $villageProfile['apbd']['year'] ?? '2026' }}</span>
                    <div class="text-2xl font-black text-amber-700 mt-1">Rp {{ $villageProfile['apbd']['total_budget'] ?? '0' }}</div>
                </div>
                <div class="space-y-2 text-xs">
                    @foreach($villageProfile['apbd']['allocations'] ?? [] as $alloc)
                    <div class="flex justify-between items-center p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-medium text-slate-700 truncate pr-2" title="{{ $alloc['name'] }}">{{ Str::limit($alloc['name'], 35) }}</span>
                        <span class="font-bold text-slate-900">{{ $alloc['pct'] }}%</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('transparansi') }}" class="block text-center w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                    Buka Halaman Transparansi APBD Lengkap &rarr;
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
