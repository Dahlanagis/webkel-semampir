<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth"
    x-data="{
        textSize: 'normal',
        highContrast: false,
        toggleTextSize() {
            this.textSize = this.textSize === 'normal' ? 'large' : 'normal';
        },
        resetTextSize() {
            this.textSize = 'normal';
        },
        toggleContrast() {
            this.highContrast = !this.highContrast;
        }
    }"
    :class="{ 'text-base': textSize === 'large', 'contrast-125 filter': highContrast }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $systemSettings['app_name'] . ' - ' . ($systemSettings['app_subtitle'] ?? 'Pemerintah Desa'))</title>

    <!-- Favicon / Logo Web Title -->
    <link rel="icon" type="image/png" href="{{ !empty($systemSettings['app_logo']) ? asset('storage/' . $systemSettings['app_logo']) : asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ !empty($systemSettings['app_logo']) ? asset('storage/' . $systemSettings['app_logo']) : asset('images/logo.png') }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Portal Resmi Pemerintah Kelurahan Patokan, Kecamatan Kraksaan, Kabupaten Probolinggo. Layanan publik mandiri, pengajuan surat online, berita, dan transparansi.')">
    <meta name="keywords" content="Kelurahan Patokan, Kraksaan, Probolinggo, Portal Desa, Pelayanan Publik, SKTM, SKU, KTP, APBDes">
    <meta name="author" content="Pemerintah Kelurahan Patokan">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN & Alpine.js -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html {
            scroll-behavior: smooth !important;
            scroll-padding-top: 6rem;
        }
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Hide Scrollbar for Chrome, Safari, and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide Scrollbar for IE, Edge, and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 25s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between" :class="{ 'bg-black text-yellow-300': highContrast }">

    <!-- HEADER & NAVBAR PARTIAL -->
    @include('layouts.partials.header')

    <!-- MAIN CONTENT BODY -->
    <main class="grow">
        @yield('content')
    </main>

    <!-- FOOTER PARTIAL -->
    @include('layouts.partials.footer')

</body>
</html>
