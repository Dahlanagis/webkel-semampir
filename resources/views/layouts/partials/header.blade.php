<!-- ========================================== -->
<!-- 1. MAIN NAVBAR                             -->
<!-- ========================================== -->
@php
    $rawVillageName = $villageProfile['village_name'] ?? ($systemSettings['app_name'] ?? 'SEMAMPIR');
    // Bersihkan kata Kelurahan atau Desa jika sudah ada agar tidak tercetak dua kali
    $cleanVillageName = trim(preg_replace('/^(kelurahan|desa)\s+/i', '', $rawVillageName));
    if (empty($cleanVillageName)) {
        $cleanVillageName = 'SEMAMPIR';
    }
@endphp
<nav x-data="{ 
        mobileMenuOpen: false,
        openDropdown: null,
        toggleDropdown(menu) {
            this.openDropdown = this.openDropdown === menu ? null : menu;
        }
    }" 
    @click.away="openDropdown = null; mobileMenuOpen = false"
    class="bg-white text-slate-800 border-b border-slate-200/90 shadow-sm sticky top-0 z-50 transition-all"
    style="background-color: #ffffff !important;">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between min-h-[4.25rem] sm:min-h-[4.75rem] py-2 gap-3 sm:gap-6">
            
            <!-- Brand / Logo Header (Non-colliding, elegant fixed footprint) -->
            <a href="{{ route('home') }}" 
               @click="playMenuVoice($event, 'Beranda', '{{ route('home') }}')"
               class="flex items-center gap-2.5 sm:gap-3 group shrink-0 mr-2 lg:mr-4 xl:mr-8 select-none">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white border border-slate-200/90 ring-2 ring-slate-100/90 flex items-center justify-center p-1.5 shadow-2xs group-hover:scale-105 group-hover:shadow-md transition duration-300 shrink-0">
                    <img src="{{ !empty($systemSettings['app_logo']) ? asset('storage/' . $systemSettings['app_logo']) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlwlIShkVajC2C_tEglw59FLYjmw5n-E1vAgqplpW75A&s=10' }}" 
                         alt="Logo Kelurahan" 
                         class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col justify-center shrink-0">
                    <div class="font-black text-sm sm:text-base tracking-tight text-black leading-tight whitespace-nowrap">
                        <span>KELURAHAN {{ strtoupper($cleanVillageName) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[9px] sm:text-[10px] text-slate-500 font-semibold tracking-wide uppercase mt-0.5 whitespace-nowrap">
                        <span>KECAMATAN KRAKSAAN</span>
                        <span class="text-slate-300">•</span>
                        <span>PROBOLINGGO</span>
                    </div>
                </div>
            </a>

            <!-- Mobile Toggle Hamburger Button & Quick Portal Admin -->
            <div class="flex items-center gap-2 lg:hidden shrink-0">
                @auth
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}" 
                       class="px-3 py-1.5 rounded-xl font-extrabold text-xs flex items-center gap-1.5 shadow-sm transition active:scale-95 bg-black text-white border border-slate-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Portal Admin</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-3 py-1.5 rounded-xl font-extrabold text-xs flex items-center gap-1.5 shadow-sm transition active:scale-95 bg-black text-white border border-slate-800">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Portal Admin</span>
                    </a>
                @endauth

                <button type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-800 border border-slate-200 transition flex items-center gap-1.5 focus:outline-none shadow-xs shrink-0"
                        aria-label="Toggle Menu Navigasi">
                    <span class="text-xs font-black tracking-wider uppercase" x-text="mobileMenuOpen ? 'TUTUP' : 'MENU'"></span>
                    <svg x-show="!mobileMenuOpen" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- DESKTOP NAVBAR (Luxury Executive Modern Design with Optimal Breathing Room) -->
            <div class="hidden lg:flex items-center gap-1 xl:gap-2 text-xs xl:text-[13px] font-bold shrink-0">
                
                <!-- 1. BERANDA -->
                <a href="{{ route('home') }}" 
                   @click="playMenuVoice($event, 'Beranda', '{{ route('home') }}')"
                   class="group px-3 xl:px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('home') ? 'bg-slate-700 text-white border border-slate-600 font-bold shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 {{ request()->routeIs('home') ? 'text-slate-200' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Beranda</span>
                </a>

                <!-- 2. PROFIL (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" 
                            @click="toggleDropdown('d_profil'); playMenuVoice(null, 'Profil')" 
                            @mouseenter="openDropdown = 'd_profil'"
                            class="group px-3 xl:px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap {{ request()->is('profil/*') || request()->is('sejarah*') || request()->is('visi-misi*') || request()->is('struktur-organisasi*') ? 'bg-slate-700 text-white border border-slate-600 font-bold shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 {{ request()->is('profil/*') || request()->is('sejarah*') || request()->is('visi-misi*') || request()->is('struktur-organisasi*') ? 'text-slate-200' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Profil</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ request()->is('profil/*') || request()->is('sejarah*') || request()->is('visi-misi*') || request()->is('struktur-organisasi*') ? 'text-slate-300' : 'text-slate-400 group-hover:text-slate-700' }}" :class="{ 'rotate-180': openDropdown === 'd_profil' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_profil'" x-cloak x-transition
                         class="absolute left-0 mt-2 w-64 max-h-[270px] dropdown-scrollbar overscroll-contain rounded-2xl shadow-xl border border-slate-100 p-2 z-[70] text-xs font-semibold"
                         style="background-color: #ffffff !important; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;">
                        @if(isset($navProfil) && $navProfil->count() > 0)
                            @foreach($navProfil as $menu)
                                <a href="{{ url($menu->url) }}" 
                                    @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                                    class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold line-clamp-1">
                                    <span class="flex items-center gap-2 truncate">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                        <span class="truncate">{{ $menu->title }}</span>
                                    </span>
                                    <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                                </a>
                            @endforeach
                        @elseif(isset($profilePages) && $profilePages->count() > 0)
                            @foreach($profilePages as $page)
                                <a href="{{ route('profile.page', $page->slug) }}" 
                                    @click="playMenuVoice($event, '{{ addslashes($page->title) }}', '{{ route('profile.page', $page->slug) }}')"
                                    class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold line-clamp-1">
                                    <span class="flex items-center gap-2 truncate">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                        <span class="truncate">{{ $page->title }}</span>
                                    </span>
                                    <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('visi-misi') }}" 
                               @click="playMenuVoice($event, 'Visi Misi', '{{ route('visi-misi') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Visi & Misi Kelurahan</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                            <a href="{{ route('struktur-organisasi') }}" 
                               @click="playMenuVoice($event, 'Struktur Organisasi', '{{ route('struktur-organisasi') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Struktur Organisasi</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                            <a href="{{ route('sejarah') }}" 
                               @click="playMenuVoice($event, 'Sejarah Kelurahan', '{{ route('sejarah') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Sejarah Kelurahan</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 3. LAYANAN (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" 
                            @click="toggleDropdown('d_layanan'); playMenuVoice(null, 'Layanan')" 
                            @mouseenter="openDropdown = 'd_layanan'"
                            class="group px-3 xl:px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('standar-pelayanan') ? 'bg-slate-700 text-white border border-slate-600 font-bold shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 {{ request()->routeIs('standar-pelayanan') ? 'text-slate-200' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Layanan</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ request()->routeIs('standar-pelayanan') ? 'text-slate-300' : 'text-slate-400 group-hover:text-slate-700' }}" :class="{ 'rotate-180': openDropdown === 'd_layanan' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_layanan'" x-cloak x-transition
                         class="absolute left-0 mt-2 w-72 sm:w-80 max-h-[270px] dropdown-scrollbar overscroll-contain rounded-2xl shadow-xl border border-slate-100 p-2 z-[70] text-xs font-semibold"
                         style="background-color: #ffffff !important; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;">
                        @if(isset($navLayanan) && $navLayanan->count() > 0)
                            @foreach($navLayanan as $menu)
                                <a href="{{ url($menu->url) }}" 
                                    @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                                    class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold transition line-clamp-1">
                                    <span class="flex items-center gap-2 truncate">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                        <span class="truncate">{{ $menu->title }}</span>
                                    </span>
                                    <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('standar-pelayanan') }}" 
                               @click="playMenuVoice($event, 'Standar Pelayanan', '{{ route('standar-pelayanan') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold transition">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Standar Pelayanan Publik</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 4. DOKUMEN (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" 
                            @click="toggleDropdown('d_dokumen'); playMenuVoice(null, 'Dokumen')" 
                            @mouseenter="openDropdown = 'd_dokumen'"
                            class="group px-3 xl:px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('dokumen') ? 'bg-slate-700 text-white border border-slate-600 font-bold shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 {{ request()->routeIs('dokumen') ? 'text-slate-200' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                        <span>Dokumen</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ request()->routeIs('dokumen') ? 'text-slate-300' : 'text-slate-400 group-hover:text-slate-700' }}" :class="{ 'rotate-180': openDropdown === 'd_dokumen' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_dokumen'" x-cloak x-transition
                         class="absolute left-0 mt-2 w-72 sm:w-80 max-h-[270px] dropdown-scrollbar overscroll-contain rounded-2xl shadow-xl border border-slate-100 p-2 z-[70] text-xs font-semibold"
                         style="background-color: #ffffff !important; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;">
                        @if(isset($navDokumen) && $navDokumen->count() > 0)
                            @foreach($navDokumen as $menu)
                                <a href="{{ url($menu->url) }}" 
                                    @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                                    class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold transition line-clamp-1 {{ request()->fullUrlIs(url($menu->url)) ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : '' }}">
                                    <span class="flex items-center gap-2 truncate">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                        <span class="truncate">{{ $menu->title }}</span>
                                    </span>
                                    <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('dokumen') }}" 
                               @click="playMenuVoice($event, 'Dokumen', '{{ route('dokumen') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold transition">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Pusat Unduhan Dokumen</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 5. INFORMASI (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" 
                            @click="toggleDropdown('d_informasi'); playMenuVoice(null, 'Informasi')" 
                            @mouseenter="openDropdown = 'd_informasi'"
                            class="group px-3 xl:px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('berita*') || request()->routeIs('pengumuman*') || request()->routeIs('agenda*') || request()->routeIs('transparansi*') || request()->routeIs('galeri*') ? 'bg-slate-700 text-white border border-slate-600 font-bold shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 {{ request()->routeIs('berita*') || request()->routeIs('pengumuman*') || request()->routeIs('agenda*') || request()->routeIs('transparansi*') || request()->routeIs('galeri*') ? 'text-slate-200' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                        <span>Informasi</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ request()->routeIs('berita*') || request()->routeIs('pengumuman*') || request()->routeIs('agenda*') || request()->routeIs('transparansi*') || request()->routeIs('galeri*') ? 'text-slate-300' : 'text-slate-400 group-hover:text-slate-700' }}" :class="{ 'rotate-180': openDropdown === 'd_informasi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_informasi'" x-cloak x-transition
                         class="absolute left-0 mt-2 min-w-[240px] max-h-[270px] dropdown-scrollbar overscroll-contain rounded-2xl shadow-xl border border-slate-100 p-2 z-[70] text-xs font-semibold"
                         style="background-color: #ffffff !important; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;">
                        @if(isset($navInformasi) && $navInformasi->count() > 0)
                            @foreach($navInformasi as $menu)
                                <a href="{{ url($menu->url) }}" 
                                    @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                                    class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition whitespace-nowrap {{ request()->fullUrlIs(url($menu->url)) ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                        <span>{{ $menu->title }}</span>
                                    </span>
                                    <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('berita') }}" 
                               @click="playMenuVoice($event, 'Berita Kelurahan', '{{ route('berita') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition whitespace-nowrap {{ request()->routeIs('berita*') ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Berita & Kabar Kelurahan</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                            <a href="{{ route('pengumuman') }}" 
                               @click="playMenuVoice($event, 'Pengumuman', '{{ route('pengumuman') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition whitespace-nowrap {{ request()->routeIs('pengumuman*') ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Pengumuman Warga</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                            <a href="{{ route('agenda') }}" 
                               @click="playMenuVoice($event, 'Agenda Kelurahan', '{{ route('agenda') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition whitespace-nowrap {{ request()->routeIs('agenda*') ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Agenda Kegiatan</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                            <a href="{{ route('transparansi') }}" 
                               @click="playMenuVoice($event, 'Transparansi Anggaran', '{{ route('transparansi') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition whitespace-nowrap {{ request()->routeIs('transparansi*') ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Transparansi Anggaran</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                            <a href="{{ route('galeri') }}" 
                               @click="playMenuVoice($event, 'Galeri Kegiatan', '{{ route('galeri') }}')"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl transition whitespace-nowrap {{ request()->routeIs('galeri*') ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                    <span>Galeri Dokumentasi</span>
                                </span>
                                <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 6. HUBUNGI (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" 
                            @click="toggleDropdown('d_hubungi'); playMenuVoice(null, 'Hubungi Kami')" 
                            @mouseenter="openDropdown = 'd_hubungi'"
                            class="group px-3 xl:px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('lokasi') || request()->routeIs('layanan-whatsapp') ? 'bg-slate-700 text-white border border-slate-600 font-bold shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100' }}">
                        <svg class="w-3.5 h-3.5 xl:w-4 xl:h-4 {{ request()->routeIs('lokasi') || request()->routeIs('layanan-whatsapp') ? 'text-slate-200' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>Hubungi</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ request()->routeIs('lokasi') || request()->routeIs('layanan-whatsapp') ? 'text-slate-300' : 'text-slate-400 group-hover:text-slate-700' }}" :class="{ 'rotate-180': openDropdown === 'd_hubungi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_hubungi'" x-cloak x-transition
                         class="absolute right-0 mt-2 min-w-[240px] max-h-[270px] dropdown-scrollbar overscroll-contain rounded-2xl shadow-xl border border-slate-100 p-2 z-[70] font-bold text-xs text-slate-800"
                         style="background-color: #ffffff !important; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;">
                        <a href="{{ route('lokasi') }}" 
                            @click="playMenuVoice($event, 'Lokasi Kantor', '{{ route('lokasi') }}')"
                            class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap {{ request()->routeIs('lokasi') ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'text-slate-800 font-bold' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                <span>Lokasi Kantor & Alamat</span>
                            </span>
                            <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                        </a>
                        <a href="{{ route('layanan-whatsapp') }}" 
                            @click="playMenuVoice($event, 'Layanan WhatsApp', '{{ route('layanan-whatsapp') }}')"
                            class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition whitespace-nowrap {{ request()->routeIs('layanan-whatsapp') ? 'bg-slate-100 text-slate-900 font-bold border-l-2 border-slate-700' : 'text-slate-800 font-bold' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-slate-700 transition-colors shrink-0"></span>
                                <span>Layanan WhatsApp CS</span>
                            </span>
                            <span class="opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-slate-700 transition-all font-bold">›</span>
                        </a>
                        <a href="https://wa.me/6282131001001" target="_blank" rel="noopener noreferrer" 
                            @click="playMenuVoice($event, 'WhatsApp')"
                            class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold transition whitespace-nowrap">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Hallo Sae (WhatsApp)</span>
                            </span>
                            <span class="text-slate-400 group-hover:text-slate-700 text-xs">↗</span>
                        </a>
                        <a href="https://www.lapor.go.id/" target="_blank" rel="noopener noreferrer" 
                            @click="playMenuVoice($event, 'Lapor SP4N')"
                            class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 hover:text-slate-900 text-slate-800 font-bold transition whitespace-nowrap">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                <span>Lapor SP4N</span>
                            </span>
                            <span class="text-slate-400 group-hover:text-slate-700 text-xs">↗</span>
                        </a>
                    </div>
                </div>


                <!-- 7. Auth Controls (Sleek Executive Black Button) -->
                <div class="flex items-center gap-2 pl-2 xl:pl-3 ml-1 xl:ml-2 border-l border-slate-200 shrink-0">
                    @auth
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}" 
                           @click="playMenuVoice($event, 'Portal Admin', '{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}')"
                           class="relative group px-3.5 xl:px-4 py-2 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 hover:from-slate-900 hover:to-slate-800 text-white font-extrabold text-xs rounded-xl shadow-md border border-slate-700/80 flex items-center gap-2 shrink-0 active:scale-95 transition-all duration-200 whitespace-nowrap"
                           title="Dashboard Manajemen">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Portal Admin</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-0.5 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="inline">@csrf
                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs p-2 hover:bg-rose-50 rounded-xl transition" title="Keluar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           @click="playMenuVoice($event, 'Portal Admin', '{{ route('login') }}')"
                           class="relative group px-3.5 xl:px-4 py-2 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 hover:from-slate-900 hover:to-slate-800 text-white font-extrabold text-xs rounded-xl shadow-md hover:shadow-lg border border-slate-700/80 flex items-center gap-2 shrink-0 active:scale-95 transition-all duration-200 whitespace-nowrap"
                           title="Masuk ke Panel Pengelola">
                            <span class="p-1 rounded-lg bg-slate-800 text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <span class="tracking-wide">Portal Admin</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-0.5 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endauth
                </div>

            </div>

        </div>
    </div>

    <!-- MOBILE ACCORDION TOGGLE MENU DRAWER -->
    <div x-show="mobileMenuOpen" 
         x-cloak 
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="lg:hidden border-t border-slate-200 bg-white py-3 px-4 space-y-2 shadow-2xl max-h-[85vh] overflow-y-auto">
        
        <!-- Mobile Header inside Drawer -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">NAVIGASI UTAMA</span>
        </div>

        <!-- 1. BERANDA -->
        <a href="{{ route('home') }}" 
           @click="playMenuVoice($event, 'Beranda', '{{ route('home') }}')"
           class="flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('home') ? 'bg-slate-700 text-white font-bold border border-slate-600 shadow-xs' : 'text-slate-800 hover:bg-slate-50' }}">
            <span>BERANDA</span>
            <span class="text-xs {{ request()->routeIs('home') ? 'text-slate-300' : 'text-slate-400' }}">›</span>
        </a>

        <!-- 2. PROFIL Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" 
                    @click="toggleDropdown('m_profil'); playMenuVoice(null, 'Profil')" 
                    class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->is('profil/*') || request()->is('sejarah*') || request()->is('visi-misi*') || request()->is('struktur-organisasi*') ? 'bg-slate-700 text-white font-bold border border-slate-600 shadow-xs' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>PROFIL</span>
                <svg class="w-4 h-4 {{ request()->is('profil/*') || request()->is('sejarah*') || request()->is('visi-misi*') || request()->is('struktur-organisasi*') ? 'text-slate-300' : 'text-slate-400' }} transition transform" :class="{ 'rotate-180 text-white': openDropdown === 'm_profil' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_profil'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                @if(isset($navProfil) && $navProfil->count() > 0)
                    @foreach($navProfil as $menu)
                        <a href="{{ url($menu->url) }}" 
                           @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                           class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium line-clamp-1">
                            {{ $menu->title }}
                        </a>
                    @endforeach
                @elseif(isset($profilePages) && $profilePages->count() > 0)
                    @foreach($profilePages as $page)
                        <a href="{{ route('profile.page', $page->slug) }}" 
                           @click="playMenuVoice($event, '{{ addslashes($page->title) }}', '{{ route('profile.page', $page->slug) }}')"
                           class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium line-clamp-1">
                            {{ $page->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('visi-misi') }}" 
                       @click="playMenuVoice($event, 'Visi Misi', '{{ route('visi-misi') }}')"
                       class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium">Visi & Misi Kelurahan</a>
                    <a href="{{ route('struktur-organisasi') }}" 
                       @click="playMenuVoice($event, 'Struktur Organisasi', '{{ route('struktur-organisasi') }}')"
                       class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium">Struktur Organisasi</a>
                    <a href="{{ route('sejarah') }}" 
                       @click="playMenuVoice($event, 'Sejarah Kelurahan', '{{ route('sejarah') }}')"
                       class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium">Sejarah Kelurahan</a>
                @endif
            </div>
        </div>

        <!-- 3. LAYANAN Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" 
                    @click="toggleDropdown('m_layanan'); playMenuVoice(null, 'Layanan')" 
                    class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('standar-pelayanan') ? 'bg-slate-700 text-white font-bold border border-slate-600 shadow-xs' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>LAYANAN</span>
                <svg class="w-4 h-4 {{ request()->routeIs('standar-pelayanan') ? 'text-slate-300' : 'text-slate-400' }} transition transform" :class="{ 'rotate-180 text-white': openDropdown === 'm_layanan' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_layanan'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                @if(isset($navLayanan) && $navLayanan->count() > 0)
                    @foreach($navLayanan as $menu)
                        <a href="{{ url($menu->url) }}" 
                           @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                           class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium line-clamp-1">
                            {{ $menu->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('standar-pelayanan') }}" 
                       @click="playMenuVoice($event, 'Standar Pelayanan', '{{ route('standar-pelayanan') }}')"
                       class="block p-2.5 rounded-lg text-slate-800 font-bold hover:text-slate-900 hover:bg-slate-100">Standar Pelayanan Publik Kelurahan</a>
                @endif
            </div>
        </div>

        <!-- 4. DOKUMEN Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" 
                    @click="toggleDropdown('m_dokumen'); playMenuVoice(null, 'Dokumen')" 
                    class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('dokumen') ? 'bg-slate-700 text-white font-bold border border-slate-600 shadow-xs' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>DOKUMEN</span>
                <svg class="w-4 h-4 {{ request()->routeIs('dokumen') ? 'text-slate-300' : 'text-slate-400' }} transition transform" :class="{ 'rotate-180 text-white': openDropdown === 'm_dokumen' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_dokumen'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                @if(isset($navDokumen) && $navDokumen->count() > 0)
                    @foreach($navDokumen as $menu)
                        <a href="{{ url($menu->url) }}" 
                           @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                           class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium line-clamp-1 {{ request()->fullUrlIs(url($menu->url)) ? 'bg-slate-100 text-slate-900 font-bold' : '' }}">
                            {{ $menu->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('dokumen') }}" 
                       @click="playMenuVoice($event, 'Dokumen', '{{ route('dokumen') }}')"
                       class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium">Pusat Unduhan Dokumen</a>
                @endif
            </div>
        </div>

        <!-- 5. INFORMASI Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" 
                    @click="toggleDropdown('m_informasi'); playMenuVoice(null, 'Informasi')" 
                    class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('berita*') || request()->routeIs('pengumuman*') || request()->routeIs('agenda*') || request()->routeIs('transparansi*') || request()->routeIs('galeri*') ? 'bg-slate-700 text-white font-bold border border-slate-600 shadow-xs' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>INFORMASI</span>
                <svg class="w-4 h-4 {{ request()->routeIs('berita*') || request()->routeIs('pengumuman*') || request()->routeIs('agenda*') || request()->routeIs('transparansi*') || request()->routeIs('galeri*') ? 'text-slate-300' : 'text-slate-400' }} transition transform" :class="{ 'rotate-180 text-white': openDropdown === 'm_informasi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_informasi'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                @if(isset($navInformasi) && $navInformasi->count() > 0)
                    @foreach($navInformasi as $menu)
                        <a href="{{ url($menu->url) }}" 
                           @click="playMenuVoice($event, '{{ addslashes($menu->title) }}', '{{ url($menu->url) }}')"
                           class="block p-2.5 rounded-lg {{ request()->fullUrlIs(url($menu->url)) ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium' }}">
                            {{ $menu->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('berita') }}" 
                       @click="playMenuVoice($event, 'Berita Kelurahan', '{{ route('berita') }}')"
                       class="block p-2.5 rounded-lg {{ request()->routeIs('berita*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium' }}">Berita & Kabar Kelurahan</a>
                    <a href="{{ route('pengumuman') }}" 
                       @click="playMenuVoice($event, 'Pengumuman', '{{ route('pengumuman') }}')"
                       class="block p-2.5 rounded-lg {{ request()->routeIs('pengumuman*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium' }}">Pengumuman Warga</a>
                    <a href="{{ route('agenda') }}" 
                       @click="playMenuVoice($event, 'Agenda Kelurahan', '{{ route('agenda') }}')"
                       class="block p-2.5 rounded-lg {{ request()->routeIs('agenda*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium' }}">Agenda Kegiatan</a>
                    <a href="{{ route('transparansi') }}" 
                       @click="playMenuVoice($event, 'Transparansi Anggaran', '{{ route('transparansi') }}')"
                       class="block p-2.5 rounded-lg {{ request()->routeIs('transparansi*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium' }}">Transparansi Anggaran</a>
                    <a href="{{ route('galeri') }}" 
                       @click="playMenuVoice($event, 'Galeri Kegiatan', '{{ route('galeri') }}')"
                       class="block p-2.5 rounded-lg {{ request()->routeIs('galeri*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium' }}">Galeri Dokumentasi</a>
                @endif
            </div>
        </div>

        <!-- 6. HUBUNGI Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" 
                    @click="toggleDropdown('m_hubungi'); playMenuVoice(null, 'Hubungi Kami')" 
                    class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('lokasi') || request()->routeIs('layanan-whatsapp') ? 'bg-slate-700 text-white font-bold border border-slate-600 shadow-xs' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>HUBUNGI</span>
                <svg class="w-4 h-4 {{ request()->routeIs('lokasi') || request()->routeIs('layanan-whatsapp') ? 'text-slate-300' : 'text-slate-400' }} transition transform" :class="{ 'rotate-180 text-white': openDropdown === 'm_hubungi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_hubungi'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                <a href="{{ route('lokasi') }}" 
                   @click="playMenuVoice($event, 'Lokasi Kantor', '{{ route('lokasi') }}')"
                   class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium {{ request()->routeIs('lokasi') ? 'bg-slate-100 text-slate-900 font-bold' : '' }}">Lokasi Kantor & Alamat Kelurahan</a>
                <a href="{{ route('layanan-whatsapp') }}" 
                   @click="playMenuVoice($event, 'Layanan WhatsApp', '{{ route('layanan-whatsapp') }}')"
                   class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium {{ request()->routeIs('layanan-whatsapp') ? 'bg-slate-100 text-slate-900 font-bold' : '' }}">Layanan WhatsApp CS</a>
                <a href="https://wa.me/6282131001001" target="_blank" rel="noopener noreferrer" 
                   @click="playMenuVoice($event, 'WhatsApp')"
                   class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium">Hallo Sae (WhatsApp)</a>
                <a href="https://www.lapor.go.id/" target="_blank" rel="noopener noreferrer" 
                   @click="playMenuVoice($event, 'Lapor SP4N')"
                   class="block p-2.5 rounded-lg text-slate-600 hover:text-slate-700 hover:bg-slate-50 font-medium">Lapor SP4N</a>
            </div>
        </div>

        <!-- Mobile Auth Action Button -->
        <div class="pt-3 border-t border-slate-100">
            @auth
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}" 
                   @click="playMenuVoice($event, 'Portal Admin', '{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}')"
                   class="w-full py-3 bg-black hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl text-center shadow-md transition flex items-center justify-center gap-2 active:scale-98 border border-slate-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Masuk Portal Admin</span>
                    <span>→</span>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   @click="playMenuVoice($event, 'Portal Admin', '{{ route('login') }}')"
                   class="w-full py-3 bg-black hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl text-center shadow-md transition flex items-center justify-center gap-2 group active:scale-98 border border-slate-800">
                    <svg class="w-4 h-4 text-blue-400 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Masuk Portal Admin</span>
                    <span class="text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                </a>
            @endauth
        </div>

    </div>
</nav>

<!-- AUTHENTIC GOOGLE INDONESIAN FEMALE VOICE AUDIO ENGINE -->
<script>
(function() {
    let idFemaleVoice = null;
    const audioCache = {};
    let currentAudio = null;

    // Preload system voices as fallback
    function loadFallbackVoices() {
        if (!('speechSynthesis' in window)) return;
        const voices = window.speechSynthesis.getVoices();
        if (!voices || voices.length === 0) return;
        idFemaleVoice = voices.find(v => v.lang.startsWith('id') && /google/i.test(v.name))
                     || voices.find(v => v.lang.startsWith('id') && /gadis|damayanti|siti|female|wanita/i.test(v.name))
                     || voices.find(v => v.lang.startsWith('id') || v.lang.includes('ID'))
                     || voices.find(v => /female|gadis|natural|zira/i.test(v.name));
    }

    if ('speechSynthesis' in window) {
        loadFallbackVoices();
        window.speechSynthesis.onvoiceschanged = loadFallbackVoices;
    }

    // Comprehensive list of menu items to preload immediately in browser cache for zero-latency, stutter-free playback
    const preloads = [
        'Beranda', 'Profil', 'Visi Misi', 'Struktur Organisasi', 'Sejarah Kelurahan',
        'Layanan', 'Standar Pelayanan', 'Dokumen', 'Informasi', 'Berita Kelurahan',
        'Pengumuman', 'Agenda Kelurahan', 'Transparansi Anggaran', 'Galeri Kegiatan',
        'Hubungi Kami', 'Lokasi Kantor', 'Layanan WhatsApp', 'WhatsApp', 'Lapor SP4N',
        'Portal Admin', 'Kelurahan Semampir'
    ];

    function preloadAudios() {
        preloads.forEach(function(txt) {
            try {
                const aud = new Audio('/tts-google?text=' + encodeURIComponent(txt));
                aud.preload = 'auto';
                aud.load();
                audioCache[txt] = aud;
            } catch(e) {}
        });
    }

    if (document.readyState === 'loading') {
        window.addEventListener('DOMContentLoaded', preloadAudios);
    } else {
        preloadAudios();
    }

    // Fallback using SpeechSynthesis if MP3 playback is truly unavailable
    function speakFallback(text, onDone) {
        if (!('speechSynthesis' in window)) {
            if (onDone) onDone();
            return;
        }
        try {
            window.speechSynthesis.cancel();
            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'id-ID';
            utter.pitch = 1.15;
            utter.rate = 1.05;
            if (idFemaleVoice) utter.voice = idFemaleVoice;
            let called = false;
            const finish = () => {
                if (!called) {
                    called = true;
                    if (onDone) onDone();
                }
            };
            utter.onend = finish;
            utter.onerror = finish;
            setTimeout(finish, 1200);
            window.speechSynthesis.speak(utter);
        } catch(e) {
            if (onDone) onDone();
        }
    }

    // The Official Google Indonesian Female Voice Audio Player (Pure, Crisp, No Cutoff)
    window.playRealGoogleVoice = function(text, onDone) {
        if (!text) {
            if (onDone) onDone();
            return;
        }

        const cleanText = text.trim();
        const ttsUrl = '/tts-google?text=' + encodeURIComponent(cleanText);

        try {
            // Gracefully stop previous playback without triggering abort cascades
            if (currentAudio) {
                try {
                    currentAudio.onended = null;
                    currentAudio.onerror = null;
                    currentAudio.pause();
                } catch(e) {}
            }

            const audio = new Audio(ttsUrl);
            audio.preload = 'auto';
            currentAudio = audio;

            let finished = false;
            const finish = () => {
                if (!finished) {
                    finished = true;
                    if (currentAudio === audio) currentAudio = null;
                    if (onDone) onDone();
                }
            };

            audio.onended = finish;
            audio.onerror = function() {
                speakFallback(cleanText, finish);
            };

            const playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.catch(function(err) {
                    if (err && err.name === 'AbortError') {
                        // User clicked another item or paused, normal browser behavior - DO NOT fallback to robot voice!
                        return;
                    }
                    speakFallback(cleanText, finish);
                });
            }

            // Generous fallback safety timeout so speech is never cut off midway
            setTimeout(function() {
                if (!finished) {
                    finish();
                }
            }, 2500);

        } catch (e) {
            speakFallback(cleanText, onDone);
        }
    };

    // Alias for compatibility
    window.speakGoogleCewek = window.playRealGoogleVoice;

    // Menu Voice Handler: Speaks naturally and transitions smoothly without stutter or premature cutoff
    window.playMenuVoice = function(event, speechText, targetUrl) {
        if (targetUrl) {
            const isBlank = event && event.currentTarget && event.currentTarget.getAttribute('target') === '_blank';
            if (!isBlank && (!event || (!event.ctrlKey && !event.metaKey && event.button === 0))) {
                // If already on the same page, just play the voice without reload
                try {
                    const currentClean = window.location.href.split('#')[0].replace(/\/+$/, '');
                    const targetClean = new URL(targetUrl, window.location.origin).href.split('#')[0].replace(/\/+$/, '');
                    if (currentClean === targetClean) {
                        window.playRealGoogleVoice(speechText);
                        return;
                    }
                } catch(e) {}

                if (event && event.preventDefault) event.preventDefault();
                
                let navigated = false;
                const go = function() {
                    if (!navigated) {
                        navigated = true;
                        window.location.href = targetUrl;
                    }
                };

                // Play the Google female voice cleanly, and navigate when speech finishes naturally!
                window.playRealGoogleVoice(speechText, function() {
                    go();
                });

                // Safety timeout so navigation never gets stuck (generous enough for natural speech)
                setTimeout(go, 1500);
                return;
            }
        }
        window.playRealGoogleVoice(speechText);
    };
})();
</script>

<!-- ========================================== -->
<!-- 3. RUNNING NEWS TICKER / ANNOUNCEMENT BAR   -->
<!-- ========================================== -->
@php
    $annCount = isset($globalAnnouncements) ? $globalAnnouncements->count() : 0;
@endphp
<div class="bg-gradient-to-r from-slate-950 via-[#0b1528] to-slate-950 text-slate-100 border-b border-slate-800/80 py-2.5 text-xs relative z-30 select-none overflow-hidden shadow-inner"
     x-data="{
         isPaused: false,
         modalOpen: false,
         modalData: { title: '', content: '', badge: '', date: '', link: '', is_urgent: false },
         openModal(title, content, badge, date, link, isUrgent) {
             this.modalData = { title, content, badge, date, link, is_urgent: isUrgent };
             this.modalOpen = true;
         }
     }">
    
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3 sm:gap-4">
        
        <!-- Left: Badge & Running Ticker Content -->
        <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0 flex-1 overflow-hidden">
            
            <!-- Category Badge (Modern Amber / Orange Glowing Pill) -->
            <a href="{{ route('pengumuman') }}" 
               class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0 flex items-center gap-1.5 shadow-md shadow-amber-500/20 transition group z-10"
               title="Lihat semua pengumuman resmi">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-slate-950 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-950"></span>
                </span>
                <span>PENGUMUMAN</span>
                <span class="text-slate-950 font-black group-hover:translate-x-0.5 transition-transform">›</span>
            </a>

            <!-- Running Text Marquee (Seamless Scroll) -->
            <div class="relative min-w-0 flex-1 overflow-hidden"
                 @mouseenter="isPaused = true" 
                 @mouseleave="isPaused = false">
                <div class="animate-marquee flex items-center gap-8 w-max py-0.5"
                     :style="isPaused ? 'animation-play-state: paused;' : ''">
                    @if($annCount > 0)
                        {{-- Track 1 (Original Track) --}}
                        <div class="flex items-center gap-8 shrink-0">
                            @foreach($globalAnnouncements as $ann)
                                <button type="button" 
                                        @click='openModal({{ json_encode($ann->title) }}, {{ json_encode($ann->content ?? "") }}, {{ json_encode($ann->badge_type ?? "INFO") }}, {{ json_encode($ann->created_at->format("d M Y")) }}, {{ json_encode($ann->link_url ?? "") }}, {{ $ann->is_urgent ? "true" : "false" }})'
                                        class="inline-flex items-center gap-2.5 hover:opacity-90 transition text-left cursor-pointer group shrink-0"
                                        title="{{ $ann->title }} (Klik untuk baca detail)">
                                    <span class="px-2 py-0.5 {{ $ann->is_urgent ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-blue-500/20 text-blue-300 border-blue-500/40' }} text-[9px] font-black rounded-md border uppercase flex items-center gap-1">
                                        @if($ann->is_urgent)
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping"></span>
                                        @endif
                                        <span>{{ $ann->badge_type ?? 'INFORMASI' }}</span>
                                    </span>
                                    <span class="font-semibold text-slate-100 group-hover:text-amber-300 group-hover:underline transition text-xs">{{ $ann->title }}</span>
                                    <span class="text-slate-400 text-[10px] font-mono">({{ $ann->created_at->format('d M Y') }})</span>
                                </button>
                                <span class="text-amber-400/50 font-bold text-xs select-none">✦</span>
                            @endforeach
                        </div>
                        {{-- Track 2 (Seamless Duplicate Track) --}}
                        <div class="flex items-center gap-8 shrink-0" aria-hidden="true">
                            @foreach($globalAnnouncements as $ann)
                                <button type="button" 
                                        @click='openModal({{ json_encode($ann->title) }}, {{ json_encode($ann->content ?? "") }}, {{ json_encode($ann->badge_type ?? "INFO") }}, {{ json_encode($ann->created_at->format("d M Y")) }}, {{ json_encode($ann->link_url ?? "") }}, {{ $ann->is_urgent ? "true" : "false" }})'
                                        class="inline-flex items-center gap-2.5 hover:opacity-90 transition text-left cursor-pointer group shrink-0"
                                        title="{{ $ann->title }} (Klik untuk baca detail)">
                                    <span class="px-2 py-0.5 {{ $ann->is_urgent ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-blue-500/20 text-blue-300 border-blue-500/40' }} text-[9px] font-black rounded-md border uppercase flex items-center gap-1">
                                        @if($ann->is_urgent)
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping"></span>
                                        @endif
                                        <span>{{ $ann->badge_type ?? 'INFORMASI' }}</span>
                                    </span>
                                    <span class="font-semibold text-slate-100 group-hover:text-amber-300 group-hover:underline transition text-xs">{{ $ann->title }}</span>
                                    <span class="text-slate-400 text-[10px] font-mono">({{ $ann->created_at->format('d M Y') }})</span>
                                </button>
                                <span class="text-amber-400/50 font-bold text-xs select-none">✦</span>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 border border-blue-500/40 text-[9px] font-black rounded-md shrink-0 uppercase">INFO RESMI</span>
                            <span class="font-semibold text-slate-200 text-xs">Selamat Datang di Portal Resmi Pemerintah Kelurahan Semampir, Kecamatan Kraksaan, Kabupaten Probolinggo</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right: Play/Pause Button & Link to all announcements -->
        @if($annCount > 0)
            <div class="flex items-center gap-2 shrink-0 text-[11px] z-10">
                <!-- Play / Pause Button -->
                <button type="button" 
                        @click="isPaused = !isPaused"
                        class="px-2.5 py-1 rounded-lg border border-slate-700 bg-slate-800/90 hover:bg-slate-700 text-slate-300 font-bold text-[10px] transition flex items-center gap-1.5 shadow-xs cursor-pointer"
                        :title="isPaused ? 'Jalankan Teks Berjalan' : 'Hentikan / Jeda Teks Berjalan'">
                    <span x-show="!isPaused" class="text-[9px]">⏸</span>
                    <span x-show="isPaused" x-cloak class="text-[9px]">▶</span>
                    <span x-text="isPaused ? 'Jalan' : 'Jeda'" class="hidden xs:inline"></span>
                </button>

                <!-- Link to all announcements -->
                <a href="{{ route('pengumuman') }}" 
                   class="px-2 py-0.5 text-amber-400 hover:text-amber-300 font-extrabold hover:underline text-[10px] hidden sm:inline-flex items-center gap-1 group">
                    <span>Lihat Semua</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                </a>
            </div>
        @endif

    </div>

    <!-- DETAIL MODAL FOR ANNOUNCEMENT -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         @keydown.escape.window="modalOpen = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-5 sm:p-6 border border-slate-100 overflow-hidden transform transition-all text-slate-900"
             @click.away="modalOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider"
                          :class="modalData.is_urgent ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'"
                          x-text="modalData.badge"></span>
                    <span class="text-xs text-slate-400 font-mono" x-text="modalData.date"></span>
                </div>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg leading-none p-1 rounded-lg hover:bg-slate-100 cursor-pointer">✕</button>
            </div>

            <div class="py-4">
                <h3 class="text-base sm:text-lg font-black text-slate-900 leading-snug mb-3" x-text="modalData.title"></h3>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line" x-text="modalData.content || 'Tidak ada keterangan tambahan untuk pengumuman ini.'"></p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                <template x-if="modalData.link">
                    <a :href="modalData.link" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                        <span>Buka Tautan</span>
                        <span>↗</span>
                    </a>
                </template>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('pengumuman') }}" class="px-3 py-2 text-slate-600 hover:text-slate-800 text-xs font-bold hover:underline">
                        Semua Pengumuman
                    </a>
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
