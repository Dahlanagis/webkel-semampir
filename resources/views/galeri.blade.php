@extends('layouts.app')

@section('title', 'Galeri Kegiatan - ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $jsonGalleries = $galleries->map(function($g) {
        $images = [['url' => $g->image_url]]; // cover image is first
        if ($g->images) {
            foreach ($g->images as $img) {
                $images[] = ['url' => asset('storage/' . $img->image_path)];
            }
        }
        return [
            'id' => $g->id,
            'title' => $g->title,
            'category' => $g->category ?? 'Kegiatan',
            'caption' => $g->caption ?? '',
            'image_url' => $g->image_url,
            'images' => $images,
            'created_at' => $g->created_at ? $g->created_at->format('d/m/Y') : '',
        ];
    })->values();
@endphp

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen"
         x-data="{
            modalOpen: false,
            videoModalOpen: false,
            activeVideo: null,
            activeAlbum: null,
            currentIndex: 0,
            items: {{ json_encode($jsonGalleries ?? []) }},
            openModal(index) {
                this.activeAlbum = this.items[index];
                this.currentIndex = 0;
                this.modalOpen = true;
                document.body.style.overflow = 'hidden';
            },
            closeModal() {
                this.modalOpen = false;
                this.activeAlbum = null;
                document.body.style.overflow = 'auto';
            },
            nextPhoto() {
                if(this.activeAlbum && this.activeAlbum.images) {
                    this.currentIndex = (this.currentIndex + 1) % this.activeAlbum.images.length;
                }
            },
            prevPhoto() {
                if(this.activeAlbum && this.activeAlbum.images) {
                    this.currentIndex = (this.currentIndex - 1 + this.activeAlbum.images.length) % this.activeAlbum.images.length;
                }
            }
         }"
         @keydown.escape.window="closeModal()"
         @keydown.right.window="if(modalOpen) nextPhoto()"
         @keydown.left.window="if(modalOpen) prevPhoto()">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Galeri & Dokumentasi Kegiatan</h1>
                <p class="text-sm text-slate-500">Dokumentasi foto kegiatan pembangunan, pelayanan publik, gotong royong, dan posyandu Kelurahan Patokan.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm shrink-0">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        {{-- Tabs Foto / Video --}}
        <div class="flex items-center gap-4 mb-6 border-b border-slate-200 px-2">
            <a href="{{ route('galeri', ['type' => 'foto']) }}" class="px-4 py-3 text-sm font-bold transition-all {{ $type === 'foto' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-slate-500 hover:text-slate-700 hover:border-b-2 hover:border-slate-300' }}">
                <i class="fas fa-camera mr-2"></i>Album Foto
            </a>
            <a href="{{ route('galeri', ['type' => 'video']) }}" class="px-4 py-3 text-sm font-bold transition-all {{ $type === 'video' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-slate-500 hover:text-slate-700 hover:border-b-2 hover:border-slate-300' }}">
                <i class="fas fa-video mr-2"></i>Video Kegiatan
            </a>
        </div>

        {{-- Main Full Width Container --}}
        <div class="w-full space-y-6">

            @if($type === 'foto')
                {{-- Filter Pill Header Bar --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex items-center gap-2 overflow-x-auto no-scrollbar">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1.5">
                        <i class="fas fa-filter text-emerald-600"></i> Filter Album:
                    </span>

                    {{-- Semua Foto --}}
                    <a href="{{ route('galeri', ['type' => 'foto']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0
                              {{ !request('kategori') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
                        <span>Semua Foto</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] {{ !request('kategori') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                            {{ $totalPhotos ?? count($galleries) }}
                        </span>
                    </a>

                    {{-- Categories --}}
                    @foreach($categories as $cat)
                        @php
                            $catName = is_object($cat) ? $cat->category : $cat;
                            $catTotal = is_object($cat) ? $cat->total : null;
                            $isActive = request('kategori') === $catName;
                        @endphp
                        <a href="{{ route('galeri', ['type' => 'foto', 'kategori' => $catName]) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0
                                  {{ $isActive ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 bg-slate-200 hover:bg-slate-300' }}">
                            <span>{{ $catName }}</span>
                            @if($catTotal)
                                <span class="px-1.5 py-0.5 rounded text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $catTotal }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Photos Grid --}}
                @if($galleries->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                            @foreach($galleries as $index => $gal)
                                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden flex flex-col group cursor-pointer hover:shadow-md transition duration-200 h-full"
                                     @click="openModal({{ $index }})">
                                    
                                    {{-- Photo Image Box --}}
                                    <div class="h-48 sm:h-52 relative overflow-hidden bg-slate-900 shrink-0 w-full">
                                        <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider absolute top-2.5 left-2.5 z-10 shadow-sm">
                                            {{ $gal->category ?? 'KEGIATAN' }}
                                        </span>

                                        <img src="{{ $gal->image_url }}"
                                             alt="{{ $gal->title }}"
                                             loading="lazy"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                                        <div class="absolute inset-0 bg-slate-950/50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center gap-2 text-white">
                                            <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg">
                                                <i class="fas fa-images text-base"></i>
                                            </div>
                                            <span class="text-xs font-bold bg-slate-900/80 px-3 py-1 rounded-full border border-white/20">
                                                Buka Album ({{ $gal->images->count() + 1 }} Foto)
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Caption Box --}}
                                    <div class="p-3.5 bg-white border-t border-slate-100 flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="font-bold text-slate-800 text-xs line-clamp-2 leading-snug group-hover:text-emerald-600 transition">
                                                {{ $gal->title }}
                                            </h3>
                                            @if($gal->caption)
                                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-1 leading-relaxed">
                                                    {{ $gal->caption }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                                            <span class="text-emerald-600 font-bold"><i class="fas fa-camera mr-1"></i>Dokumentasi</span>
                                            <span>{{ $gal->created_at ? $gal->created_at->format('d/m/Y') : '' }}</span>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6 flex justify-center">
                            {{ $galleries->links() }}
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 shadow-sm">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-200">
                            <i class="fas fa-images text-2xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Foto Belum Tersedia</h3>
                        <p class="text-sm text-slate-500 mb-4">Belum ada foto kegiatan dalam kategori album ini.</p>
                        <a href="{{ route('galeri') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition inline-block shadow-sm">
                            Lihat Semua Foto
                        </a>
                    </div>
                @endif
            @elseif($type === 'video')
                {{-- Videos Grid --}}
                @if($galleries->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                            @foreach($galleries as $vid)
                                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden flex flex-col group cursor-pointer hover:shadow-md transition duration-200 h-full">
                                    <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-900 shrink-0 w-full">
                                        <img src="{{ $vid->image_url }}"
                                             alt="{{ $vid->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        
                                        <!-- Play Button Overlay -->
                                        <button type="button" @click="activeVideo = {{ json_encode($vid) }}; videoModalOpen = true"
                                                class="absolute inset-0 bg-slate-950/50 flex items-center justify-center text-white transition hover:bg-slate-950/70">
                                            <div class="w-12 h-12 rounded-full bg-emerald-600 text-slate-900 flex items-center justify-center shadow-2xl transition transform group-hover:scale-110">
                                                <i class="fas fa-play text-base pl-1"></i>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="p-3.5 bg-white border-t border-slate-100 flex-1 flex flex-col justify-between">
                                        <h3 class="font-bold text-slate-800 text-xs line-clamp-2 leading-snug group-hover:text-emerald-600 transition">
                                            {{ $vid->title }}
                                        </h3>
                                        <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                                            <span class="text-rose-600 font-bold"><i class="fab fa-youtube mr-1"></i>Video</span>
                                            <span class="font-mono">{{ $vid->created_at ? $vid->created_at->format('d/m/Y') : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6 flex justify-center">
                            {{ $galleries->links() }}
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 shadow-sm">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-200">
                            <i class="fas fa-video text-2xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Video Belum Tersedia</h3>
                        <p class="text-sm text-slate-500 mb-4">Belum ada video kegiatan yang diunggah.</p>
                    </div>
                @endif
            @endif

        </div>

    </div>

    {{-- Lightbox Modal --}}
    <div x-show="modalOpen"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4"
         style="display: none;">

        <!-- Lebar disamakan dengan beranda (max-w-2xl) dan rounded-3xl -->
        <div class="relative bg-slate-900 text-white rounded-3xl overflow-hidden max-w-2xl w-full border border-slate-800 shadow-2xl flex flex-col"
             @click.away="closeModal()">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-4 bg-slate-950 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="bg-emerald-600 text-white text-[10px] font-black uppercase px-2.5 py-0.5 rounded-md"
                          x-text="activeAlbum?.category || 'KEGIATAN'"></span>
                    <span class="text-slate-400 text-xs font-bold">
                        Foto <span x-text="currentIndex + 1"></span> dari <span x-text="activeAlbum?.images?.length || 1"></span>
                    </span>
                </div>

                <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-white transition flex items-center justify-center focus:outline-none">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Modal Image Viewport (Kunci Tinggi Tetap: h-72 sm:h-96) --}}
            <div class="relative bg-black h-72 sm:h-96 flex items-center justify-center overflow-hidden w-full shrink-0">
                <!-- Navigasi Kiri -->
                <button type="button" @click="prevPhoto()" x-show="activeAlbum?.images?.length > 1" class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-slate-950/70 hover:bg-emerald-600 text-white transition flex items-center justify-center z-20 focus:outline-none shadow-md">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>

                <!-- Gambar dibuat object-cover agar mengisi kotak tanpa gepeng/berubah dimensi -->
                <img :src="activeAlbum?.images[currentIndex]?.url" :alt="activeAlbum?.title" class="w-full h-full object-contain transition duration-300">

                <!-- Navigasi Kanan -->
                <button type="button" @click="nextPhoto()" x-show="activeAlbum?.images?.length > 1" class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-slate-950/70 hover:bg-emerald-600 text-white transition flex items-center justify-center z-20 focus:outline-none shadow-md">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
            </div>

            {{-- Modal Footer (Dilengkapi tombol unduh agar seragam dengan beranda) --}}
            <div class="p-6 h-36 flex flex-col justify-between shrink-0 bg-slate-900 border-t border-slate-800">
                <div>
                    <h3 x-text="activeAlbum?.title" class="font-bold text-base text-white line-clamp-1"></h3>
                    <p x-text="activeAlbum?.caption || activeAlbum?.created_at" class="text-xs text-slate-400 line-clamp-2 mt-1"></p>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <!-- Thumbnails for multiple images -->
                    <div class="flex gap-2 overflow-x-auto no-scrollbar max-w-sm">
                        <template x-for="(img, idx) in activeAlbum?.images" :key="idx">
                            <button @click="currentIndex = idx" 
                                    class="w-10 h-10 shrink-0 rounded-lg overflow-hidden border-2 transition"
                                    :class="currentIndex === idx ? 'border-emerald-500 opacity-100' : 'border-transparent opacity-50 hover:opacity-100'">
                                <img :src="img.url" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                    
                    <a :href="activeAlbum?.images[currentIndex]?.url || '#'" download target="_blank"
                       class="px-4 py-2 shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl flex items-center gap-2 transition">
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
</section>

@endsection
