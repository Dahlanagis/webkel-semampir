@extends('layouts.admin')

@section('title', 'Kelola Halaman Dinamis')
@section('header-title', 'Kelola Halaman Dinamis')
@section('header-subtitle', 'Manajemen konten halaman profil (sejarah, potensi, dll) yang dibuat dari Kelola Header')

@section('content')
<div class="space-y-6">
    <!-- Pesan Sukses -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-start gap-3">
        <i class="fas fa-check-circle mt-0.5 text-emerald-600"></i>
        <div>
            <h4 class="font-bold text-sm">Berhasil!</h4>
            <p class="text-xs text-emerald-700 mt-1">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Daftar Halaman</h2>
                <p class="text-sm text-slate-500 mt-1">Halaman ini otomatis terbuat ketika Anda menambahkan tautan di "Dropdown Profil" pada menu Kelola Header.</p>
            </div>
            <a href="{{ route('admin.navigation.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl shadow-sm transition inline-flex items-center gap-2 text-sm border border-slate-200">
                <i class="fas fa-list"></i>
                Ke Kelola Header
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-y border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 font-bold w-12 text-center">No</th>
                        <th class="py-3 px-4 font-bold">Judul Halaman</th>
                        <th class="py-3 px-4 font-bold">Link (URL)</th>
                        <th class="py-3 px-4 font-bold text-center">Status</th>
                        <th class="py-3 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($pages as $index => $page)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-center font-bold text-slate-500">{{ $pages->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800">{{ $page->title }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Terakhir diubah: {{ $page->updated_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-mono text-xs">/halaman/{{ $page->slug }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($page->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">Aktif</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">Sembunyi</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold rounded-lg transition text-xs">
                                    <i class="fas fa-edit"></i> Edit Konten
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center">
                                <div class="w-16 h-16 mx-auto bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-file-alt text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-slate-700">Belum ada halaman</h4>
                                <p class="text-xs text-slate-500 mt-1">Tambahkan tautan "Dropdown Profil" di Kelola Header untuk membuat halaman otomatis.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $pages->links() }}
        </div>
    </div>
</div>
@endsection
