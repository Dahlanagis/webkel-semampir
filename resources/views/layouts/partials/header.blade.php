<!-- ========================================== -->
<!-- 1. TOP BAR HEADER                          -->
<!-- ========================================== -->
<header class="bg-slate-950 text-slate-200 text-[10px] sm:text-[11px] py-1.5 border-b border-slate-900 relative z-50 overflow-x-hidden">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-1.5 sm:gap-2">
        
        <!-- Left Info: Office Hours -->
        <div class="flex items-center gap-1.5 text-slate-300 font-medium justify-center sm:justify-start text-center sm:text-left">
            <span class="text-slate-400">🕒 Jam Layanan Kantor:</span>
            <strong class="text-white">{{ $villageProfile['office_hours_mon_thu'] ?? '08.00 - 15.30 WIB' }}</strong>
        </div>

        <!-- Right Info: Accessibility Controls -->
        <div class="flex items-center gap-2 text-slate-300 font-medium text-[10px] shrink-0">
            <span class="text-slate-400 font-semibold hidden xs:inline">Aksesibilitas:</span>
            
            <div class="flex items-center gap-1 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">
                <button type="button" @click="toggleTextSize()" class="px-1.5 py-0.5 rounded hover:bg-slate-800 text-white font-black" title="Perbesar Ukuran Teks">
                    A+
                </button>
                <button type="button" @click="resetTextSize()" class="px-1.5 py-0.5 rounded hover:bg-slate-800 text-slate-300 font-bold" title="Ukuran Teks Normal">
                    A
                </button>
                <button type="button" @click="toggleContrast()" class="px-1.5 py-0.5 rounded hover:bg-slate-800 text-amber-300 font-bold flex items-center gap-1" title="Mode Kontras Tinggi">
                    <span>🌓</span> Kontras
                </button>
            </div>
        </div>

    </div>
</header>

