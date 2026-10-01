@extends('layouts.app')

@section('title', $page->title . ' - ' . ($villageProfile['village_name'] ?? 'Kelurahan Semampir'))

@section('content')

<!-- FontAwesome Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="w-full py-8 sm:py-12 bg-slate-50 border-t border-slate-200 relative min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Top --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">{{ $page->title }}</h1>
                @if(!empty($page->subtitle))
                    <div class="text-sm text-slate-500">
                        {!! $page->subtitle !!}
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold text-sm transition-all shadow-sm">
                    <i class="fas fa-arrow-left text-slate-400"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>

        {{-- Main Outer Container: SaaS / GovTech Card --}}
        <div class="w-full space-y-6">

            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 space-y-8">
                
                {{-- Header Section inside Card --}}
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    @if($page->badge_text)
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-slate-50 text-slate-700 border border-slate-200 text-[11px] font-black uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                        {{ $page->badge_text }}
                    </span>
                    @endif
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $page->title }}
                    </h2>
                    @if($page->subtitle)
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                        {{ $page->subtitle }}
                    </p>
                    @endif
                </div>

                @if($page->type === 'sotk')
                    {{-- Org Chart Canvas (overflow-x-auto for responsiveness) --}}
                    <div class="overflow-x-auto pb-8 custom-scrollbar">
                        <div class="min-w-[920px] flex flex-col items-center py-6">

                            {{-- 1. TOP NODE (Chief / Lurah) --}}
                            <div class="flex flex-col items-center z-10 relative">
                                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-56 hover:border-slate-300 transition">
                                    {{-- Foto Lingkaran Lurah --}}
                                    <div class="w-24 h-24 rounded-full border-2 border-slate-800 p-0.5 mb-2.5 bg-slate-50 shrink-0 shadow-sm">
                                        <img src="{{ !empty($villageProfile['head_photo']) ? asset('storage/' . $villageProfile['head_photo']) . '?v=' . time() : asset('images/sotk/lurah.png') }}" 
                                            alt="Lurah Semampir" 
                                            class="w-full h-full object-cover rounded-full">
                                    </div>
                                    
                                    <h3 class="font-bold text-xs text-slate-900 leading-snug">
                                        {{ $villageProfile['head_name'] ?? 'Latif Hasan Asyari, SH.' }}
                                    </h3>
                                    @if(!empty($villageProfile['head_nip']))
                                        <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['head_nip'] }}</p>
                                    @endif
                                    
                                    <div class="mt-1.5">
                                        <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-3 py-1 rounded-md tracking-wider inline-block">
                                            LURAH SEMAMPIR
                                        </span>
                                    </div>
                                </div>

                                {{-- Garis Penghubung Turun --}}
                                <div class="w-px h-10 bg-slate-300"></div>
                            </div>

                            {{-- 3. BOTTOM NODES (4 Columns Grid) --}}
                            <div class="w-full max-w-4xl relative">
                                {{-- Horizontal Branch Line --}}
                                <div class="w-full h-px bg-slate-300 absolute top-0 left-0 right-0"></div>

                                {{-- 4 Evenly Spaced Columns --}}
                                <div class="grid grid-cols-4 gap-4 sm:gap-6 pt-8">

                                    {{-- Node 1: Secretary --}}
                                    <div class="flex flex-col items-center relative">
                                        <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>
                                        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-300 transition group">
                                            <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                                <img src="{{ !empty($villageProfile['sekel_photo']) ? asset('storage/' . $villageProfile['sekel_photo']) : asset('images/sotk/sekel.png') }}" 
                                                    alt="Sekretaris Kelurahan" 
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                                {{ $villageProfile['sekel_name'] ?? 'Budi Santoso, S.STP' }}
                                            </h5>
                                            @if(!empty($villageProfile['sekel_nip']))
                                                <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['sekel_nip'] }}</p>
                                            @endif
                                            <div class="mt-2">
                                                <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                    {{ strtoupper($villageProfile['sekel_role'] ?? 'SEKRETARIS KELURAHAN') }}
                                                </span>
                                            </div>
                                            <span class="text-slate-500 text-[10px] font-medium mt-2 inline-block">
                                                Sekretariat Kelurahan
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Node 2: Section Head 1 (Pemerintahan) --}}
                                    <div class="flex flex-col items-center relative">
                                        <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>
                                        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-400 transition group">
                                            <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                                <img src="{{ !empty($villageProfile['kasi_pem_photo']) ? asset('storage/' . $villageProfile['kasi_pem_photo']) : asset('images/sotk/kasi_pem.png') }}" 
                                                    alt="Kasi Pemerintahan" 
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                                {{ $villageProfile['kasi_pem_name'] ?? 'Arief Rachman, S.IP' }}
                                            </h5>
                                            @if(!empty($villageProfile['kasi_pem_nip']))
                                                <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_pem_nip'] }}</p>
                                            @endif
                                            <div class="mt-2">
                                                <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                    {{ strtoupper($villageProfile['kasi_pem_role'] ?? 'KASI PEMERINTAHAN & TRANTIB') }}
                                                </span>
                                            </div>
                                            <span class="text-slate-500 text-[10px] font-medium mt-2 inline-block">
                                                Seksi Pemerintahan
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Node 3: Section Head 2 (Kesra) --}}
                                    <div class="flex flex-col items-center relative">
                                        <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>
                                        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-400 transition group">
                                            <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                                <img src="{{ !empty($villageProfile['kasi_kesra_photo']) ? asset('storage/' . $villageProfile['kasi_kesra_photo']) : asset('images/sotk/kasi_kesra.png') }}" 
                                                    alt="Kasi Kesra" 
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                                {{ $villageProfile['kasi_kesra_name'] ?? 'Siti Aminah, S.Sos' }}
                                            </h5>
                                            @if(!empty($villageProfile['kasi_kesra_nip']))
                                                <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_kesra_nip'] }}</p>
                                            @endif
                                            <div class="mt-2">
                                                <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                    {{ strtoupper($villageProfile['kasi_kesra_role'] ?? 'KASI PELAYANAN & KESRA') }}
                                                </span>
                                            </div>
                                            <span class="text-slate-500 text-[10px] font-medium mt-2 inline-block">
                                                Seksi Sosial & Kesra
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Node 4: Section Head 3 (Ekbang) --}}
                                    <div class="flex flex-col items-center relative">
                                        <div class="w-px h-8 bg-slate-300 absolute -top-8 left-1/2 -translate-x-1/2"></div>
                                        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-400 transition group">
                                            <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                                <img src="{{ !empty($villageProfile['kasi_ekbang_photo']) ? asset('storage/' . $villageProfile['kasi_ekbang_photo']) : asset('images/sotk/kasi_ekbang.png') }}" 
                                                    alt="Kasi Ekbang" 
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <h5 class="font-bold text-xs text-slate-900 leading-tight capitalize">
                                                {{ $villageProfile['kasi_ekbang_name'] ?? 'Bambang Wijaya, S.T' }}
                                            </h5>
                                            @if(!empty($villageProfile['kasi_ekbang_nip']))
                                                <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $villageProfile['kasi_ekbang_nip'] }}</p>
                                            @endif
                                            <div class="mt-2">
                                                <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-2xs">
                                                    {{ strtoupper($villageProfile['kasi_ekbang_role'] ?? 'KASI PEMBERDAYAAN & EKBANG') }}
                                                </span>
                                            </div>
                                            <span class="text-slate-500 text-[10px] font-medium mt-2 inline-block">
                                                Seksi Perekonomian
                                            </span>
                                        </div>
                                    </div>

                                </div>

                                {{-- Additional Members Tier (if any) --}}
                                @if(!empty($villageProfile['sotk_members']) && count($villageProfile['sotk_members']) > 0)
                                    <div class="pt-8 relative">
                                        <div class="w-px h-8 bg-slate-300 absolute top-0 left-1/2 -translate-x-1/2"></div>
                                        <div class="text-center mb-5 pt-3">
                                            <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-extrabold uppercase px-3 py-1 rounded-full tracking-wider shadow-2xs">
                                                <i class="fas fa-users text-slate-500"></i>
                                                <span>STAF PELAYANAN & JABATAN FUNGSIONAL ({{ count($villageProfile['sotk_members']) }})</span>
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                                            @foreach($villageProfile['sotk_members'] as $m)
                                                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col items-center text-center w-full hover:border-slate-400 transition group">
                                                    <div class="w-16 h-16 rounded-full border-2 border-slate-800 overflow-hidden shadow-sm mb-2 bg-slate-100">
                                                        @if(!empty($m['photo']))
                                                            <img src="{{ asset('storage/' . $m['photo']) }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center bg-slate-200 text-slate-600 font-black text-lg">
                                                                {{ strtoupper(substr($m['name'] ?? 'S', 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <h5 class="font-bold text-xs text-slate-900 leading-tight">
                                                        {{ $m['name'] }}
                                                    </h5>
                                                    @if(!empty($m['nip']))
                                                        <p class="text-[10px] text-slate-500 font-mono mt-0.5 font-medium">NIP. {{ $m['nip'] }}</p>
                                                    @endif
                                                    <div class="mt-2">
                                                        <span class="bg-slate-900 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-full tracking-wider inline-block shadow-sm">
                                                            {{ $m['position'] ?? 'STAF' }}
                                                        </span>
                                                    </div>
                                                    <span class="border border-slate-300 text-slate-500 text-[10px] font-semibold px-2.5 py-0.5 rounded-full mt-2.5 inline-block">
                                                        Kelompok Jabatan Fungsional
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>


                @else
                    {{-- Standard Page Content --}}
                    
                    @if($page->banner_image)
                    <div class="w-full rounded-2xl overflow-hidden shadow-sm border border-slate-200 mt-6">
                        <img src="{{ asset('storage/' . $page->banner_image) }}" alt="Banner {{ $page->title }}" class="w-full h-auto object-cover max-h-96">
                    </div>
                    @endif

                    <div class="prose prose-slate prose-sm sm:prose-base lg:prose-lg max-w-none prose-headings:font-bold prose-a:text-slate-600 hover:prose-a:text-slate-700 prose-img:rounded-xl">
                        @php
                            $blocks = [];
                            if (!empty($page->content)) {
                                $decoded = json_decode($page->content, true);
                                if (is_array($decoded)) {
                                    $blocks = $decoded;
                                } else {
                                    // Fallback to old TinyMCE html
                                    $blocks = [['type' => 'raw_html', 'content' => $page->content]];
                                }
                            }
                        @endphp

                        @if(empty($blocks))
                            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-200">
                                <i class="fas fa-tools text-4xl text-slate-300 mb-4"></i>
                                <h3 class="text-lg font-bold text-slate-700 mb-2">Halaman Sedang Dalam Pengembangan</h3>
                                <p class="text-slate-500">Konten untuk halaman ini sedang disusun oleh admin kelurahan.</p>
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach($blocks as $block)
                                    
                                    @if($block['type'] === 'text')
                                        <div class="text-slate-700 text-editor-content">
                                            {!! $block['content'] !!}
                                        </div>
                                    
                                    @elseif($block['type'] === 'raw_html')
                                        <div>
                                            {!! $block['content'] !!}
                                        </div>

                                    @elseif($block['type'] === 'image')
                                        @if(!empty($block['url']))
                                        <figure class="my-8 relative group rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-slate-50">
                                            <img src="{{ $block['url'] }}" alt="{{ $block['caption'] ?? 'Gambar' }}" class="w-full h-auto object-cover max-h-[600px] !m-0 rounded-none">
                                            @if(!empty($block['caption']))
                                            <figcaption class="text-center text-sm p-4 bg-slate-50 text-slate-600 font-medium">
                                                {{ $block['caption'] }}
                                            </figcaption>
                                            @endif
                                        </figure>
                                        @endif

                                    @elseif($block['type'] === 'cards')
                                        @if(!empty($block['items']))
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8 not-prose">
                                            @foreach($block['items'] as $item)
                                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md hover:border-slate-300 transition-all group">
                                                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-slate-700 transition-colors">{{ $item['title'] ?? '' }}</h3>
                                                <p class="text-slate-600 leading-relaxed m-0">{{ $item['content'] ?? '' }}</p>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif

                                    @elseif($block['type'] === 'alert')
                                        @php
                                            $style = $block['style'] ?? 'amber';
                                            $colors = [
                                                'amber' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-500', 'text' => 'text-amber-800', 'icon' => 'fa-exclamation-triangle'],
                                                'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-500', 'text' => 'text-blue-800', 'icon' => 'fa-info-circle'],
                                                'slate' => ['bg' => 'bg-slate-50', 'border' => 'border-slate-500', 'text' => 'text-slate-800', 'icon' => 'fa-check-circle'],
                                                'rose' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-500', 'text' => 'text-rose-800', 'icon' => 'fa-exclamation-circle'],
                                            ];
                                            $c = $colors[$style] ?? $colors['amber'];
                                        @endphp
                                        <div class="{{ $c['bg'] }} border-l-4 {{ $c['border'] }} p-5 my-8 rounded-r-2xl shadow-sm not-prose">
                                            <div class="flex items-start">
                                                <div class="shrink-0 mt-0.5">
                                                    <i class="fas {{ $c['icon'] }} {{ $c['text'] }} text-lg"></i>
                                                </div>
                                                <div class="ml-4">
                                                    @if(!empty($block['title']))
                                                        <h3 class="{{ $c['text'] }} font-bold text-lg m-0 mb-1">{{ $block['title'] }}</h3>
                                                    @endif
                                                    <div class="{{ $c['text'] }} opacity-90 leading-relaxed">
                                                        {!! nl2br(e($block['content'] ?? '')) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @endforeach
                            </div>
                        @endif
                    </div>

                @endif
            </div>

        </div>

    </div>
</section>

@endsection
