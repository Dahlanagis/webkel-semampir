@extends('layouts.app')

@section('title', $document->name . ' - Dokumen Publik - ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('content')

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen" 
    x-data="{ 
        search: '', 
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
        }
    }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center shrink-0">
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
                        <h3 class="font-bold text-slate-800"><i class="fas fa-file-pdf text-emerald-600 mr-2"></i> File yang Tersedia</h3>
                        <span class="text-xs font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg">{{ count($document->files) }} File</span>
                    </div>
                    
                    <div class="p-6">
                        @if(count($document->files) > 0)
                            <!-- Pencarian -->
                            <div class="mb-5 relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" x-model="search" placeholder="Cari nama, tahun, atau bulan (Misal: 2024 atau Januari)..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 focus:bg-white transition-colors">
                            </div>

                            <div class="space-y-3">
                                <template x-for="file in filteredFiles" :key="file.id">
                                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-xl hover:border-emerald-300 hover:shadow-sm transition-all group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white border border-rose-100 text-rose-500 rounded-lg flex items-center justify-center shrink-0">
                                                <i class="fas fa-file-pdf text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 transition-colors" x-text="file.name"></div>
                                                <div class="text-[10px] text-slate-400 font-medium" x-text="'Diunggah: ' + file.date"></div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a :href="file.url" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors flex items-center gap-2 shadow-sm border border-slate-200">
                                                <i class="fas fa-book-open text-emerald-600"></i>
                                                <span class="hidden sm:inline">Baca</span>
                                            </a>
                                            <a :href="file.url" download class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-2 shadow-sm">
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
</section>

@endsection
