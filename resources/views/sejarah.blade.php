@extends('layouts.app')

@section('title', 'Sejarah Kelurahan - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Sejarah Singkat {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}</h1>
                <p class="text-sm text-slate-500">Menelusuri akar sejarah, asal usul nama, dan perjalanan era pemerintahan di Kraksaan.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm shrink-0">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        {{-- Full Width Main Container --}}
        <div class="w-full">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-8">
                
                {{-- Title Header --}}
                <div class="border-b border-slate-100 pb-6">
                    <span class="inline-flex items-center gap-2 text-slate-700 text-xs font-bold uppercase tracking-widest px-3 py-1 bg-slate-50 rounded-full border border-slate-200 mb-3">
                        <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                        Etimologi & Asal Usul Nama
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                        Dari Sebuah "Patok Batas Batu" Menjadi Pusat Pemerintahan Ibu Kota
                    </h2>
                </div>

                {{-- Image & Narrative Grid --}}
                <div class="space-y-6">
                    <div class="relative rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-slate-900">
                        <img src="{{ !empty($villageProfile['history_hero_image']) ? asset('storage/' . $villageProfile['history_hero_image']) : asset('images/sejarah/history_hero.png') }}" 
                             alt="Monumen Sejarah {{ $villageProfile['village_name'] ?? 'Kelurahan Semampir' }}" 
                             class="w-full h-72 sm:h-96 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <span class="px-2.5 py-0.5 rounded bg-slate-600 text-white font-bold text-[10px] uppercase tracking-wider">Monumen & Pilar Batas</span>
                            <p class="text-xs text-slate-200 mt-1 font-medium">Patok Batu Penanda Wilayah Kraksaan Era Klasik Jawa</p>
                        </div>
                    </div>

                    <div class="prose prose-slate text-xs sm:text-sm text-slate-600 leading-relaxed space-y-4 font-normal max-w-none">
                        {!! $villageProfile['history_text'] ?? '<p>Nama <strong>"Semampir"</strong> memiliki latar belakang sejarah etimologi yang berakar dari kata dasar <em>"Patok"</em>, yang berarti titik acuan penanda atau tiang pembatas wilayah.</p><p>Pada masa era kadipaten abad ke-18 dan masa pemerintahan kolonial di pesisir utara Probolinggo, wilayah ini difungsikan sebagai titik ukur nol dan acuan batas administrasi tanah wilayah Kraksaan. Di lokasi ini ditanam sebuah <strong>patok batu hitam besar</strong> yang menjadi semampir para musafir, pedagang, dan petugas karesidenan saat mengukur jarak jalur pos (De Grote Postweg).</p><p>Lambat laun, pemukiman di sekitar pilar patok penanda tersebut berkembang pesat dan akrab disapa warga dengan sebutan <strong>Dusun Semampir</strong>. Berkat letaknya yang sangat strategis di persimpangan jalan utama dan dekat dengan pusat perniagaan, wilayah ini terus bertumbuh menjadi desa pusat kegiatan masyarakat Kraksaan.</p>' !!}
                    </div>

                    {{-- Highlight Box --}}
                    <div class="p-4 bg-slate-50/60 border-l-4 border-slate-600 rounded-r-2xl space-y-1">
                        <h4 class="text-xs font-bold text-slate-900">Filosofi Nama {{ str_replace('Kelurahan ', '', $villageProfile['village_name'] ?? 'Semampir') }}:</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            "{{ str_replace('Kelurahan ', '', $villageProfile['village_name'] ?? 'Semampir') }}" tidak hanya bermakna pilar fisik penanda wilayah, tetapi juga mengandung filosofi moral bahwa pemerintah kelurahan senantiasa menjadi <strong>semampir (pedoman utama)</strong> dalam memberikan pelayanan publik yang jujur, adil, dan terpercaya bagi masyarakat.
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection
