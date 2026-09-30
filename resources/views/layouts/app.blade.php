<!DOCTYPE html>
<html lang="id" class="scroll-smooth"
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
    <meta name="description" content="@yield('meta_description', 'Portal Resmi Pemerintah Kelurahan Semampir, Kecamatan Kraksaan, Kabupaten Probolinggo. Layanan publik mandiri, pengajuan surat online, berita, dan transparansi.')">
    <meta name="keywords" content="Kelurahan Semampir, Kraksaan, Probolinggo, Portal Desa, Pelayanan Publik, SKTM, SKU, KTP, APBDes">
    <meta name="author" content="Pemerintah Kelurahan Semampir">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons (for DLH components) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: inline-flex;
            white-space: nowrap;
            animation: marquee 35s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }

        /* Custom slim sleek scrollbar for dropdown menus */
        .dropdown-scrollbar {
            overflow-y: auto !important;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }
        .dropdown-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .dropdown-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 9999px;
            margin: 6px 0;
        }
        .dropdown-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        .dropdown-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        /* Audio Equalizer animation for Google Voice */
        @keyframes soundbar {
            0%, 100% { height: 4px; }
            50% { height: 14px; }
        }
        .animate-soundbar-1 { animation: soundbar 0.8s ease-in-out infinite; }
        .animate-soundbar-2 { animation: soundbar 1.1s ease-in-out infinite 0.2s; }
        .animate-soundbar-3 { animation: soundbar 0.7s ease-in-out infinite 0.4s; }
        .animate-soundbar-4 { animation: soundbar 1.0s ease-in-out infinite 0.1s; }
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
