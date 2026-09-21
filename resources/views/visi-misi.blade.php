@extends('layouts.app')

@section('title', 'Visi & Misi - ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Visi & Misi Pembangunan</h1>
                <p class="text-sm text-slate-500">Arah kebijakan, cita-cita, dan komitmen pelayanan Pemerintah {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm shrink-0">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        {{-- Full Width Main Container --}}
        <div class="w-full">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-8">
                
                {{-- Header Banner --}}
                <div class="border-b border-slate-100 pb-6">
                    <span class="inline-flex items-center gap-2 text-emerald-700 text-xs font-bold uppercase tracking-widest px-3 py-1 bg-emerald-50 rounded-full border border-emerald-200 mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        Pedoman Pembangunan Kelurahan
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                        Visi & Misi {{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}, {{ $villageProfile['subdistrict'] ?? 'Kecamatan Kraksaan' }}
                    </h2>
                </div>

                {{-- VISI CARD --}}
                <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-md border border-emerald-800 relative overflow-hidden">
                    <div class="flex items-center gap-2 text-emerald-300 font-extrabold text-xs uppercase tracking-wider mb-2">
                        <i class="fas fa-bullseye text-base"></i> VISI {{ strtoupper($villageProfile['village_name'] ?? 'KELURAHAN PATOKAN') }}
                    </div>
                    <div class="text-base sm:text-lg font-bold text-white leading-relaxed prose prose-invert max-w-none">
                        {!! $villageProfile['vision'] ?? '"Terwujudnya Kelurahan Patokan yang Maju, Sejahtera, Mandiri, Berbudaya, dan Pelayanan Publik Prima Berbasis Transparansi serta Gotong Royong Warga."' !!}
                    </div>
                </div>

                {{-- MISI CARD --}}
                <div class="space-y-4">
                    <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i class="fas fa-list-check text-emerald-600"></i>
                        <span>MISI PEMBANGUNAN {{ strtoupper(str_replace('Kelurahan ', '', $villageProfile['village_name'] ?? 'PATOKAN')) }}</span>
                    </h3>

                    <div class="prose prose-slate text-xs sm:text-sm text-slate-700 leading-relaxed font-normal max-w-none">
                        {!! $villageProfile['mission'] ?? '
                        <ol class="list-decimal ml-5 space-y-3">
                            <li class="pl-2"><strong>Pelayanan Prima:</strong> Meningkatkan kualitas pelayanan administrasi kependudukan yang ramah, cepat, transparan, dan bebas dari pungutan liar.</li>
                            <li class="pl-2"><strong>Pemberdayaan Ekonomi:</strong> Mendorong pertumbuhan ekonomi warga melalui pembinaan Usaha Mikro, Kecil, dan Menengah (UMKM) lokal Kelurahan Patokan.</li>
                            <li class="pl-2"><strong>Ketertiban & Kebersihan:</strong> Memelihara keamanan, ketertiban, kebersihan lingkungan, serta mempererat kerukunan hidup antar warga RT dan RW.</li>
                            <li class="pl-2"><strong>Tata Kelola Transparan:</strong> Memperkuat transparansi tata kelola pemerintahan kelurahan serta pembangunan infrastruktur yang tepat guna.</li>
                        </ol>
                        ' !!}
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection
