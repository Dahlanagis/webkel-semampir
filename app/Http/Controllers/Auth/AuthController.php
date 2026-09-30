<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan Halaman Login Utama.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserBasedOnRole(Auth::user());
        }

        $this->generateNewCaptchaCode();

        return view('auth.login');
    }

    /**
     * Endpoint API untuk reload/refresh captcha baru via AJAX.
     */
    public function reloadCaptcha()
    {
        $code = $this->generateNewCaptchaCode();
        return response()->json([
            'captcha' => $code
        ]);
    }

    /**
     * Helper internal untuk membuat kode captcha acak 6 karakter.
     */
    protected function generateNewCaptchaCode()
    {
        $chars = '23456789abcdefghjkmnpqrstuvwxyz';
        $captcha = '';
        for ($i = 0; $i < 6; $i++) {
            $captcha .= $chars[rand(0, strlen($chars) - 1)];
        }
        session(['captcha_code' => strtolower($captcha)]);
        return $captcha;
    }

    /**
     * Diproses saat pengguna mengirimkan formulir login.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input Kredensial & Captcha
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|string',
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required' => 'Kode captcha wajib diisi.',
        ]);

        // 2. Validasi Kode Captcha dari Session
        $sessionCaptcha = session('captcha_code');
        $userCaptcha = strtolower(trim($request->input('captcha')));

        if (empty($sessionCaptcha) || $userCaptcha !== $sessionCaptcha) {
            $this->generateNewCaptchaCode();

            return back()->withErrors([
                'captcha' => 'Kode captcha yang Anda masukkan tidak sesuai. Silakan coba lagi.',
            ])->withInput($request->only('login', 'remember'));
        }

        // 3. Terapkan Rate Limiting (Maksimal 5x percobaan per menit)
        $throttleKey = Str::lower($request->input('login')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->generateNewCaptchaCode();

            throw ValidationException::withMessages([
                'login' => ["Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik."],
            ]);
        }

        // 4. Siapkan Kredensial (Dukungan Login via Email ATAU Username)
        $loginInput = trim($request->input('login'));
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => strtolower($loginInput),
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        // 5. Coba Melakukan Autentikasi
        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            session()->forget('captcha_code');
            $request->session()->regenerate();

            return $this->redirectUserBasedOnRole(Auth::user());
        }

        // 6. Catat Percobaan Login Gagal & Reset Captcha
        RateLimiter::hit($throttleKey, 60);
        $this->generateNewCaptchaCode();

        return back()->withErrors([
            'login' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('login', 'remember'));
    }

    /**
     * Proses Validasi & Reset Lupa Password.
     * Kedua kode validasi (No. WhatsApp & Kode Referral) HARUS benar!
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
            'whatsapp' => ['required', 'regex:/^0[0-9]{10,12}$/'],
            'referral_code' => ['required', 'regex:/^[A-Za-z]{3}[0-9]{3}$/'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',       // Huruf Besar
                'regex:/[a-z]/',       // Huruf Kecil
                'regex:/[0-9]/',       // Angka
                'regex:/[@#$%!*_\-]/', // Kode Unik / Simbol
            ],
            'password_confirmation' => 'required|same:password',
        ], [
            'identity.required' => 'Email atau Username wajib diisi.',
            'whatsapp.required' => 'No. WhatsApp (Validasi 1) wajib diisi.',
            'whatsapp.regex' => 'No. WhatsApp harus diawali 0 & panjang 11-13 digit angka.',
            'referral_code.required' => 'Kode Referral (Validasi 2) wajib diisi.',
            'referral_code.regex' => 'Kode Referral harus tepat 3 huruf & 3 angka (misal ADI123).',
            'password.required' => 'Password Baru wajib diisi.',
            'password.min' => 'Password Baru minimal 8 karakter.',
            'password.regex' => 'Password Baru harus mengandung Huruf Besar, Huruf Kecil, Angka, & Simbol Unik.',
            'password_confirmation.required' => 'Konfirmasi Password Baru wajib diisi.',
            'password_confirmation.same' => 'Konfirmasi Password Baru tidak cocok.',
        ]);

        $identity = strtolower(trim($request->input('identity')));
        $whatsapp = trim($request->input('whatsapp'));
        $referralCode = strtoupper(trim($request->input('referral_code')));

        // 1. Cari user berdasarkan email atau username
        $user = User::where(function ($q) use ($identity) {
            $q->where('email', $identity)
              ->orWhere('username', $identity);
        })->first();

        if (!$user) {
            return back()->withErrors([
                'identity' => 'Email atau Username tidak ditemukan dalam sistem.',
            ])->withInput()->with('forgot_modal_open', true);
        }

        // 2. Verifikasi kedua kode validasi (Validasi 1: WhatsApp & Validasi 2: Referral Code)
        $isWhatsappValid = ($user->whatsapp === $whatsapp);
        $isReferralValid = (strtoupper($user->referral_code ?? '') === $referralCode);

        if (!$isWhatsappValid || !$isReferralValid) {
            $customErrors = [];

            if (!$isWhatsappValid) {
                $customErrors['whatsapp'] = 'No. WhatsApp (Validasi 1) tidak sesuai dengan data akun terdaftar.';
            }
            if (!$isReferralValid) {
                $customErrors['referral_code'] = 'Kode Referral (Validasi 2) tidak sesuai dengan data akun terdaftar.';
            }
            $customErrors['forgot_error'] = 'Perubahan password ditolak! Kedua kode validasi (No. WhatsApp & Kode Referral) HARUS terverifikasi benar.';

            return back()->withErrors($customErrors)->withInput()->with('forgot_modal_open', true);
        }

        // 3. Jika kedua kode validasi benar, perbarui password
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return back()->with('status', 'Password akun ' . $user->name . ' berhasil diperbarui! Silakan masuk dengan password baru Anda.');
    }

    /**
     * Redirect pengguna ke Dashboard yang sesuai dengan Role (Admin atau Staf).
     */
    protected function redirectUserBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('status', 'Selamat datang kembali di Panel Administrator, ' . $user->name . '!');
        }

        return redirect()->intended(route('staff.dashboard'))
            ->with('status', 'Selamat bertugas di Panel Staf Pelayanan, ' . $user->name . '!');
    }

    /**
     * Keluar dari aplikasi (Logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda telah berhasil keluar dari akun.');
    }
}
