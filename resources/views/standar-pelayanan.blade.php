@extends('layouts.app')

@section('title', 'Standar Pelayanan Publik - ' . ($settings['village_name'] ?? 'Kelurahan Patokan'))

@section('content')

<!-- Alpine.js logic -->
<section class="w-full py-6 sm:py-8 bg-slate-50 min-h-screen"
         x-data="{ 
            viewMode: 'detail',
            services: {{ \Illuminate\Support\Js::from($services) }},
            activeId: new URLSearchParams(window.location.search).get('id') ? parseInt(new URLSearchParams(window.location.search).get('id')) : ({{ $services->count() > 0 ? $services->first()->id : 'null' }}),
            get activeService() {
                return this.services.find(s => s.id === this.activeId) || this.services[0];
            },
            getPdfUrl(service) {
                if (!service || !service.pdf_document) {
                    return null;
                }
                const clean = service.pdf_document.replace(/\\/g, '/');
                return clean.startsWith('/') ? clean : ('/storage/' + clean);
            },
            getRequirements(service) {
                if (!service || !service.required_documents) return [];
                // Jika sudah berbentuk array
                if (Array.isArray(service.required_documents)) {
                    return service.required_documents;
                }
                // Jika string dipisah koma atau newline
                if (typeof service.required_documents === 'string') {
                    return service.required_documents
                        .split(/,|\n/)
                        .map(item => item.trim())
                        .filter(item => item.length > 0);
                }
                return [];
            },
            copyLink() {
                const url = window.location.origin + window.location.pathname + '?id=' + this.activeId;
                navigator.clipboard.writeText(url).then(() => {
                    alert('Tautan disalin: ' + url);
                });
            }
         }"
         @hashchange.window="activeId = parseInt(new URLSearchParams(window.location.search).get('id')) || activeId; viewMode = 'detail'"
         x-init="$watch('activeId', value => { viewMode = 'detail'; window.history.replaceState(null, null, '?id=' + value); })">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Standar Pelayanan Publik & SOP</h1>
                <p class="text-xs sm:text-sm text-slate-500">Pilih dokumen layanan pada daftar di sebelah kanan untuk melihat SOP.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-sm shrink-0">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <template x-if="services.length === 0">
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-200">
                    <i class="fas fa-folder-open text-2xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Standar Pelayanan</h3>
                <p class="text-sm text-slate-500">Informasi SOP layanan kelurahan belum ditambahkan oleh administrator.</p>
            </div>
        </template>

        <template x-if="services.length > 0">
            <div class="flex flex-col lg:flex-row gap-6 items-start w-full">
                
                <!-- KOLOM KIRI: Detail Layanan & PDF Viewer (8 Kolom) -->
                <div class="w-full lg:w-[68%] space-y-5">
                    
                    <!-- Card Utama -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        
                        <!-- Toolbar Atas (Gaya Gambar ke-2) -->
                        <div class="bg-white px-5 py-3.5 border-b border-slate-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[11px] font-black rounded uppercase tracking-wider shrink-0">PDF</span>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-800 text-sm truncate" x-text="viewMode === 'pdf' ? 'Pratinjau Dokumen Publik' : (activeService ? activeService.name : '')"></h3>
                                    <p class="text-[11px] text-slate-500 truncate" x-text="(activeService && activeService.description) || 'Kelurahan Patokan, Kecamatan Kraksaan'"></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Tombol Kembali jika sedang di mode PDF -->
                                <button type="button" x-show="viewMode === 'pdf'" @click="viewMode = 'detail'" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                                    <i class="fas fa-arrow-left"></i> Info Detail
                                </button>
                                <!-- <button type="button" @click="copyLink()" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                                    <i class="fas fa-link"></i> Salin Tautan
                                </button>
                                <a :href="getPdfUrl(activeService) || '{{ asset('docs/sop-pelayanan.pdf') }}'" target="_blank" download class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-download"></i> Unduh
                                </a> -->
                            </div>
                        </div>

                        <!-- Mode 1: Detail Info -->
                        <div x-show="viewMode === 'detail'" class="p-6 space-y-6 bg-white">
                            <!-- Persyaratan -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center text-[10px] font-bold">1</span>
                                    <span>Persyaratan Dokumen</span>
                                </h4>
                                <template x-if="getRequirements(activeService).length > 0">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        <template x-for="(doc, idx) in getRequirements(activeService)" :key="idx">
                                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl px-3.5 py-3">
                                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-check text-[10px]"></i>
                                                </div>
                                                <span class="text-xs font-medium text-slate-700 leading-snug" x-text="doc"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="getRequirements(activeService).length === 0">
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs text-slate-400 italic text-center">
                                        Tidak ada berkas persyaratan khusus.
                                    </div>
                                </template>
                            </div>

                            <!-- Ketentuan Pelayanan -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center text-[10px] font-bold">2</span>
                                    <span>Ketentuan Pelayanan</span>
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Jam Operasional</span>
                                        <p class="text-xs font-bold text-slate-700 mt-2" x-text="activeService.operational_hours || 'Senin - Jumat'"></p>
                                        <span class="text-[10px] text-slate-400">Jam Kerja</span>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Estimasi Waktu</span>
                                        <p class="text-xs font-bold text-slate-700 mt-2" x-text="activeService.estimated_time || 'Tergantung Layanan'"></p>
                                        <span class="text-[10px] text-slate-400">Hari Kerja</span>
                                    </div>
                                    <div class="bg-emerald-50/70 border border-emerald-100 rounded-xl p-3.5">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Biaya Pelayanan</span>
                                        <p class="text-xs font-black text-emerald-600 mt-2" x-text="activeService.cost || 'GRATIS'"></p>
                                        <span class="text-[10px] text-emerald-600/70">Bebas Pungutan</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Buka Pratinjau PDF -->
                            <div class="pt-2 border-t border-slate-100">
                                <button type="button" @click="viewMode = 'pdf'" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-sm flex items-center gap-2">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Baca Dokumen SOP (PDF)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Mode 2: PDF Viewer Frame (Persis Layout Gambar ke-2) -->
                        <div x-show="viewMode === 'pdf'" x-cloak class="w-full bg-[#2a303c] p-3 sm:p-5 flex flex-col" style="min-height: 720px;">
                            <template x-if="viewMode === 'pdf'">
                                <iframe :key="activeId + '-pdf'"
                                        :src="(getPdfUrl(activeService) || '{{ asset('docs/sop-pelayanan.pdf') }}') + '#toolbar=1&navpanes=0&view=FitH'" 
                                        class="w-full rounded-xl shadow-2xl bg-white"
                                        style="height: 680px; border: none;"
                                        title="Pratinjau Dokumen SOP"></iframe>
                            </template>
                        </div>

                    </div>

                    <!-- Banner Maklumat Bebas Biaya -->
                    <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-sm">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-emerald-950 text-xs sm:text-sm mb-1">Maklumat Pelayanan Bebas Biaya (GRATIS)</h4>
                            <p class="text-xs text-emerald-800/90 leading-relaxed">
                                Seluruh pengurusan surat keterangan dan administrasi kependudukan di Kelurahan Patokan tidak dipungut biaya apapun (GRATIS). Pastikan Anda membawa berkas persyaratan lengkap saat datang ke kantor kelurahan.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN: Daftar SOP & Bantuan (4 Kolom) -->
                <div class="w-full lg:w-[32%] space-y-5">
                    
                    <!-- Card Daftar SOP -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-white px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <h3 class="font-bold text-slate-800 text-xs sm:text-sm">Daftar Standar Pelayanan & SOP</h3>
                            </div>
                            <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-100" x-text="services.length + ' Dokumen'"></span>
                        </div>
                        
                        <!-- List Group Navigasi -->
                        <div class="max-h-[380px] overflow-y-auto p-2 space-y-1.5 custom-scrollbar">
                            <template x-for="(srv, index) in services" :key="srv.id">
                                <button type="button" @click="activeId = srv.id"
                                        class="w-full text-left p-2.5 rounded-xl transition flex items-start gap-2.5 focus:outline-none"
                                        :class="activeId === srv.id ? 'bg-emerald-600 text-white shadow' : 'text-slate-600 hover:bg-slate-50'">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5"
                                          :class="activeId === srv.id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'">
                                        <span x-text="index + 1"></span>
                                    </span>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-xs leading-snug line-clamp-2" x-text="srv.name"></h4>
                                        <p x-show="activeId !== srv.id" class="text-[10px] mt-0.5 line-clamp-1 opacity-70" :class="activeId === srv.id ? 'text-emerald-100' : 'text-slate-400'" x-text="srv.description || 'SOP Layanan'"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Card Bantuan WhatsApp -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="bg-emerald-50 w-7 h-7 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fab fa-whatsapp text-emerald-600 text-[15px]"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-xs">Bantuan & Informasi</h3>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-3">
                            Butuh informasi mengenai persyaratan kelurahan? Silakan tanyakan melalui kontak resmi WhatsApp.
                        </p>
                        <a href="https://wa.me/6281234567890" target="_blank" class="w-full py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fab fa-whatsapp text-sm"></i>
                            <span>Hubungi WhatsApp</span>
                        </a>
                    </div>

                </div>

            </div>
        </template>
        
    </div>
</section>

<style>
/* Custom Scrollbar for SOP List */
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 6px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 6px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

@endsection