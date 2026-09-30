<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Tampilkan daftar Operator / Akun Pengguna Sistem.
     */
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->where('role', $request->input('role'));
        }

        $users = $query->paginate(15)->withQueryString();

        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $staffCount = User::where('role', 'staff')->count();

        return view('admin.operator.index', compact('users', 'totalUsers', 'adminCount', 'staffCount'));
    }

    /**
     * Simpan operator/user baru dengan validasi ketat.
     */
    public function store(Request $request)
    {
        // Bersihkan dan normalkan format nomor WhatsApp
        $rawWhatsapp = (string)$request->input('whatsapp');
        $whatsappClean = preg_replace('/[^0-9]/', '', $rawWhatsapp);
        if (str_starts_with($whatsappClean, '62')) {
            $whatsappClean = '0' . substr($whatsappClean, 2);
        }
        $request->merge([
            'whatsapp' => $whatsappClean,
            'referral_code' => strtoupper(trim((string)$request->input('referral_code'))),
        ]);

        $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|alpha_dash|unique:users,username',
            'role' => 'required|in:staff,admin',
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'whatsapp' => ['required', 'regex:/^0[0-9]{9,13}$/'],
            'referral_code' => ['required', 'string', 'min:3', 'max:20', 'alpha_num'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'avatar.image' => 'File ditolak! Foto profil harus berupa file gambar.',
            'avatar.mimes' => 'File ditolak! Format foto profil wajib JPG, PNG, atau WEBP.',
            'avatar.max' => 'File ditolak! Ukuran foto profil maksimal 5 MB.',
            'name.required' => 'Nama Lengkap Pengguna wajib diisi.',
            'username.required' => 'Username Login wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan.',
            'role.required' => 'Role Hak Akses wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'email.required' => 'Email Pengguna wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'whatsapp.required' => 'No. WhatsApp wajib diisi.',
            'whatsapp.regex' => 'No. WhatsApp tidak valid (contoh: 08123456789).',
            'referral_code.required' => 'Kode Referral wajib diisi.',
            'referral_code.min' => 'Kode Referral minimal 3 karakter.',
            'referral_code.max' => 'Kode Referral maksimal 20 karakter.',
            'referral_code.alpha_num' => 'Kode Referral hanya boleh berisi huruf dan angka (misal ADI123).',
            'password.required' => 'Password Keamanan wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name' => $request->input('name'),
            'username' => strtolower($request->input('username')),
            'email' => strtolower($request->input('email')),
            'avatar' => $avatarPath,
            'whatsapp' => $request->input('whatsapp'),
            'referral_code' => strtoupper($request->input('referral_code')),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'is_active' => true,
        ]);

        return back()->with('status', 'Akun pengguna baru berhasil dibuat.');
    }

    /**
     * Perbarui data operator/user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Bersihkan dan normalkan format nomor WhatsApp
        $rawWhatsapp = (string)$request->input('whatsapp');
        $whatsappClean = preg_replace('/[^0-9]/', '', $rawWhatsapp);
        if (str_starts_with($whatsappClean, '62')) {
            $whatsappClean = '0' . substr($whatsappClean, 2);
        }
        $request->merge([
            'whatsapp' => $whatsappClean,
            'referral_code' => strtoupper(trim((string)$request->input('referral_code'))),
        ]);

        $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|alpha_dash|unique:users,username,' . $id,
            'role' => 'required|in:staff,admin',
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'whatsapp' => ['required', 'regex:/^0[0-9]{9,13}$/'],
            'referral_code' => ['required', 'string', 'min:3', 'max:20', 'alpha_num'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'avatar.image' => 'File ditolak! Foto profil harus berupa file gambar.',
            'avatar.mimes' => 'File ditolak! Format foto profil wajib JPG, PNG, atau WEBP.',
            'avatar.max' => 'File ditolak! Ukuran foto profil maksimal 5 MB.',
            'name.required' => 'Nama Lengkap Pengguna wajib diisi.',
            'username.required' => 'Username Login wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan.',
            'role.required' => 'Role Hak Akses wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'email.required' => 'Email Pengguna wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'whatsapp.required' => 'No. WhatsApp wajib diisi.',
            'whatsapp.regex' => 'No. WhatsApp tidak valid (contoh: 08123456789).',
            'referral_code.required' => 'Kode Referral wajib diisi.',
            'referral_code.min' => 'Kode Referral minimal 3 karakter.',
            'referral_code.max' => 'Kode Referral maksimal 20 karakter.',
            'referral_code.alpha_num' => 'Kode Referral hanya boleh berisi huruf dan angka (misal ADI123).',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $data = [
            'name' => $request->input('name'),
            'username' => strtolower($request->input('username')),
            'email' => strtolower($request->input('email')),
            'role' => $request->input('role'),
            'whatsapp' => $request->input('whatsapp'),
            'referral_code' => strtoupper($request->input('referral_code')),
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return back()->with('status', "Data akun {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif akun operator.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('warning', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('status', "Akun {$user->name} berhasil {$statusText}.");
    }

    /**
     * Reset password operator ke default.
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $newPassword = 'Dishub#2026!';

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return back()->with('status', "Password akun {$user->name} berhasil direset ke: {$newPassword}");
    }

    /**
     * Hapus akun operator.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('warning', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('status', "Akun {$user->name} berhasil dihapus.");
    }
}
