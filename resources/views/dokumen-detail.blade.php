@extends('layouts.app')

@section('title', $document->name . ' - Dokumen Publik - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen" 
    x-data="{ 
        search: '', 
        pdfModalOpen: false,
        activePdf: null,
        files: @js($document->files->map(function($f) {
            return [
                'id' => $f->id,
                'name' => $f->name,
                'date' => $f->created_at->format('d M Y'),
                'url' => asset('storage/' . $f->file_path)
            ];
        })),
        get filteredFiles() {
            if (this.search === '') return this.files;
            const term = String(this.search).toLowerCase();
            return this.files.filter(f => String(f.name).toLowerCase().includes(term));
        },
        openPdf(file) {
            this.activePdf = file;
            this.pdfModalOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closePdf() {
            this.pdfModalOpen = false;
            this.activePdf = null;
            document.body.classList.remove('overflow-hidden');
        }
    }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center shrink-0">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">
                        {{ $document->name }}
                    </h1>
                    <div class="text-sm text-slate-500 flex items-center gap-2">
                        <span class="inline-block px-2.5 py-1 bg-slate-200 text-slate-700 text-[10px] font-bold rounded">{{ $document->code ?? 'DOKUMEN' }}</span>
                        <span>• Dipublikasikan pada {{ $document->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('dokumen') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <!-- Daftar File -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800"><i class="fas fa-file-pdf text-slate-600 mr-2"></i> File yang Tersedia</h3>
                        <span class="text-xs font-bold px-2 py-1 bg-slate-100 text-slate-700 rounded-lg">{{ count($document->files) }} File</span>
                    </div>
                    
                    <div class="p-6">
                        @if(count($document->files) > 0)
                            <!-- Pencarian -->
                            <div class="mb-5 relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" x-model="search" placeholder="Cari nama, tahun, atau bulan (Misal: 2024 atau Januari)..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-slate-500 focus:border-slate-500 bg-slate-50 focus:bg-white transition-colors">
                            </div>

                            <div class="space-y-3">
                                <template x-for="file in filteredFiles" :key="file.id">
                                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-xl hover:border-slate-300 hover:shadow-sm transition-all group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white border border-rose-100 text-rose-500 rounded-lg flex items-center justify-center shrink-0">
                                                <i class="fas fa-file-pdf text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm group-hover:text-slate-700 transition-colors" x-text="file.name"></div>
                                                <div class="text-[10px] text-slate-400 font-medium" x-text="'Diunggah: ' + file.date"></div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" 
                                                    @click="openPdf(file)" 
                                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors flex items-center gap-2 shadow-sm border border-slate-200 cursor-pointer">
                                                <i class="fas fa-book-open text-slate-600"></i>
                                                <span class="hidden sm:inline">Baca</span>
                                            </button>
                                            <a :href="file.url" download class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                                                <i class="fas fa-download"></i>
                                                <span class="hidden sm:inline">Unduh</span>
                                            </a>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="filteredFiles.length === 0" class="text-center py-6 text-slate-500 text-sm italic" style="display: none;">
                                    File yang Anda cari tidak ditemukan.
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-folder-open text-4xl text-slate-300 mb-3"></i>
                                <p class="text-slate-500 font-medium text-sm">Belum ada file yang diunggah untuk dokumen ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-bold text-slate-800 text-sm mb-4 pb-3 border-b border-slate-100">Keterangan Dokumen</h4>
                    @if($document->description)
                        <div class="prose prose-sm prose-slate max-w-none text-xs">
                            {!! $document->description !!}
                        </div>
                    @else
                        <p class="text-slate-400 text-xs italic">Tidak ada keterangan / deskripsi.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL PRATINJAU DOKUMEN (BACA LANGSUNG DI WEB TANPA BUKA TAB BARU)        -->
    <!-- ========================================================================= -->
    <div x-show="pdfModalOpen" 
         x-cloak
         @keydown.escape.window="closePdf()"
         class="fixed inset-0 z-50 overflow-hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 md:p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop click to close -->
        <div class="fixed inset-0" @click="closePdf()"></div>

        <!-- Dialog Box Container -->
        <div class="relative bg-white rounded-2xl w-full max-w-6xl h-[92vh] max-h-[92vh] shadow-2xl border border-slate-200 flex flex-col z-10 overflow-hidden"
             @click.stop>
            
            <!-- Header Toolbar -->
            <div class="px-4 sm:px-6 py-3.5 bg-slate-900 text-white flex items-center justify-between gap-4 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center shrink-0 text-base">
                        <i class="fas fa-file-pdf"></i>
                    </span>
                    <div class="min-w-0">
                        <h4 class="font-bold text-sm text-white truncate" x-text="activePdf ? activePdf.name : 'Membaca Dokumen'"></h4>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400">
                            <span class="inline-flex items-center gap-1 text-emerald-400 font-semibold">
                                <i class="fas fa-check-circle text-[10px]"></i> Dokumen Resmi
                            </span>
                            <span>•</span>
                            <span x-text="activePdf ? ('Diunggah: ' + activePdf.date) : ''"></span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Header -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Tombol Unduh Berkas -->
                    <template x-if="activePdf">
                        <a :href="activePdf.url" download
                           class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-download text-xs"></i>
                            <span class="hidden sm:inline">Unduh PDF</span>
                        </a>
                    </template>

                    <!-- Tombol Tab Baru (Alternatif opsional) -->
                    <template x-if="activePdf">
                        <a :href="activePdf.url" target="_blank" 
                           class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition" 
                           title="Buka di tab terpisah">
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </template>

                    <!-- Tombol Tutup -->
                    <button type="button" @click="closePdf()" 
                            class="p-2 text-slate-400 hover:text-white hover:bg-rose-600/80 rounded-lg transition cursor-pointer"
                            title="Tutup (Esc)">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- PDF Viewer Body Frame -->
            <div class="flex-1 bg-slate-950 p-1 sm:p-2 relative flex flex-col overflow-hidden">
                <template x-if="pdfModalOpen && activePdf">
                    <iframe :src="activePdf.url + '#toolbar=1&navpanes=0&view=FitH'" 
                            class="w-full h-full rounded-xl bg-white border-0 shadow-inner" 
                            title="Pratinjau Dokumen">
                    </iframe>
                </template>

                <!-- Helper Fallback for Devices that do not support embedded PDF -->
                <div class="px-4 py-2 bg-slate-900/90 text-slate-400 text-[11px] flex flex-col sm:flex-row items-center justify-between gap-2 border-t border-slate-800/80 shrink-0">
                    <div class="flex items-center gap-2 text-center sm:text-left">
                        <i class="fas fa-info-circle text-blue-400"></i>
                        <span>Gunakan kontrol bilah alat di atas dokumen untuk memperbesar, memperkecil, atau mencetak.</span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px]">
                        <span>Tekan tombol <strong>Esc</strong> atau tombol Tutup untuk kembali ke halaman</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
