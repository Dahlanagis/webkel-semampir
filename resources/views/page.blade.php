@extends('layouts.app')

@section('title', $page->title . ' - ' . ($villageProfile['village_name'] ?? 'Kelurahan Patokan'))

@section('content')
<section class="w-full py-8 sm:py-16 bg-white relative">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mb-4">{{ $page->title }}</h1>
            <div class="w-20 h-1.5 bg-emerald-500 mx-auto rounded-full"></div>
            <div class="mt-4 text-sm text-slate-500">
                Terakhir diperbarui pada {{ $page->updated_at->format('d F Y') }}
            </div>
        </div>

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
                        <div class="prose prose-slate prose-lg max-w-none text-slate-700">
                            {{-- We use nl2br since text block uses textarea now --}}
                            {!! nl2br(e($block['content'])) !!}
                        </div>
                    
                    @elseif($block['type'] === 'raw_html')
                        <div class="prose prose-slate prose-lg max-w-none">
                            {!! $block['content'] !!}
                        </div>

                    @elseif($block['type'] === 'image')
                        @if(!empty($block['url']))
                        <figure class="my-8 relative group rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-slate-50">
                            <img src="{{ $block['url'] }}" alt="{{ $block['caption'] ?? 'Gambar' }}" class="w-full h-auto object-cover max-h-[600px]">
                            @if(!empty($block['caption']))
                            <figcaption class="text-center text-sm p-4 bg-slate-50 text-slate-600 font-medium">
                                {{ $block['caption'] }}
                            </figcaption>
                            @endif
                        </figure>
                        @endif

                    @elseif($block['type'] === 'cards')
                        @if(!empty($block['items']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
                            @foreach($block['items'] as $item)
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md hover:border-emerald-300 transition-all group">
                                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-emerald-700 transition-colors">{{ $item['title'] ?? '' }}</h3>
                                <p class="text-slate-600 leading-relaxed">{{ $item['content'] ?? '' }}</p>
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
                                'emerald' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-500', 'text' => 'text-emerald-800', 'icon' => 'fa-check-circle'],
                                'rose' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-500', 'text' => 'text-rose-800', 'icon' => 'fa-exclamation-circle'],
                            ];
                            $c = $colors[$style] ?? $colors['amber'];
                        @endphp
                        <div class="{{ $c['bg'] }} border-l-4 {{ $c['border'] }} p-5 my-8 rounded-r-2xl shadow-sm">
                            <div class="flex items-start">
                                <div class="shrink-0 mt-0.5">
                                    <i class="fas {{ $c['icon'] }} {{ $c['text'] }} text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    @if(!empty($block['title']))
                                        <h3 class="{{ $c['text'] }} font-bold text-lg">{{ $block['title'] }}</h3>
                                    @endif
                                    <div class="{{ $c['text'] }} opacity-90 leading-relaxed {{ !empty($block['title']) ? 'mt-2' : '' }}">
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
</section>
@endsection
