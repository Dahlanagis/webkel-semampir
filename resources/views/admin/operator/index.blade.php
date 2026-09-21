@extends('layouts.admin')

@section('title', 'CRUD Pengguna & Hak Akses (User)')
@section('header-title', 'CRUD Pengguna & Hak Akses (User)')
@section('header-subtitle', 'Kelola akun administrator, super admin, dan staf pelayanan SIMPEL KELURAHAN')

@section('content')
<div class="space-y-6" x-data="{
    createModalOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
    editModalOpen: {{ $errors->any() && old('_method') == 'PUT' ? 'true' : 'false' }},
    showCreatePassword: false,
    showEditPassword: false,
    createPassword: '',
    editPassword: '',
    selectedUser: null,
    previewCreateAvatar: null,
    previewEditAvatar: null,

    // Live password check helper
    isMinLength(val) { return val.length >= 8; },
    hasUpper(val) { return /[A-Z]/.test(val); },
    hasLower(val) { return /[a-z]/.test(val); },
    hasNumber(val) { return /[0-9]/.test(val); },
    hasSymbol(val) { return /[@#$%!*_\-]/.test(val); }
}">

    <!-- Alert Status -->

    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-2xl flex items-center gap-3 text-xs font-semibold shadow-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs space-y-1 shadow-sm">
            <div class="font-bold flex items-center gap-2 text-rose-900">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Terdapat kesalahan dalam pengisian formulir:
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5 text-rose-700 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Pengguna</span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $totalUsers }}</h3>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-600 font-bold">👥</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Administrator / Super Admin</span>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-900 mt-1">{{ $adminCount }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-700 font-bold">🛡️</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-teal-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider">Staf Pelayanan</span>
                <h3 class="text-2xl sm:text-3xl font-black text-teal-900 mt-1">{{ $staffCount }}</h3>
            </div>
            <div class="w-12 h-12 bg-teal-100 rounded-2xl flex items-center justify-center text-teal-700 font-bold">👤</div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.operator.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, email, WA..." class="pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-600 shadow-sm w-64">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select name="role" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white font-medium shadow-sm">
                <option value="all">Semua Role</option>
                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Anggota Staf</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator / Super Admin</option>
            </select>
            <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">Cari</button>
        </form>
        <button @click="createModalOpen = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Pengguna Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[850px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Foto & Nama</th>
                        <th class="py-3.5 px-5">Username</th>
                        <th class="py-3.5 px-5">Email & WhatsApp</th>
                        <th class="py-3.5 px-5">Kode Referral</th>
                        <th class="py-3.5 px-5 text-center">Role</th>
                        <th class="py-3.5 px-5 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50 transition {{ $u->id === auth()->id() ? 'bg-emerald-50/40' : '' }}">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    @if($u->avatar)
                                        <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->name }}" class="w-9 h-9 rounded-full object-cover border border-emerald-500 shadow-sm">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-black flex items-center justify-center text-sm shadow-sm">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                        @if($u->id === auth()->id())
                                            <span class="text-[10px] text-emerald-600 font-bold">(Akun Anda)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 font-mono text-slate-700 font-semibold">{{ $u->username ?? '-' }}</td>
                            <td class="py-3.5 px-5">
                                <div class="font-medium text-slate-800">{{ $u->email }}</div>
                                <div class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1 mt-0.5">
                                    <span>📱</span> {{ $u->whatsapp ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-5 font-mono text-emerald-700 font-bold">
                                {{ $u->referral_code ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-sky-100 text-emerald-900 border border-sky-200 whitespace-nowrap">🛡️ Administrator</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-200 whitespace-nowrap">👤 Anggota Staf</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.operator.toggle', $u->id) }}" method="POST" class="inline">@csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition shadow-sm border {{ ($u->is_active ?? true) ? 'bg-emerald-100 text-emerald-900 border-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-300 hover:bg-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ ($u->is_active ?? true) ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                            {{ ($u->is_active ?? true) ? 'Aktif' : 'Non-Aktif' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Aktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="selectedUser = {{ json_encode($u) }}; editModalOpen = true; editPassword = ''; previewEditAvatar = selectedUser.avatar ? '{{ asset('storage') }}/' + selectedUser.avatar : null" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg transition border border-slate-300">Edit</button>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.operator.reset-password', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Reset password akun ini ke default (Dishub#2026!)?')">@csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-[11px] rounded-lg transition border border-amber-200">Reset PW</button>
                                        </form>
                                        <form action="{{ route('admin.operator.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun operator ini?')">@csrf @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-[11px] rounded-lg transition border border-rose-200">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-400"><p class="font-semibold text-slate-600">Belum ada pengguna terdaftar.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-200 text-xs">{{ $users->links() }}</div>
    </div>

    <!-- MODAL: TAMBAH USER BARU -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            
            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-2xl w-full border border-slate-200 my-8 transform transition-all">
                <form action="{{ route('admin.operator.store') }}" method="POST" enctype="multipart/form-data">@csrf
                    
                    <!-- Header -->
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                            <h3 class="text-base font-bold text-slate-800">Tambah Akun Pengguna Baru</h3>
                        </div>
                        <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                        
                        <!-- 1. Foto Profil (PP Pengguna) -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-emerald-600 text-white font-black flex items-center justify-center text-xl shrink-0 overflow-hidden shadow-md">
                                <template x-if="previewCreateAvatar">
                                    <img :src="previewCreateAvatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!previewCreateAvatar">
                                    <span>U</span>
                                </template>
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-800">Foto Profil (PP Pengguna)</label>
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" 
                                    @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 1, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; previewCreateAvatar = url; } }) }"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                                <p class="text-[11px] text-emerald-700 font-semibold">Format: JPG, PNG, WEBP (Maksimal 5MB)</p>
                            </div>
                        </div>

                        <!-- 2. Nama Lengkap Pengguna -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Nama Lengkap Pengguna <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Sukma Anggota Staf" required 
                                class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                        </div>

                        <!-- 3. Username & Role Hak Akses -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Username Login <span class="text-rose-500">*</span></label>
                                <input type="text" name="username" value="{{ old('username') }}" placeholder="sukma" required 
                                    class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Role Hak Akses <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <select name="role" required class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-semibold text-slate-800 appearance-none">
                                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>👤 Anggota Staf</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>🛡️ Administrator / Super Admin</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Email Resmi Administrator -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1">Email Resmi Administrator <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="sukma@gmail.com" required 
                                class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                            <p class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1 mt-1">
                                <span>ⓘ</span> Email wajib berakhiran <strong class="text-emerald-900">@gmail.com</strong>
                            </p>
                        </div>

                        <!-- 5. No WhatsApp & Kode Referral -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1 flex items-center gap-1">
                                    <span class="text-emerald-600">💬</span> No. WhatsApp (Validasi 1) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="089876543210" required 
                                    class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                                <p class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1 mt-1">
                                    <span>ⓘ</span> Diawali 0 & panjang 11-13 digit angka
                                </p>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1 flex items-center gap-1">
                                    <span class="text-emerald-600">🔑</span> Kode Referral (Validasi 2) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="referral_code" value="{{ old('referral_code') }}" placeholder="SUK202" required uppercase 
                                    class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-mono font-bold">
                                <p class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1 mt-1">
                                    <span>ⓘ</span> Tepat 3 huruf & 3 angka (6 karakter, misal <strong class="text-emerald-900">ADI123</strong>)
                                </p>
                            </div>
                        </div>

                        <!-- 6. Password Keamanan & Gmail-Style Live Validation -->
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between">
                                <label class="block font-bold text-slate-800">Password Keamanan <span class="text-rose-500">*</span></label>
                                <button type="button" @click="showCreatePassword = !showCreatePassword" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1">
                                    <span x-text="showCreatePassword ? '🙈 Sembunyikan' : '👁 Terlihat oleh Super Admin'"></span>
                                </button>
                            </div>

                            <div class="relative">
                                <input :type="showCreatePassword ? 'text' : 'password'" name="password" x-model="createPassword" placeholder="Contoh: Dishub#2026!" required 
                                    class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium pr-10">
                                <button type="button" @click="showCreatePassword = !showCreatePassword" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>

                            <!-- Interactive Gmail Style Live Password Requirements Box -->
                            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200 space-y-2.5">
                                <span class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
                                    SYARAT KOMBINASI PASSWORD RUMIT & AMAN (GMAIL STYLE):
                                </span>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-semibold">
                                    <div class="flex items-center gap-2" :class="isMinLength(createPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="isMinLength(createPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                            <span x-text="isMinLength(createPassword) ? '✓' : '○'"></span>
                                        </span>
                                        <span>Min. 8 Karakter</span>
                                    </div>

                                    <div class="flex items-center gap-2" :class="hasUpper(createPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasUpper(createPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                            <span x-text="hasUpper(createPassword) ? '✓' : '○'"></span>
                                        </span>
                                        <span>Huruf Besar (A-Z)</span>
                                    </div>

                                    <div class="flex items-center gap-2" :class="hasLower(createPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasLower(createPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                            <span x-text="hasLower(createPassword) ? '✓' : '○'"></span>
                                        </span>
                                        <span>Huruf Kecil (a-z)</span>
                                    </div>

                                    <div class="flex items-center gap-2" :class="hasNumber(createPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasNumber(createPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                            <span x-text="hasNumber(createPassword) ? '✓' : '○'"></span>
                                        </span>
                                        <span>Angka (0-9)</span>
                                    </div>

                                    <div class="flex items-center gap-2 sm:col-span-2" :class="hasSymbol(createPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasSymbol(createPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                            <span x-text="hasSymbol(createPassword) ? '✓' : '○'"></span>
                                        </span>
                                        <span>Kode Unik / Simbol (@, #, $, %, !, *)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Buttons -->
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="createModalOpen = false" class="px-5 py-2.5 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Simpan Data User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT USER -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
            <div  class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            
            <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl max-w-2xl w-full border border-slate-200 my-8 transform transition-all">
                <template x-if="selectedUser">
                    <form :action="'{{ url('admin/operator') }}/' + selectedUser.id" method="POST" enctype="multipart/form-data">
                        @csrf 
                        @method('PUT')
                        
                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <h3 class="text-base font-bold text-slate-800">Edit Akun Pengguna</h3>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-5 text-xs text-slate-700 max-h-[80vh] overflow-y-auto">
                            
                            <!-- 1. Foto Profil (PP Pengguna) -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center gap-4">
                                <div class="w-16 h-16 rounded-full bg-emerald-600 text-white font-black flex items-center justify-center text-xl shrink-0 overflow-hidden shadow-md">
                                    <template x-if="previewEditAvatar">
                                        <img :src="previewEditAvatar" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!previewEditAvatar">
                                        <span x-text="selectedUser.name ? selectedUser.name.charAt(0).toUpperCase() : 'U'"></span>
                                    </template>
                                </div>
                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-800">Foto Profil (PP Pengguna)</label>
                                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" 
                                        @change="const file = $event.target.files[0]; if(file) { $dispatch('open-cropper', { file: file, aspectRatio: 1, onCrop: (blob, url) => { let dt = new DataTransfer(); dt.items.add(new File([blob], file.name, {type: file.type})); $event.target.files = dt.files; previewEditAvatar = url; } }) }"
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                                    <p class="text-[11px] text-emerald-700 font-semibold">Format: JPG, PNG, WEBP (Maksimal 5MB)</p>
                                </div>
                            </div>

                            <!-- 2. Nama Lengkap Pengguna -->
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Nama Lengkap Pengguna <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="selectedUser.name" required 
                                    class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                            </div>

                            <!-- 3. Username & Role Hak Akses -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Username Login <span class="text-rose-500">*</span></label>
                                    <input type="text" name="username" x-model="selectedUser.username" required 
                                        class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Role Hak Akses <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="role" x-model="selectedUser.role" required class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-semibold text-slate-800 appearance-none">
                                            <option value="staff">👤 Anggota Staf</option>
                                            <option value="admin">🛡️ Administrator / Super Admin</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Email Resmi Administrator -->
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Email Resmi Administrator <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" x-model="selectedUser.email" required 
                                    class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                                <p class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1 mt-1">
                                    <span>ⓘ</span> Email wajib berakhiran <strong class="text-emerald-900">@gmail.com</strong>
                                </p>
                            </div>

                            <!-- 5. No WhatsApp & Kode Referral -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1 flex items-center gap-1">
                                        <span class="text-emerald-600">💬</span> No. WhatsApp (Validasi 1) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="whatsapp" x-model="selectedUser.whatsapp" required 
                                        class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium">
                                    <p class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1 mt-1">
                                        <span>ⓘ</span> Diawali 0 & panjang 11-13 digit angka
                                    </p>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1 flex items-center gap-1">
                                        <span class="text-emerald-600">🔑</span> Kode Referral (Validasi 2) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="referral_code" x-model="selectedUser.referral_code" required uppercase 
                                        class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-mono font-bold">
                                    <p class="text-[11px] text-emerald-700 font-semibold flex items-center gap-1 mt-1">
                                        <span>ⓘ</span> Tepat 3 huruf & 3 angka (6 karakter, misal <strong class="text-emerald-900">ADI123</strong>)
                                    </p>
                                </div>
                            </div>

                            <!-- 6. Password Keamanan -->
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center justify-between">
                                    <label class="block font-bold text-slate-800">
                                        Password Keamanan <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
                                    </label>
                                    <button type="button" @click="showEditPassword = !showEditPassword" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1">
                                        <span x-text="showEditPassword ? '🙈 Sembunyikan' : '👁 Terlihat oleh Super Admin'"></span>
                                    </button>
                                </div>

                                <div class="relative">
                                    <input :type="showEditPassword ? 'text' : 'password'" name="password" x-model="editPassword" placeholder="Contoh: Dishub#2026!" 
                                        class="w-full p-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium pr-10">
                                    <button type="button" @click="showEditPassword = !showEditPassword" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>

                                <!-- Interactive Gmail Style Live Password Requirements Box -->
                                <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200 space-y-2.5">
                                    <span class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
                                        SYARAT KOMBINASI PASSWORD RUMIT & AMAN (GMAIL STYLE):
                                    </span>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-semibold">
                                        <div class="flex items-center gap-2" :class="isMinLength(editPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="isMinLength(editPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                                <span x-text="isMinLength(editPassword) ? '✓' : '○'"></span>
                                            </span>
                                            <span>Min. 8 Karakter</span>
                                        </div>

                                        <div class="flex items-center gap-2" :class="hasUpper(editPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasUpper(editPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                                <span x-text="hasUpper(editPassword) ? '✓' : '○'"></span>
                                            </span>
                                            <span>Huruf Besar (A-Z)</span>
                                        </div>

                                        <div class="flex items-center gap-2" :class="hasLower(editPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasLower(editPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                                <span x-text="hasLower(editPassword) ? '✓' : '○'"></span>
                                            </span>
                                            <span>Huruf Kecil (a-z)</span>
                                        </div>

                                        <div class="flex items-center gap-2" :class="hasNumber(editPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasNumber(editPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                                <span x-text="hasNumber(editPassword) ? '✓' : '○'"></span>
                                            </span>
                                            <span>Angka (0-9)</span>
                                        </div>

                                        <div class="flex items-center gap-2 sm:col-span-2" :class="hasSymbol(editPassword) ? 'text-emerald-700' : 'text-slate-500'">
                                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="hasSymbol(editPassword) ? 'bg-emerald-600 text-white font-bold' : 'border border-slate-300 text-slate-400'">
                                                <span x-text="hasSymbol(editPassword) ? '✓' : '○'"></span>
                                            </span>
                                            <span>Kode Unik / Simbol (@, #, $, %, !, *)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Footer Buttons -->
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Data User
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection
