@extends('layouts.admin')

@section('title', 'Galeri Dokumentasi Kegiatan')
@section('header-title', 'Galeri Foto Kegiatan Kemasyarakatan')
@section('header-subtitle', 'Unggah dan kelola foto dokumentasi kegiatan kelurahan, kerja bakti, dan sosialisasi')

@section('content')
<div class="space-y-6" x-data="{ 
    uploadModalOpen: false, 
    editModalOpen: false, 
    selectedItem: null, 
    previewModalOpen: false, 
    previewItem: null,
    previewIndex: 0,
    nextPreviewPhoto() {
        if(this.previewItem && this.previewItem.images) {
            this.previewIndex = (this.previewIndex + 1) % this.previewItem.images.length;
        }
    },
    prevPreviewPhoto() {
        if(this.previewItem && this.previewItem.images) {
            this.previewIndex = (this.previewIndex - 1 + this.previewItem.images.length) % this.previewItem.images.length;
        }
    }
}" @keydown.right.window="if(previewModalOpen) nextPreviewPhoto()" @keydown.left.window="if(previewModalOpen) prevPreviewPhoto()">


    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Dokumentasi Foto Kegiatan Warga</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Foto yang diunggah akan otomatis ditampilkan pada section Galeri di beranda portal</p>
        </div>

        <button @click="uploadModalOpen = true" 
                class="px-4 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            <span>Unggah Foto Kegiatan Baru</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between bg-slate-50 p-3 rounded-2xl border border-slate-200">
        <!-- Type Tabs -->
        <div class="flex bg-white rounded-xl p-1 border border-slate-200 shadow-sm shrink-0">
            <a href="{{ request()->fullUrlWithQuery(['type' => null, 'page' => null]) }}" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition {{ !request('type') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">Semua Tipe</a>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'foto', 'page' => null]) }}" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition flex items-center gap-1.5 {{ request('type') == 'foto' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                <i class="fas fa-camera"></i> Foto
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'video', 'page' => null]) }}" class="px-4 py-1.5 rounded-lg text-[11px] font-bold transition flex items-center gap-1.5 {{ request('type') == 'video' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                <i class="fab fa-youtube"></i> Video
            </a>
        </div>

        <!-- Category Dropdown -->
        <div class="w-full sm:w-auto">
            <select onchange="window.location.href=this.value" class="w-full sm:w-auto text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-emerald-600">
                <option value="{{ request()->fullUrlWithQuery(['category_id' => 'all', 'page' => null]) }}" {{ !request('category_id') || request('category_id') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ request()->fullUrlWithQuery(['category_id' => $cat->id, 'page' => null]) }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Photo Gallery Grid (3 or 4 columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($galleries as $item)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 bg-slate-900 overflow-hidden cursor-pointer group/preview" @click="previewItem = { 
                        type: '{{ $item->type }}', 
                        title: {{ json_encode($item->title) }}, 
                        image: '{{ str_starts_with($item->image ?? '', 'http') ? $item->image : asset('storage/' . $item->image) }}', 
                        youtubeId: '{{ $item->youtube_id }}',
                        images: [
                            '{{ str_starts_with($item->image ?? '', 'http') ? $item->image : asset('storage/' . $item->image) }}',
                            ...{{ json_encode($item->images->map(fn($img) => asset('storage/' . $img->image_path))->toArray()) }}
                        ]
                    }; previewIndex = 0; previewModalOpen = true">
                        @if($item->type === 'video')
                            <div class="absolute inset-0 bg-slate-900/40 group-hover/preview:bg-slate-900/60 transition flex items-center justify-center z-10">
                                <div class="w-12 h-12 bg-rose-600 rounded-full flex items-center justify-center shadow-lg transform group-hover/preview:scale-110 transition duration-300">
                                    <i class="fas fa-play text-white ml-1 text-lg"></i>
                                </div>
                            </div>
                        @else
                            <div class="absolute inset-0 bg-slate-900/0 group-hover/preview:bg-slate-900/40 transition flex items-center justify-center z-10 opacity-0 group-hover/preview:opacity-100">
                                <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg transform scale-75 group-hover/preview:scale-100 transition duration-300">
                                    <i class="fas fa-search-plus text-slate-800 text-lg"></i>
                                </div>
                            </div>
                        @endif
                        <img src="{{ str_starts_with($item->image ?? '', 'http') ? $item->image : asset('storage/' . $item->image) }}" 
                             alt="{{ $item->title }}" 
                             class="w-full h-full object-cover group-hover/preview:scale-105 transition duration-500"
                             onerror="this.src='https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=500&q=80'">
                        <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded bg-emerald-950/80 text-emerald-300 font-extrabold text-[10px] uppercase tracking-wider backdrop-blur-sm border border-emerald-500/30 z-20">
                            {{ $item->categoryModel->name ?? $item->category }}
                        </span>
                        @if($item->type === 'foto' && $item->images->count() > 0)
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-slate-900/80 text-white font-extrabold text-[10px] uppercase tracking-wider backdrop-blur-sm border border-white/20 shadow-sm flex items-center gap-1.5 z-20">
                                <i class="fas fa-images text-emerald-400"></i>
                                +{{ $item->images->count() }} Foto
                            </span>
                        @endif
                    </div>

                    <div class="p-4 space-y-1">
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-1">
                            {{ $item->title }}
                        </h3>
                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $item->caption ?? 'Dokumentasi resmi kegiatan masyarakat Kelurahan Patokan.' }}
                        </p>
                    </div>
                </div>

                <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[10px] text-slate-400 font-mono">
                        {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                    </span>

                    <div class="flex items-center gap-1">
                        <!-- Toggle Homepage Button -->
                        <form action="{{ route('admin.galeri.toggle_homepage', $item->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-1.5 rounded-lg transition {{ $item->show_on_homepage ? 'text-amber-500 hover:bg-amber-100' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-200' }}" title="{{ $item->show_on_homepage ? 'Hapus dari Beranda' : 'Tampilkan di Beranda' }}">
                                <svg class="w-4 h-4" fill="{{ $item->show_on_homepage ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            </button>
                        </form>
                        <!-- Edit Button -->
                        <button type="button" 
                                @php
                                    $safePreviewUrl = str_starts_with($item->image ?? '', 'http') ? $item->image : ($item->image ? asset('storage/' . $item->image) : null);
                                @endphp
                                @click="selectedItem = {
                                    id: {{ $item->id }},
                                    title: {{ json_encode($item->title) }},
                                    category_id: {{ $item->category_id ?? 'null' }},
                                    caption: {{ json_encode($item->caption ?? '') }},
                                    image: {{ json_encode($safePreviewUrl) }},
                                    images: {{ json_encode($item->images->map(fn($img) => ['id' => $img->id, 'url' => asset('storage/' . $img->image_path)])->toArray()) }},
                                    type: {{ json_encode($item->type ?? 'foto') }},
                                    show_on_homepage: {{ $item->show_on_homepage ? 'true' : 'false' }},
                                    youtube_url: {{ json_encode($item->youtube_id ? 'https://www.youtube.com/watch?v='.$item->youtube_id : '') }},
                                    updateUrl: {{ json_encode(route('admin.galeri.update', $item->id)) }}
                                }; editModalOpen = true"
                                class="p-1.5 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="Edit Foto Kegiatan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.galeri.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus foto kegiatan ini dari galeri?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-rose-600 hover:text-white hover:bg-rose-600 rounded-lg transition" title="Hapus Foto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="font-semibold text-slate-600">Belum ada foto galeri kegiatan yang diunggah.</p>
            </div>
        @endforelse
    </div>

    <div class="p-4 bg-white rounded-2xl border border-slate-200 text-xs">
        {{ $galleries->links() }}
    </div>

    <!-- MODAL UNGGAH FOTO BARU -->
    <div x-show="uploadModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="uploadModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="uploadModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-gradient-to-r from-emerald-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                        <h3 class="text-base font-bold">Unggah Foto Kegiatan Baru</h3>
                        <button type="button" @click="uploadModalOpen = false" class="text-emerald-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul / Nama Kegiatan *</label>
                            <input type="text" name="title" required placeholder="Contoh: Kerja Bakti Pembersihan Saluran Air RW 03" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Kegiatan *</label>
                            <select name="category_id" required class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600 font-medium">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-data="{ mediaType: 'foto', imageMode: 'file', liveUploadPreview: null }">
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Media *</label>
                            <div class="flex gap-4 mb-4">
                                <label class="flex items-center gap-2 cursor-pointer p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition" :class="{'bg-emerald-50 border-emerald-300': mediaType === 'foto'}">
                                    <input type="radio" name="type" value="foto" x-model="mediaType" class="text-emerald-600 focus:ring-emerald-600">
                                    <span class="text-xs font-bold text-slate-700"><i class="fas fa-camera mr-1 text-emerald-600"></i> Foto Kegiatan</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition" :class="{'bg-rose-50 border-rose-300': mediaType === 'video'}">
                                    <input type="radio" name="type" value="video" x-model="mediaType" class="text-rose-600 focus:ring-rose-600">
                                    <span class="text-xs font-bold text-slate-700"><i class="fab fa-youtube mr-1 text-rose-600"></i> Video YouTube</span>
                                </label>
                            </div>

                            <div x-show="mediaType === 'foto'">
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Foto Dokumentasi Kegiatan *</label>
                                
                                <!-- Toggle Mode -->
                                <div class="flex items-center p-1 bg-slate-100 rounded-lg w-max mb-3 border border-slate-200">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="image_mode" value="file" x-model="imageMode" class="sr-only peer">
                                        <div class="px-3 py-1.5 text-[11px] font-bold text-slate-500 rounded-md peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm transition">Upload File</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="image_mode" value="url" x-model="imageMode" class="sr-only peer">
                                        <div class="px-3 py-1.5 text-[11px] font-bold text-slate-500 rounded-md peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm transition">Gunakan Link URL</div>
                                    </label>
                                </div>

                                <!-- File Input -->
                                <div x-show="imageMode === 'file'">
                                    <input type="file" name="image_files[]" accept="image/jpeg, image/png, image/webp" multiple
                                           @change="const files = $event.target.files; if(files.length > 0) { liveUploadPreview = URL.createObjectURL(files[0]); }"
                                           class="w-full text-xs p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600">
                                    <p class="text-[10px] text-slate-400 mt-1">Anda dapat memilih lebih dari 1 foto sekaligus (Max 10). Format: JPG, PNG, WEBP. Maks 5MB per foto.</p>
                                </div>

                                <!-- URL Input -->
                                <div x-show="imageMode === 'url'" x-cloak>
                                    <input type="url" name="image_url" placeholder="https://contoh.com/gambar.jpg"
                                           @input="liveUploadPreview = $event.target.value"
                                           class="w-full text-xs p-3 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-600 font-mono">
                                    <p class="text-[10px] text-slate-400 mt-1">Masukkan URL link gambar yang valid.</p>
                                </div>

                                <template x-if="liveUploadPreview">
                                    <div class="mt-3">
                                        <img :src="liveUploadPreview" class="w-full h-32 object-cover rounded-xl border border-slate-200 shadow-sm" onerror="this.src='https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=500&q=80'">
                                    </div>
                                </template>
                            </div>

                            <!-- Video Input -->
                            <div x-show="mediaType === 'video'" x-cloak>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Link Video YouTube *</label>
                                <input type="url" name="youtube_url" placeholder="Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="w-full text-xs p-3 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-rose-600 font-mono" :required="mediaType === 'video'">
                                <p class="text-[10px] text-slate-400 mt-1">Masukkan URL lengkap video dari YouTube. Thumbnail akan otomatis diambil.</p>
                            </div>
                        </div>
                            @error('image')
                                <p class="mt-1.5 p-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Foto (Caption)</label>
                            <textarea name="caption" rows="2" placeholder="Penjelasan singkat suasana kegiatan..." class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-600"></textarea>
                        </div>
                        
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer mt-2 bg-emerald-50/50 p-3 rounded-xl border border-emerald-100 hover:bg-emerald-50 transition">
                                <input type="checkbox" name="show_on_homepage" value="1" class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-600">
                                <span class="text-xs font-bold text-emerald-800">Tampilkan Album/Video ini di Halaman Beranda Utama</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold rounded-xl shadow">Unggah Foto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT FOTO KEGIATAN -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="editModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="editModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <template x-if="selectedItem">
                    <form :action="selectedItem.updateUrl" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-gradient-to-r from-sky-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                            <h3 class="text-base font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>Edit Foto Kegiatan</span>
                            </h3>
                            <button type="button" @click="editModalOpen = false" class="text-sky-300 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul / Nama Kegiatan *</label>
                                <input type="text" name="title" x-model="selectedItem.title" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-600 font-semibold">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Kegiatan *</label>
                                <select name="category_id" x-model="selectedItem.category_id" required class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-600 font-medium bg-white">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-data="{ mediaType: selectedItem ? selectedItem.type : 'foto', imageMode: 'file', localLivePreview: selectedItem ? selectedItem.image : null }" x-init="$watch('selectedItem', value => { if(value) { localLivePreview = value.image; mediaType = value.type || 'foto'; } })">
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Media *</label>
                                <div class="flex gap-4 mb-4">
                                    <label class="flex items-center gap-2 cursor-pointer p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition" :class="{'bg-sky-50 border-sky-300': mediaType === 'foto'}">
                                        <input type="radio" name="type" value="foto" x-model="mediaType" class="text-sky-600 focus:ring-sky-600">
                                        <span class="text-xs font-bold text-slate-700"><i class="fas fa-camera mr-1 text-sky-600"></i> Foto Kegiatan</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition" :class="{'bg-rose-50 border-rose-300': mediaType === 'video'}">
                                        <input type="radio" name="type" value="video" x-model="mediaType" class="text-rose-600 focus:ring-rose-600">
                                        <span class="text-xs font-bold text-slate-700"><i class="fab fa-youtube mr-1 text-rose-600"></i> Video YouTube</span>
                                    </label>
                                </div>

                                <div x-show="mediaType === 'foto'">
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Sampul & Foto Album</label>
                                    
                                    <!-- Toggle Mode -->
                                    <div class="flex items-center p-1 bg-slate-100 rounded-lg w-max mb-3 border border-slate-200">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="image_mode" value="file" x-model="imageMode" class="sr-only peer">
                                            <div class="px-3 py-1.5 text-[11px] font-bold text-slate-500 rounded-md peer-checked:bg-white peer-checked:text-sky-700 peer-checked:shadow-sm transition">Upload File Baru</div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="image_mode" value="url" x-model="imageMode" class="sr-only peer">
                                            <div class="px-3 py-1.5 text-[11px] font-bold text-slate-500 rounded-md peer-checked:bg-white peer-checked:text-sky-700 peer-checked:shadow-sm transition">Ganti Sampul (URL)</div>
                                        </label>
                                    </div>

                                <!-- File Input -->
                                    <div x-show="imageMode === 'file'" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                        <div class="flex items-center gap-2 mb-2 text-emerald-800 font-bold text-xs uppercase tracking-wider">
                                            <i class="fas fa-plus-circle"></i> Tambahkan Foto Baru ke Album
                                        </div>
                                        <input type="file" name="image_files[]" accept="image/jpeg, image/png, image/webp" multiple
                                               class="w-full text-xs p-2.5 border border-white rounded-lg bg-white shadow-sm focus:ring-2 focus:ring-emerald-600">
                                        <p class="text-[10px] text-emerald-600/80 mt-1 font-medium">Bisa pilih &gt; 1 file sekaligus. Foto-foto ini akan <b>ditambahkan</b> ke dalam album, tidak menimpa foto lama.</p>
                                    </div>

                                    <!-- URL Input -->
                                    <div x-show="imageMode === 'url'" x-cloak>
                                        <input type="url" name="image_url" placeholder="https://contoh.com/gambar.jpg"
                                               class="w-full text-xs p-3 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-sky-600 font-mono">
                                        <p class="text-[10px] text-slate-400 mt-1">Masukkan URL gambar untuk mengubah gambar <b>Sampul Utama</b>.</p>
                                    </div>
                                    
                                    <!-- Daftar Foto dalam Album -->
                                    <div class="mt-4 pt-4 border-t border-slate-100">
                                        <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                            <i class="fas fa-images text-emerald-600"></i>
                                            Isi Album Foto (<span x-text="selectedItem.images.length + (selectedItem.image ? 1 : 0)"></span>)
                                        </div>
                                        
                                        <div class="grid grid-cols-3 gap-2 mt-2 max-h-48 overflow-y-auto pr-1 pb-1">
                                            <!-- Cover Image -->
                                            <template x-if="selectedItem.image">
                                                <div class="relative group h-20 rounded-lg overflow-hidden border border-slate-200 shadow-sm bg-slate-900">
                                                    <span class="absolute top-1 left-1 px-1.5 py-0.5 text-[8px] bg-emerald-600 text-white font-bold rounded z-10 uppercase shadow-sm">Sampul</span>
                                                    <img :src="selectedItem.image" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition">
                                                </div>
                                            </template>
                                            
                                            <!-- Additional Images -->
                                            <template x-for="img in selectedItem.images" :key="img.id">
                                                <div class="relative group h-20 rounded-lg overflow-hidden border border-slate-200 shadow-sm bg-slate-900">
                                                    <img :src="img.url" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition flex flex-col justify-end p-1.5">
                                                        <form :action="'{{ url('admin/galeri') }}/' + selectedItem.id + '/image/' + img.id" method="POST" onsubmit="return confirm('Hapus foto ini dari album?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="w-full py-1.5 rounded border border-rose-500/50 bg-rose-600/90 hover:bg-rose-600 text-white font-bold text-[10px] flex items-center justify-center gap-1.5 shadow-lg transition transform hover:scale-105 backdrop-blur-sm">
                                                                <i class="fas fa-trash-alt"></i> Hapus Foto
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="mediaType === 'video'" x-cloak>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Link Video YouTube *</label>
                                    <input type="url" name="youtube_url" x-model="selectedItem.youtube_url" placeholder="Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="w-full text-xs p-3 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-rose-600 font-mono" :required="mediaType === 'video'">
                                    <p class="text-[10px] text-slate-400 mt-1">Masukkan URL lengkap video dari YouTube. Thumbnail akan diperbarui jika link diganti.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Foto (Caption)</label>
                                <textarea name="caption" x-model="selectedItem.caption" rows="2" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-600 font-medium"></textarea>
                            </div>
                            
                            <div>
                                <label class="flex items-center gap-2 cursor-pointer mt-2 bg-sky-50/50 p-3 rounded-xl border border-sky-100 hover:bg-sky-50 transition">
                                    <input type="checkbox" name="show_on_homepage" value="1" x-model="selectedItem.show_on_homepage" class="w-5 h-5 text-sky-600 border-slate-300 rounded focus:ring-sky-600">
                                    <span class="text-xs font-bold text-sky-800">Tampilkan Album/Video ini di Halaman Beranda Utama</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white font-extrabold rounded-xl shadow transition transform hover:-translate-y-0.5">Simpan Perubahan</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- PREVIEW MODAL -->
    <div x-show="previewModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <div x-show="previewModalOpen" @click="previewModalOpen = false; previewItem = null" class="fixed inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity"></div>
        
        <div x-show="previewModalOpen" class="relative w-full max-w-4xl bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-700 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-5 py-3 border-b border-slate-700/50 flex justify-between items-center bg-slate-900/50 absolute top-0 left-0 right-0 z-20">
                <h3 class="font-bold text-white text-sm truncate pr-4" x-text="previewItem?.title"></h3>
                <button @click="previewModalOpen = false; previewItem = null" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center transition shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-1 w-full relative overflow-y-auto pt-14 bg-black flex items-center justify-center min-h-[50vh]">
                <template x-if="previewItem?.type === 'video'">
                    <div class="w-full h-full aspect-video">
                        <iframe :src="'https://www.youtube.com/embed/' + previewItem.youtubeId + '?autoplay=1'" class="w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </template>
                
                <template x-if="previewItem?.type === 'foto'">
                    <div class="relative w-full h-[75vh] flex items-center justify-center overflow-hidden">
                        <!-- Navigasi Kiri -->
                        <button type="button" @click="prevPreviewPhoto()" x-show="previewItem?.images?.length > 1" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white transition flex items-center justify-center z-20 shadow-lg">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <img :src="previewItem.images[previewIndex]" class="max-w-full max-h-full object-contain mx-auto shadow-2xl rounded" alt="Album Image">

                        <!-- Navigasi Kanan -->
                        <button type="button" @click="nextPreviewPhoto()" x-show="previewItem?.images?.length > 1" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white transition flex items-center justify-center z-20 shadow-lg">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Bottom Thumbnail Navigation -->
            <template x-if="previewItem?.type === 'foto' && previewItem?.images?.length > 1">
                <div class="p-4 bg-slate-900 border-t border-slate-800 flex justify-center gap-2 overflow-x-auto no-scrollbar shrink-0">
                    <template x-for="(imgUrl, idx) in previewItem.images" :key="idx">
                        <button @click="previewIndex = idx" 
                                class="w-12 h-12 shrink-0 rounded-lg overflow-hidden border-2 transition"
                                :class="previewIndex === idx ? 'border-emerald-500 opacity-100' : 'border-transparent opacity-50 hover:opacity-100'">
                            <img :src="imgUrl" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
