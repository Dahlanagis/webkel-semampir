@extends('layouts.admin')

@section('title', 'Media Library & Pengelola Berkas')
@section('header-title', 'Media Library & Pengelola Berkas Publik')
@section('header-subtitle', 'Kelola foto, gambar banner, dan dokumen PDF yang terunggah di server')

@section('content')
<div class="space-y-6" x-data="{ uploadModalOpen: false, copyToast: false }">

    <!-- Alert Status -->

    @if(session('warning'))
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center gap-3 shadow-sm">
            <div class="w-7 h-7 rounded-xl bg-amber-600 text-white flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Action Header & Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.media.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama berkas atau folder..." class="pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-slate-600 shadow-sm w-60">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select name="type" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white font-medium shadow-sm">
                <option value="">Semua Tipe File</option>
                <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar (JPG/PNG/WEBP)</option>
                <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>Dokumen PDF</option>
            </select>
            <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition">Filter</button>
        </form>

        <button @click="uploadModalOpen = true" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            <span>Unggah Berkas Media Baru</span>
        </button>
    </div>

    <!-- Media Library Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($files as $file)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <!-- Preview Container -->
                    <div class="relative h-40 bg-slate-900 overflow-hidden flex items-center justify-center">
                        @if($file['is_image'])
                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @elseif($file['is_pdf'])
                            <div class="text-center p-4">
                                <div class="w-14 h-14 bg-rose-500 text-white rounded-2xl flex items-center justify-center font-black text-sm mx-auto shadow-md">PDF</div>
                                <span class="text-[10px] text-slate-300 font-mono mt-2 block truncate max-w-[180px]">{{ $file['name'] }}</span>
                            </div>
                        @else
                            <div class="text-center p-4">
                                <div class="w-14 h-14 bg-slate-700 text-white rounded-2xl flex items-center justify-center font-black text-xs uppercase mx-auto shadow-md">{{ $file['extension'] }}</div>
                            </div>
                        @endif

                        <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded bg-slate-950/80 text-white font-mono text-[9px] uppercase tracking-wider backdrop-blur-sm border border-slate-700">
                            {{ $file['extension'] }} • {{ round($file['size'] / 1024, 1) }} KB
                        </span>
                    </div>

                    <!-- File Info -->
                    <div class="p-3.5 space-y-1 text-xs">
                        <div class="font-bold text-slate-900 truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                        <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $file['path'] }}">Path: {{ $file['path'] }}</div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="px-3.5 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[10px] text-slate-400 font-mono">
                        {{ date('d M Y', $file['last_modified']) }}
                    </span>

                    <div class="flex items-center gap-1.5">
                        <!-- Copy URL Button -->
                        <button type="button" 
                                @click="navigator.clipboard.writeText('{{ $file['url'] }}'); copyToast = true; setTimeout(() => copyToast = false, 2000)"
                                class="px-2 py-1 bg-sky-50 hover:bg-sky-100 text-sky-800 font-bold text-[10px] rounded-lg transition border border-sky-200 flex items-center gap-1"
                                title="Salin URL Berkas">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span>Salin URL</span>
                        </button>

                        <!-- Open/Download Link -->
                        <a href="{{ $file['url'] }}" target="_blank" class="p-1 text-slate-600 hover:text-slate-900 rounded-lg transition" title="Buka / Download">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.media.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas media ini?')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="path" value="{{ $file['path'] }}">
                            <button type="submit" class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Berkas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="font-semibold text-slate-600">Belum ada berkas media di server.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200 text-xs">
        {{ $files->links() }}
    </div>

    <!-- MODAL UNGGAH BERKAS BARU -->
    <div x-show="uploadModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-6">
            <div x-show="uploadModalOpen"  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="uploadModalOpen" class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 my-8">
                <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" x-data="{ mediaPreview: null }">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                        <h3 class="text-base font-bold">Unggah Berkas Media Baru</h3>
                        <button type="button" @click="uploadModalOpen = false" class="text-slate-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Folder Penyimpanan</label>
                            <select name="folder" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-slate-600">
                                <option value="uploads">uploads (Berkas Umum)</option>
                                <option value="documents">documents (Dokumen & Form)</option>
                                <option value="gallery">gallery (Foto Kegiatan)</option>
                                <option value="posts">posts (Gambar Berita)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Berkas *</label>
                            <input type="file" name="file" required accept="image/*,application/pdf,.pdf"
                                   @change="const f = $event.target.files[0]; if(f && f.type.startsWith('image/')) { mediaPreview = URL.createObjectURL(f); } else { mediaPreview = null; }" 
                                   class="w-full p-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-slate-600">
                            <p class="text-[10px] text-slate-400 mt-1">Format: Gambar (JPG/PNG/WEBP) atau PDF. Maksimal 10MB.</p>
                        </div>

                        <template x-if="mediaPreview">
                            <div class="mt-2.5 p-2.5 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-3">
                                <img :src="mediaPreview" class="w-14 h-14 rounded-xl object-cover border-2 border-slate-500 shadow-sm shrink-0">
                                <div class="min-w-0 text-[11px]">
                                    <div class="font-bold text-slate-800">Pratinjau Gambar Media</div>
                                    <div class="text-[10px] text-sky-600 font-medium">Siap diunggah ke server</div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold rounded-xl shadow">Unggah Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- COPY TOAST NOTIFICATION -->
    <div x-show="copyToast" x-cloak class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-2.5 rounded-2xl shadow-xl border border-slate-700 text-xs font-bold flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>URL Berkas Berhasil Disalin!</span>
    </div>

</div>
@endsection
