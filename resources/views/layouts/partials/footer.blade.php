<!-- FOOTER -->
<footer id="kontak" class="bg-[#0b1727] text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Identitas & Sosial Media -->
            <div class="bg-[#152336] rounded-2xl p-6 lg:p-8 flex flex-col justify-between h-full border border-slate-700/50 shadow-sm">
                <div>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center p-1.5 shrink-0">
                            <img src="{{ !empty($systemSettings['app_logo']) ? asset('storage/' . $systemSettings['app_logo']) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlwlIShkVajC2C_tEglw59FLYjmw5n-E1vAgqplpW75A&s=10' }}" 
                                 alt="Logo Kelurahan" 
                                 class="w-full h-full object-contain">
                        </div>
                        <h3 class="text-white font-bold text-lg leading-tight">{{ $systemSettings['app_name'] ?? $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}</h3>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed mb-8">
                        {!! $villageProfile['footer_description'] ?? 'Website Resmi Kelurahan Patokan, Kecamatan Kraksaan, Kabupaten Probolinggo - Portal Informasi Publik, Pelayanan Administrasi Kependudukan, Surat Keterangan Online, Berita, dan Pembangunan Kemasyarakatan.' !!}
                    </p>
                </div>
                <div>
                    <h4 class="text-[10px] font-bold text-slate-300 uppercase tracking-widest mb-4">Media Sosial</h4>
                    <div class="flex items-center gap-3">
                        <a href="{{ $villageProfile['social_instagram'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 hover:bg-emerald-600 transition flex items-center justify-center text-white border border-white/10 hover:border-emerald-500">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="{{ $villageProfile['social_youtube'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 hover:bg-emerald-600 transition flex items-center justify-center text-white border border-white/10 hover:border-emerald-500">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="{{ $villageProfile['social_tiktok'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 hover:bg-emerald-600 transition flex items-center justify-center text-white border border-white/10 hover:border-emerald-500">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="{{ $villageProfile['social_whatsapp'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 hover:bg-emerald-600 transition flex items-center justify-center text-white border border-white/10 hover:border-emerald-500">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: QR Code -->
            <div class="bg-[#152336] rounded-2xl p-6 lg:p-8 flex flex-col items-center justify-center h-full border border-slate-700/50 text-center relative overflow-hidden shadow-sm">
                <div class="w-10 h-1 bg-emerald-500 rounded-full mb-6 mx-auto"></div>
                <h4 class="text-[10px] font-bold text-white uppercase tracking-widest mb-6">Scan Kode QR</h4>
                
                <div class="bg-white p-3 rounded-xl mb-6 shadow-lg inline-block">
                    @if(!empty($villageProfile['qr_code_image']))
                        <img src="{{ asset('storage/' . $villageProfile['qr_code_image']) }}" alt="QR Code Portal" class="w-36 h-36 md:w-44 md:h-44 object-cover">
                    @else
                        <!-- Fallback Placeholder -->
                        <div class="w-36 h-36 md:w-44 md:h-44 bg-slate-100 flex items-center justify-center text-slate-300">
                            <i class="fas fa-qrcode text-5xl"></i>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 text-emerald-400 font-semibold text-sm">
                    <i class="fas fa-hand-pointer"></i>
                    <span>Scan QR Portal Pelayanan</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">{{ $villageProfile['village_name'] ?? 'Kelurahan Patokan' }}</p>
            </div>

            <!-- Card 3: Alamat Kantor -->
            <div class="bg-[#152336] rounded-2xl p-6 lg:p-8 flex flex-col justify-center h-full border border-slate-700/50 shadow-sm">
                <div class="w-10 h-1 bg-emerald-500 rounded-full mb-6"></div>
                <h4 class="text-base font-bold text-white mb-6">Alamat Kantor</h4>
                
                <div class="space-y-6 text-sm text-slate-300">
                    <div class="flex items-start gap-3.5">
                        <div class="mt-0.5 text-emerald-400 shrink-0"><i class="fas fa-map-marker-alt"></i></div>
                        <p class="leading-relaxed">{{ $villageProfile['address'] ?? 'Jl. Pahlawan No. 01, Kelurahan Patokan, Kecamatan Kraksaan, Kabupaten Probolinggo, Jawa Timur 67282' }}</p>
                    </div>
                    
                    <div class="flex items-center gap-3.5">
                        <div class="text-emerald-400 shrink-0"><i class="fas fa-phone-alt"></i></div>
                        <p>Telepon: {{ $villageProfile['phone'] ?? '(0335) 841234' }} <br> WhatsApp: {{ $villageProfile['whatsapp'] ?? '0812-3456-7890' }}</p>
                    </div>
                    
                    <div class="flex items-start gap-3.5">
                        <div class="mt-0.5 text-emerald-400 shrink-0"><i class="fas fa-envelope"></i></div>
                        <p class="break-all">Email: {{ $villageProfile['email'] ?? 'kelurahanpatokan@probolinggokab.go.id' }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom Copyright -->
    <div class="py-6 border-t border-slate-800/50 flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-xs text-slate-500">
        <div class="flex items-center gap-2 mb-4 md:mb-0">
            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700/60 shadow-sm">
                <i class="fas fa-shield-alt text-emerald-500 text-[10px]"></i>
            </div>
        </div>
        <div class="text-center md:text-right">
            {{ $systemSettings['app_name'] ?? $villageProfile['village_name'] ?? 'Kelurahan Patokan' }} &copy; {{ date('Y') }}. All Rights Reserved.
        </div>
    </div>
</footer>