<!-- ========================================== -->
<!-- 2. MAIN NAVBAR WITH HORIZONTAL DROPDOWNS   -->
<!-- ========================================== -->
<nav x-data="{ 
        mobileMenuOpen: false,
        openDropdown: null,
        toggleDropdown(menu) {
            this.openDropdown = this.openDropdown === menu ? null : menu;
        }
    }" 
    @click.away="openDropdown = null; mobileMenuOpen = false"
    class="bg-white/95 backdrop-blur-md text-slate-800 border-b border-slate-200 shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between min-h-[4rem] sm:min-h-[4.5rem] py-2 gap-2 sm:gap-4">
            
            <!-- Brand / Logo Header -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group min-w-0 pr-1">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-1 shadow-sm group-hover:scale-105 transition duration-300 shrink-0">
                    <img src="{{ !empty($systemSettings['app_logo']) ? asset('storage/' . $systemSettings['app_logo']) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlwlIShkVajC2C_tEglw59FLYjmw5n-E1vAgqplpW75A&s=10' }}" 
                         alt="Logo Aplikasi" 
                         class="w-full h-full object-contain">
                </div>
                <div class="min-w-0 flex flex-col justify-center">
                    <div class="font-extrabold text-sm sm:text-base tracking-tight text-slate-900 leading-none group-hover:text-emerald-700 transition truncate uppercase">
                        {{ strtoupper($systemSettings['app_name'] ?? 'SIMPEL KELURAHAN') }}
                    </div>
                    <div class="text-[9px] sm:text-[10px] text-emerald-600/90 font-medium tracking-wide uppercase mt-1 truncate">
                        {{ strtoupper($systemSettings['app_subtitle'] ?? 'KECAMATAN KRAKSAAN') }}
                    </div>
                </div>
            </a>

            <!-- Mobile Toggle Hamburger Button (Visible on screens smaller than lg) -->
            <div class="flex items-center gap-2 lg:hidden shrink-0">
                <button type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-800 hover:text-emerald-700 border border-slate-200 transition flex items-center gap-1.5 focus:outline-none shadow-sm shrink-0"
                        aria-label="Toggle Menu Navigasi">
                    <span class="text-xs font-black tracking-wider uppercase" x-text="mobileMenuOpen ? 'TUTUP' : 'MENU'"></span>
                    <svg x-show="!mobileMenuOpen" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- DESKTOP NAVBAR (Hidden on mobile lg:hidden, Visible on desktop lg:flex) -->
            <div class="hidden lg:flex items-center gap-2 font-bold text-xs">
                
                <!-- 1. HOME -->
                <a href="{{ route('home') }}" 
                   class="px-3 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'text-emerald-700 font-black bg-emerald-50' : 'text-slate-700 hover:text-emerald-700 hover:bg-slate-50' }}">
                    HOME
                </a>

                <!-- 2. PROFIL (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" @click="toggleDropdown('d_profil')" @mouseenter="openDropdown = 'd_profil'"
                            class="px-3 py-2 rounded-lg transition flex items-center gap-1 uppercase {{ request()->is('profil/*') || request()->is('sejarah*') || request()->is('visi-misi*') || request()->is('struktur-organisasi*') ? 'text-emerald-700 font-black bg-emerald-50' : 'text-slate-700 hover:text-emerald-700 hover:bg-slate-50' }}">
                        <span>PROFIL</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition" :class="{ 'rotate-180': openDropdown === 'd_profil' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_profil'" x-cloak x-transition
                         class="absolute left-0 mt-1 w-56 max-h-[360px] overflow-y-auto bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 font-medium text-xs">
                        @if(isset($profilePages) && $profilePages->count() > 0)
                            @foreach($profilePages as $page)
                                <a href="{{ route('profile.page', $page->slug) }}" class="block px-4 py-2.5 transition hover:bg-emerald-50 hover:text-emerald-800 line-clamp-1">
                                    {{ $page->title }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('sejarah') }}" class="block px-4 py-2.5 transition hover:bg-emerald-50 hover:text-emerald-800">Sejarah Kelurahan</a>
                        @endif
                    </div>
                </div>

                <!-- 3. LAYANAN (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" @click="toggleDropdown('d_layanan')" @mouseenter="openDropdown = 'd_layanan'"
                            class="px-3 py-2 rounded-lg transition flex items-center gap-1 uppercase {{ request()->routeIs('standar-pelayanan') ? 'text-emerald-700 font-black bg-emerald-50' : 'text-slate-700 hover:text-emerald-700 hover:bg-slate-50' }}">
                        <span>LAYANAN</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition" :class="{ 'rotate-180': openDropdown === 'd_layanan' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_layanan'" x-cloak x-transition
                         class="absolute left-0 mt-1 w-72 sm:w-80 max-h-[360px] overflow-y-auto bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 font-medium text-xs">
                        @if(isset($navLayanan) && $navLayanan->count() > 0)
                            @foreach($navLayanan as $menu)
                                <a href="{{ url($menu->url) }}" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition line-clamp-1">
                                    {{ $menu->title }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('standar-pelayanan') }}" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition">
                                Standar Pelayanan Publik
                            </a>
                        @endif

                    </div>
                </div>

                <!-- 4. DOKUMEN (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" @click="toggleDropdown('d_dokumen')" @mouseenter="openDropdown = 'd_dokumen'"
                            class="px-3 py-2 rounded-lg transition flex items-center gap-1 uppercase {{ request()->routeIs('dokumen') ? 'text-emerald-700 font-black bg-emerald-50' : 'text-slate-700 hover:text-emerald-700 hover:bg-slate-50' }}">
                        <span>DOKUMEN</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition" :class="{ 'rotate-180': openDropdown === 'd_dokumen' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_dokumen'" x-cloak x-transition
                         class="absolute left-0 mt-1 w-72 sm:w-80 max-h-[360px] overflow-y-auto bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 font-medium text-xs">
                        @if(isset($navDokumen) && $navDokumen->count() > 0)
                            @foreach($navDokumen as $menu)
                                <a href="{{ url($menu->url) }}" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition line-clamp-1">
                                    {{ $menu->title }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('dokumen') }}" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition">
                                Pusat Unduhan Dokumen
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 5. INFORMASI (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" @click="toggleDropdown('d_informasi')" @mouseenter="openDropdown = 'd_informasi'"
                            class="px-3 py-2 rounded-lg transition flex items-center gap-1 uppercase {{ request()->routeIs('berita') || request()->routeIs('berita.detail') || request()->routeIs('galeri') ? 'text-emerald-700 font-black bg-emerald-50' : 'text-slate-700 hover:text-emerald-700 hover:bg-slate-50' }}">
                        <span>INFORMASI</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition" :class="{ 'rotate-180': openDropdown === 'd_informasi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_informasi'" x-cloak x-transition
                         class="absolute left-0 mt-1 min-w-max bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 font-medium text-xs">
                        <a href="{{ route('berita') }}" class="block px-4 py-2.5 transition whitespace-nowrap {{ request()->routeIs('berita') || request()->routeIs('berita.detail') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'hover:bg-emerald-50 hover:text-emerald-800' }}">Berita & Kabar Desa</a>
                        <a href="{{ route('pengumuman') }}" class="block px-4 py-2.5 transition whitespace-nowrap {{ request()->routeIs('pengumuman') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'hover:bg-emerald-50 hover:text-emerald-800' }}">Pengumuman Warga</a>
                        <a href="{{ route('galeri') }}" class="block px-4 py-2.5 transition whitespace-nowrap {{ request()->routeIs('galeri') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'hover:bg-emerald-50 hover:text-emerald-800' }}">Galeri Dokumentasi</a>
                    </div>
                </div>

                <!-- 6. HUBUNGI (Dropdown) -->
                <div class="relative" @mouseleave="openDropdown = null">
                    <button type="button" @click="toggleDropdown('d_hubungi')" @mouseenter="openDropdown = 'd_hubungi'"
                            class="px-3 py-2 rounded-lg transition flex items-center gap-1 uppercase {{ request()->routeIs('lokasi') || request()->routeIs('layanan-whatsapp') ? 'text-emerald-700 font-black bg-emerald-50' : 'text-slate-700 hover:text-emerald-700 hover:bg-slate-50' }}">
                        <span>HUBUNGI</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition" :class="{ 'rotate-180': openDropdown === 'd_hubungi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDropdown === 'd_hubungi'" x-cloak x-transition
                         class="absolute right-0 mt-1 min-w-max bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 font-bold text-xs text-slate-800">
                        <a href="{{ route('lokasi') }}" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition whitespace-nowrap {{ request()->routeIs('lokasi') ? 'bg-emerald-50 text-emerald-800' : '' }}">Lokasi Kantor & Alamat Kelurahan</a>
                        <a href="{{ route('layanan-whatsapp') }}" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition whitespace-nowrap {{ request()->routeIs('layanan-whatsapp') ? 'bg-emerald-50 text-emerald-800' : '' }}">Layanan WhatsApp CS</a>
                        <a href="https://wa.me/6282131001001" target="_blank" rel="noopener noreferrer" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition whitespace-nowrap">Hallo Sae (WhatsApp)</a>
                        <a href="https://www.lapor.go.id/" target="_blank" rel="noopener noreferrer" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-800 transition whitespace-nowrap">LaporSP4N</a>
                    </div>
                </div>

                <!-- 7. BerAKHLAK Official Logo -->
                <div class="px-1 shrink-0">
                    <img src="https://diskominfo.probolinggokab.go.id/frontend/images/img-berakhlak.png" 
                         alt="Logo BerAKHLAK Bangga Melayani Bangsa" 
                         class="h-7 sm:h-8 w-auto object-contain">
                </div>

                <!-- 8. Auth Controls -->
                <div class="flex items-center gap-2 pl-2">
                    @auth
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}" 
                           class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow transition flex items-center gap-1.5 shrink-0">
                            <span>🌐</span>
                            <span>Panel Admin</span>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="inline">@csrf
                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs px-2 py-1.5 flex items-center gap-1">
                                <span>Keluar</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow transition flex items-center gap-1.5 shrink-0">
                            <span>Login Admin</span>
                        </a>
                    @endauth
                </div>

            </div>

        </div>
    </div>

    <!-- MOBILE ACCORDION TOGGLE MENU DRAWER (Only visible when mobileMenuOpen is true on small screens) -->
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
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">NAVIGASI {{ strtoupper($villageProfile['village_name'] ?? 'PATOKAN') }}</span>
            <img src="https://diskominfo.probolinggokab.go.id/frontend/images/img-berakhlak.png" 
                 alt="Logo BerAKHLAK" 
                 class="h-6 w-auto object-contain">
        </div>

        <!-- 1. HOME -->
        <a href="{{ route('home') }}" class="flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('home') ? 'bg-emerald-50 text-emerald-800 font-black border border-emerald-200' : 'text-slate-800 hover:bg-slate-50' }}">
            <span>HOME</span>
        </a>

        <!-- 2. PROFIL Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" @click="toggleDropdown('m_profil')" class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->is('profil/*') || request()->is('sejarah*') || request()->is('visi-misi*') || request()->is('struktur-organisasi*') ? 'bg-emerald-50 text-emerald-800 font-black border border-emerald-200' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>PROFIL</span>
                <svg class="w-4 h-4 text-slate-400 transition transform" :class="{ 'rotate-180 text-emerald-600': openDropdown === 'm_profil' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_profil'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                @if(isset($profilePages) && $profilePages->count() > 0)
                    @foreach($profilePages as $page)
                        <a href="{{ route('profile.page', $page->slug) }}" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium line-clamp-1">
                            {{ $page->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('sejarah') }}" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium">Sejarah Kelurahan</a>
                @endif
            </div>
        </div>

        <!-- 3. LAYANAN Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" @click="toggleDropdown('m_layanan')" class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('standar-pelayanan') ? 'bg-emerald-50 text-emerald-800 font-black border border-emerald-200' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>LAYANAN</span>
                <svg class="w-4 h-4 text-slate-400 transition transform" :class="{ 'rotate-180 text-emerald-600': openDropdown === 'm_layanan' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_layanan'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                @if(isset($navLayanan) && $navLayanan->count() > 0)
                    @foreach($navLayanan as $menu)
                        <a href="{{ url($menu->url) }}" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium line-clamp-1">
                            {{ $menu->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('standar-pelayanan') }}" class="block p-2.5 rounded-lg text-slate-800 font-bold hover:text-emerald-700 hover:bg-emerald-50">Standar Pelayanan Publik Kelurahan</a>
                @endif
            </div>
        </div>

        <!-- 4. DOKUMEN Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" @click="toggleDropdown('m_dokumen')" class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('dokumen') ? 'bg-emerald-50 text-emerald-800 font-black border border-emerald-200' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>DOKUMEN</span>
                <svg class="w-4 h-4 text-slate-400 transition transform" :class="{ 'rotate-180 text-emerald-600': openDropdown === 'm_dokumen' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_dokumen'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                @if(isset($navDokumen) && $navDokumen->count() > 0)
                    @foreach($navDokumen as $menu)
                        <a href="{{ url($menu->url) }}" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium line-clamp-1">
                            {{ $menu->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('dokumen') }}" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium">Pusat Unduhan Dokumen</a>
                @endif
            </div>
        </div>

        <!-- 5. INFORMASI Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" @click="toggleDropdown('m_informasi')" class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('berita') || request()->routeIs('berita.detail') || request()->routeIs('galeri') ? 'bg-emerald-50 text-emerald-800 font-black border border-emerald-200' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>INFORMASI</span>
                <svg class="w-4 h-4 text-slate-400 transition transform" :class="{ 'rotate-180 text-emerald-600': openDropdown === 'm_informasi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_informasi'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                <a href="{{ route('berita') }}" class="block p-2.5 rounded-lg {{ request()->routeIs('berita') || request()->routeIs('berita.detail') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium' }}">Berita & Kabar Desa</a>
                <a href="{{ route('pengumuman') }}" class="block p-2.5 rounded-lg {{ request()->routeIs('pengumuman') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium' }}">Pengumuman Warga</a>
                <a href="{{ route('galeri') }}" class="block p-2.5 rounded-lg {{ request()->routeIs('galeri') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium' }}">Galeri Dokumentasi</a>
            </div>
        </div>

        <!-- 6. HUBUNGI Mobile Accordion -->
        <div class="space-y-1">
            <button type="button" @click="toggleDropdown('m_hubungi')" class="w-full flex items-center justify-between p-3 rounded-xl font-bold text-xs {{ request()->routeIs('lokasi') || request()->routeIs('layanan-whatsapp') ? 'bg-emerald-50 text-emerald-800 font-black border border-emerald-200' : 'text-slate-800 hover:bg-slate-50' }}">
                <span>HUBUNGI</span>
                <svg class="w-4 h-4 text-slate-400 transition transform" :class="{ 'rotate-180 text-emerald-600': openDropdown === 'm_hubungi' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="openDropdown === 'm_hubungi'" x-cloak x-transition class="pl-4 space-y-1 text-xs">
                <a href="{{ route('lokasi') }}" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium {{ request()->routeIs('lokasi') ? 'bg-emerald-50 text-emerald-800' : '' }}">Lokasi Kantor & Alamat Kelurahan</a>
                <a href="{{ route('layanan-whatsapp') }}" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium {{ request()->routeIs('layanan-whatsapp') ? 'bg-emerald-50 text-emerald-800' : '' }}">Layanan WhatsApp CS</a>
                <a href="https://wa.me/6282131001001" target="_blank" rel="noopener noreferrer" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium">Hallo Sae (WhatsApp)</a>
                <a href="https://www.lapor.go.id/" target="_blank" rel="noopener noreferrer" class="block p-2.5 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium">LaporSP4N</a>
            </div>
        </div>

        <!-- Mobile Auth Action Button -->
        <div class="pt-3 border-t border-slate-100">
            @auth
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}" 
                   class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl text-center shadow transition flex items-center justify-center gap-2">
                    <span>🌐</span>
                    <span>Masuk Panel Admin</span>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl text-center shadow transition flex items-center justify-center gap-2">
                    <span>🔑</span>
                    <span>Login Admin / Staf</span>
                </a>
            @endauth
        </div>

    </div>
</nav>

<!-- ========================================== -->
<!-- 3. RUNNING NEWS TICKER BAR                 -->
<!-- ========================================== -->
<div class="bg-emerald-50/60 border-b border-emerald-100 py-2 overflow-hidden text-xs">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 flex items-center gap-3">
        <div class="bg-emerald-600 text-white px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider shrink-0 flex items-center gap-1 shadow-sm shadow-emerald-600/20">
            <span>📣 PENGUMUMAN</span>
            <span class="text-emerald-200 font-bold">›</span>
        </div>

        <div class="overflow-hidden relative w-full text-slate-800 font-medium">
            <div class="animate-marquee flex items-center gap-8">
                @if(isset($globalAnnouncements) && $globalAnnouncements->count() > 0)
                    @foreach($globalAnnouncements as $ann)
                        <span class="inline-flex items-center gap-2">
                            <span class="px-2 py-0.5 {{ $ann->is_urgent ? 'bg-rose-100/80 text-rose-800 border-rose-200/60' : 'bg-emerald-100/80 text-emerald-800 border-emerald-200/60' }} text-[9px] font-bold rounded-md border uppercase">
                                {{ $ann->badge_type ?? 'INFORMASI' }}
                            </span>
                            <span class="font-bold text-slate-800">
                                @if($ann->link_url)
                                    <a href="{{ $ann->link_url }}" class="hover:text-emerald-700 hover:underline" target="_blank">{{ $ann->title }}</a>
                                @else
                                    {{ $ann->title }}
                                @endif
                            </span>
                            <span class="text-slate-500 text-[10px] font-mono">({{ $ann->created_at->format('d M Y') }})</span>
                        </span>
                    @endforeach
                @else
                    <span class="inline-flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-slate-100/80 text-slate-800 text-[9px] font-bold rounded-md border border-slate-200/60">INFO</span>
                        <span class="font-bold text-slate-800">Selamat Datang di Portal Resmi Pemerintah Kelurahan Patokan, Kabupaten Probolinggo</span>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
