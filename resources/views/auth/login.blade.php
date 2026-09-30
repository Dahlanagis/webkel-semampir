<!DOCTYPE html>
<html lang="id" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - Kelurahan Semampir</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Stylized Notebook Grid Pattern for Captcha Box */
        .captcha-grid-bg {
            background-color: #ffffff;
            background-image: 
                linear-gradient(to right, rgba(226, 232, 240, 0.8) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(226, 232, 240, 0.8) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="h-full font-sans bg-white text-slate-800 min-h-screen antialiased selection:bg-sky-500 selection:text-white" 
    x-data="{
        captchaText: '{{ session('captcha_code', '') }}',
        userCaptcha: '',
        showPassword: false,
        showForgotModal: {{ session('forgot_modal_open') || ($errors->has('identity') || $errors->has('whatsapp') || $errors->has('referral_code') || $errors->has('password') || $errors->has('forgot_error') || $errors->has('password_confirmation')) ? 'true' : 'false' }},
        showForgotNewPassword: false,
        forgotPasswordInput: '',
        forgotIdentity: '{{ old('identity') }}',
        forgotWhatsapp: '{{ old('whatsapp') }}',
        forgotReferral: '{{ old('referral_code') }}',

        // Live password check helper for forgot password
        isMinLength(val) { return val.length >= 8; },
        hasUpper(val) { return /[A-Z]/.test(val); },
        hasLower(val) { return /[a-z]/.test(val); },
        hasNumber(val) { return /[0-9]/.test(val); },
        hasSymbol(val) { return /[@#$%!*_\-]/.test(val); },

        init() {
            this.$nextTick(() => {
                if (!this.captchaText) {
                    this.reloadCaptcha();
                } else {
                    this.drawCaptcha(this.captchaText);
                }
            });
        },
        async reloadCaptcha() {
            try {
                const response = await fetch('{{ route('captcha.reload') }}');
                const data = await response.json();
                if (data.captcha) {
                    this.captchaText = data.captcha;
                    this.drawCaptcha(data.captcha);
                    this.userCaptcha = '';
                }
            } catch (err) {
                console.error('Gagal reload captcha:', err);
            }
        },
        drawCaptcha(text) {
            const canvas = this.$refs.captchaCanvas;
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Canvas Grid Line
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 1;
            for(let x = 0; x < canvas.width; x += 24) {
                ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, canvas.height); ctx.stroke();
            }
            for(let y = 0; y < canvas.height; y += 24) {
                ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(canvas.width, y); ctx.stroke();
            }

            // Stylized Colorful Big Letters
            const colors = ['#0f172a', '#1e3a8a', '#1e40af', '#b91c1c', '#047857', '#1d4ed8', '#0369a1'];
            ctx.font = '900 28px Inter, sans-serif';
            ctx.textBaseline = 'middle';

            const charSpacing = canvas.width / (text.length + 1);
            for (let i = 0; i < text.length; i++) {
                ctx.save();
                const x = charSpacing * (i + 0.7);
                const y = canvas.height / 2 + 1;
                ctx.translate(x, y);
                ctx.fillStyle = colors[i % colors.length];
                ctx.fillText(text[i].toLowerCase(), 0, 0);
                ctx.restore();
            }
        },
        fillAccount(login, password) {
            document.getElementById('login').value = login;
            document.getElementById('password').value = password;
        }
    }">

    <div class="h-screen w-full flex flex-col lg:flex-row overflow-hidden bg-white">

        <!-- LEFT SIDE: Banner & Branding -->
        <div class="hidden lg:flex w-full lg:w-1/2 h-screen relative flex-col justify-between p-8 xl:p-12 overflow-hidden bg-slate-900 shrink-0">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/login_left_banner.png') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/30 to-slate-950/60 backdrop-blur-[0.5px]"></div>

            <!-- Content Container -->
            <div class="relative z-10 flex flex-col justify-between h-full w-full">
                <!-- Header Logos -->
                <div class="flex items-center gap-4">
                    <div class="bg-white/95 backdrop-blur p-2 rounded-2xl shadow-lg border border-white/20">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlwlIShkVajC2C_tEglw59FLYjmw5n-E1vAgqplpW75A&s=10" 
                             alt="Logo Pemkab Probolinggo" 
                             class="h-10 xl:h-12 w-auto object-contain">
                    </div>

                    <!-- Endless Probolinggo Stylized Logo Badge -->
                    <div class="flex items-center pl-2">
                        <div class="font-extrabold text-lg xl:text-xl tracking-tight leading-none text-white drop-shadow">
                            <span class="text-[10px] font-bold block text-white/90 lowercase tracking-normal mb-0.5">endless</span>
                            <span class="text-amber-400">probo</span><span class="text-slate-400">linggo</span>
                        </div>
                    </div>
                </div>

                <!-- Main Branding Text -->
                <div class="my-auto py-6 max-w-lg">
                    <h1 class="text-3xl xl:text-4xl font-black text-white leading-tight tracking-tight drop-shadow-md">
                        Kelurahan Semampir
                    </h1>
                    <h2 class="text-sm xl:text-base font-bold text-slate-400 mt-2 tracking-wide drop-shadow">
                        Kecamatan Kraksaan, Kabupaten Probolinggo
                    </h2>
                </div>

                <!-- Footer Tagline -->
                <div class="text-xs text-white/70 font-medium">
                    Pemerintah Kabupaten Probolinggo &bull; SIMPEL Kelurahan Integrasi
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Login Form (Fits viewport height cleanly) -->
        <div class="w-full lg:w-1/2 h-screen max-h-screen bg-white flex flex-col justify-between p-4 sm:p-6 xl:p-10 overflow-y-auto lg:overflow-hidden shrink-0">

            <!-- Top Header Row with "Kembali ke Beranda" Button -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-50 text-slate-700 hover:text-slate-800 font-bold text-xs border border-slate-200 transition shadow-sm group">
                    <svg class="w-4 h-4 text-slate-600 group-hover:-translate-x-0.5 transition transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>

                <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlwlIShkVajC2C_tEglw59FLYjmw5n-E1vAgqplpW75A&s=10" 
                         alt="Logo Pemkab Probolinggo" 
                         class="h-6 w-auto">
                    <span class="hidden xs:inline">Kelurahan Semampir</span>
                </div>
            </div>

            <!-- Form Content Box -->
            <div class="my-auto max-w-md w-full mx-auto space-y-4 py-2">

                <!-- Form Title -->
                <div class="text-center lg:text-left mb-2">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                        Login Admin & Staf
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        Masuk untuk mengelola pelayanan kelurahan
                    </p>
                </div>

                <!-- Quick Credentials Info Box -->
                <div class="bg-slate-50 border border-slate-200 text-slate-800 p-3 rounded-xl text-xs flex items-center justify-between shadow-xs">
                    <div>
                        <span class="font-bold text-slate-700 block">Kredensial Login Admin:</span>
                        <span class="font-mono text-[11px] text-slate-600">Username: <strong class="text-blue-700">admin</strong> | Password: <strong class="text-blue-700">password</strong></span>
                    </div>
                    <button type="button" 
                            @click="fillAccount('admin', 'password')" 
                            class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-bold transition shadow-xs cursor-pointer">
                        Isi Otomatis
                    </button>
                </div>

                <!-- Status Messages & Error Banner -->
                @if(session('status'))
                    <div class="bg-slate-50 border border-slate-200 text-slate-800 px-3.5 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if($errors->has('login') || $errors->has('captcha'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3.5 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm">
                        <span class="w-4 h-4 rounded-full bg-rose-500 text-white flex items-center justify-center text-[10px] font-bold shrink-0">!</span>
                        <span>{{ $errors->first('login') ?: $errors->first('captcha') }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-3">
                    @csrf

                    <!-- Username Field -->
                    <div>
                        <label for="login" class="block text-xs font-bold text-slate-700 mb-1">
                            Username / Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="login" 
                               name="login" 
                               value="{{ old('login', 'admin') }}"
                               required 
                               autofocus
                               placeholder="admin"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-600 focus:bg-white transition shadow-sm">
                    </div>

                    <!-- Password Field with eye toggle icon -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   value="password"
                                   required 
                                   placeholder="••••••••"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-600 focus:bg-white transition shadow-sm pr-10">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 focus:outline-none"
                                    tabindex="-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 01-6 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha Section -->
                    <div class="space-y-1">
                        <label for="captchaInput" class="block text-xs font-bold text-slate-700">
                            Kode Verifikasi Captcha <span class="text-rose-500">*</span>
                        </label>
                        
                        <div class="flex items-center gap-2">
                            <!-- Stylized notebook grid captcha box -->
                            <div class="flex-1 captcha-grid-bg border border-slate-300 rounded-xl overflow-hidden h-10 flex items-center justify-center px-3 relative shadow-inner">
                                <canvas x-ref="captchaCanvas" width="220" height="38" class="w-full h-9 block select-none cursor-pointer" @click="reloadCaptcha()"></canvas>
                            </div>
                            
                            <!-- Green Refresh Button -->
                            <button type="button" 
                                    @click="reloadCaptcha()" 
                                    class="w-10 h-10 bg-slate-600 hover:bg-slate-700 text-white rounded-xl flex items-center justify-center transition shadow shrink-0" 
                                    title="Muat ulang kode captcha">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>

                        <input type="text" 
                               id="captchaInput"
                               name="captcha"
                               x-model="userCaptcha" 
                               required
                               placeholder="Ketik kode di atas..."
                               autocomplete="off"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-600 focus:bg-white transition shadow-sm mt-1">
                    </div>

                    <!-- Lupa Password & Login Row -->
                    <div class="pt-1 space-y-2">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 bg-slate-600 hover:bg-slate-700 text-white font-extrabold text-xs rounded-xl text-center transition shadow-md hover:shadow-lg">
                            Masuk Ke System
                        </button>

                        <div class="flex items-center justify-between text-xs pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600">
                                <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-600 focus:ring-slate-600">
                                <span>Ingat Saya</span>
                            </label>
                            
                            <button type="button" @click="showForgotModal = true" class="text-slate-700 hover:text-slate-900 font-bold hover:underline">
                                Lupa Password?
                            </button>
                        </div>
                    </div>
                </form>

            </div>

            <!-- Footer -->
            <div class="pt-2 text-center text-[11px] text-slate-400 font-medium shrink-0">
                &copy; {{ date('Y') }} Pemerintah Kelurahan Semampir &bull; Kab. Probolinggo
            </div>

        </div>

    </div>

    <!-- MODAL LUPA PASSWORD (WITH FULL VALIDATION) -->
    <div x-show="showForgotModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div @click="showForgotModal = false" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-lg w-full border border-slate-200 my-8">
                <form action="{{ route('password.reset.submit') }}" method="POST">
                    @csrf
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-slate-900 to-slate-900 px-6 py-4 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                            <h3 class="text-base font-bold">Lupa Password / Reset Akun</h3>
                        </div>
                        <button type="button" @click="showForgotModal = false" class="text-slate-300 hover:text-white p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                        <p class="text-slate-500 text-[11px] leading-relaxed">
                            Masukkan Username/Email terdaftar beserta <strong>No. WhatsApp (Validasi 1)</strong> & <strong>Kode Referral (Validasi 2)</strong> akun Anda untuk mereset password.
                        </p>

                        @if($errors->has('forgot_error'))
                            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-xl font-bold flex items-center gap-2">
                                <span>⚠️</span> {{ $errors->first('forgot_error') }}
                            </div>
                        @endif

                        <!-- 1. Email / Username Login -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Email Resmi / Username Login <span class="text-rose-500">*</span></label>
                            <input type="text" name="identity" x-model="forgotIdentity" required placeholder="misal: admin@gmail.com atau admin" 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium">
                            @error('identity')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 2. No WhatsApp (Validasi 1) -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1 flex items-center gap-1">
                                <span class="text-slate-600">💬</span> No. WhatsApp (Validasi 1) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="whatsapp" x-model="forgotWhatsapp" required placeholder="089876543210" 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium">
                            <p class="text-[11px] text-slate-700 font-semibold mt-1">ⓘ Diawali 0 & panjang 11-13 digit angka</p>
                            @error('whatsapp')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 3. Kode Referral (Validasi 2) -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1 flex items-center gap-1">
                                <span class="text-slate-600">🔑</span> Kode Referral (Validasi 2) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="referral_code" x-model="forgotReferral" required uppercase placeholder="SUK202" 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-mono font-bold">
                            <p class="text-[11px] text-slate-700 font-semibold mt-1">ⓘ Tepat 3 huruf & 3 angka (6 karakter, misal ADI123)</p>
                            @error('referral_code')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 4. Password Baru & Live Validation -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <label class="block font-bold text-slate-800">Password Baru <span class="text-rose-500">*</span></label>
                                <button type="button" @click="showForgotNewPassword = !showForgotNewPassword" class="text-[11px] font-bold text-slate-700 hover:underline">
                                    <span x-text="showForgotNewPassword ? '🙈 Sembunyikan' : '👁 Terlihat oleh Super Admin'"></span>
                                </button>
                            </div>

                            <input :type="showForgotNewPassword ? 'text' : 'password'" name="password" x-model="forgotPasswordInput" required placeholder="Contoh: Dishub#2026!" 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium">
                            @error('password')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror

                            <!-- Password Rules Box -->
                            <div class="bg-slate-50/80 p-3.5 rounded-2xl border border-slate-200 space-y-2">
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                    SYARAT KOMBINASI PASSWORD RUMIT & AMAN:
                                </span>
                                <div class="grid grid-cols-2 gap-1.5 text-[11px] font-semibold">
                                    <div class="flex items-center gap-1.5" :class="isMinLength(forgotPasswordInput) ? 'text-slate-700' : 'text-slate-500'">
                                        <span x-text="isMinLength(forgotPasswordInput) ? '✓' : '○'"></span> Min. 8 Karakter
                                    </div>
                                    <div class="flex items-center gap-1.5" :class="hasUpper(forgotPasswordInput) ? 'text-slate-700' : 'text-slate-500'">
                                        <span x-text="hasUpper(forgotPasswordInput) ? '✓' : '○'"></span> Huruf Besar (A-Z)
                                    </div>
                                    <div class="flex items-center gap-1.5" :class="hasLower(forgotPasswordInput) ? 'text-slate-700' : 'text-slate-500'">
                                        <span x-text="hasLower(forgotPasswordInput) ? '✓' : '○'"></span> Huruf Kecil (a-z)
                                    </div>
                                    <div class="flex items-center gap-1.5" :class="hasNumber(forgotPasswordInput) ? 'text-slate-700' : 'text-slate-500'">
                                        <span x-text="hasNumber(forgotPasswordInput) ? '✓' : '○'"></span> Angka (0-9)
                                    </div>
                                    <div class="flex items-center gap-1.5 col-span-2" :class="hasSymbol(forgotPasswordInput) ? 'text-slate-700' : 'text-slate-500'">
                                        <span x-text="hasSymbol(forgotPasswordInput) ? '✓' : '○'"></span> Kode Unik / Simbol (@, #, $, %, !, *)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Konfirmasi Password Baru -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi password baru" 
                                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-600 font-medium">
                            @error('password_confirmation')
                                <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <button type="button" @click="showForgotModal = false" class="px-4 py-2 text-slate-600 font-bold rounded-xl hover:bg-slate-200">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-slate-600 hover:bg-slate-700 text-white font-extrabold rounded-xl shadow-md transition">
                            Reset Password Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
