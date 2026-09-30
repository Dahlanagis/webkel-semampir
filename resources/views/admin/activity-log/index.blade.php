@extends('layouts.admin')

@section('title', 'Log Aktivitas Sistem - SIMPEL KELURAHAN SEMAMPIR')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded bg-slate-50 text-slate-700 text-[10px] font-black uppercase tracking-wider border border-slate-200">
                    Audit Trail & Log
                </span>
                <span class="text-xs text-slate-400 font-mono">System Activity Log</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                Log Aktivitas Pengelola & Sistem
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Catatan riwayat aktivitas, aksi pengelola, dan jejak audit portal Kelurahan Semampir.
            </p>
        </div>

        @if($logs->count() > 0)
            <form action="{{ route('admin.activity-log.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh riwayat log aktivitas?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 text-xs font-bold rounded-xl transition border border-rose-200 flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Bersihkan Riwayat Log</span>
                </button>
            </form>
        @endif
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold shrink-0 border border-slate-200">
                📊
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Log</div>
                <div class="text-base sm:text-lg font-black text-slate-900">{{ number_format($stats['total']) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center font-bold shrink-0 border border-slate-200">
                ⚡
            </div>
            <div>
                <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Hari Ini</div>
                <div class="text-base sm:text-lg font-black text-slate-900">{{ number_format($stats['today']) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold shrink-0 border border-blue-200">
                ✏️
            </div>
            <div>
                <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Pengubahan Data</div>
                <div class="text-base sm:text-lg font-black text-slate-900">{{ number_format($stats['updates']) }}</div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold shrink-0 border border-purple-200">
                🔑
            </div>
            <div>
                <div class="text-[10px] font-bold text-purple-600 uppercase tracking-wider">Riwayat Login</div>
                <div class="text-base sm:text-lg font-black text-slate-900">{{ number_format($stats['logins']) }}</div>
            </div>
        </div>
    </div>

    {{-- Filter & Table Container --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-4 p-4 sm:p-6">

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="flex flex-col md:flex-row items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="w-full md:w-auto flex items-center gap-2 overflow-x-auto no-scrollbar">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Aksi:</span>
                
                <a href="{{ route('admin.activity-log.index') }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0
                          {{ !request('action') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Aksi
                </a>
                
                <a href="{{ route('admin.activity-log.index', ['action' => 'LOGIN']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0
                          {{ request('action') == 'LOGIN' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100' }}">
                    LOGIN
                </a>

                <a href="{{ route('admin.activity-log.index', ['action' => 'CREATE']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0
                          {{ request('action') == 'CREATE' ? 'bg-slate-600 text-white shadow-xs' : 'bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100' }}">
                    CREATE
                </a>

                <a href="{{ route('admin.activity-log.index', ['action' => 'UPDATE']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0
                          {{ request('action') == 'UPDATE' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' }}">
                    UPDATE
                </a>

                <a href="{{ route('admin.activity-log.index', ['action' => 'DELETE']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0
                          {{ request('action') == 'DELETE' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }}">
                    DELETE
                </a>
            </div>

            <div class="w-full md:w-72 relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari user, deskripsi, IP..."
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-slate-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>

        {{-- Log Table --}}
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-y border-slate-200">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pengelola / User</th>
                        <th class="py-3 px-4">Aksi</th>
                        <th class="py-3 px-4">Deskripsi Aktivitas</th>
                        <th class="py-3 px-4">Alamat IP & Device</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            {{-- Waktu --}}
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] text-slate-500">
                                <div>{{ $log->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->created_at->format('H:i:s') }} WIB</div>
                            </td>

                            {{-- Pengelola / User --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($log->user_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $log->user_name }}</div>
                                        <div class="text-[10px] text-slate-400">User ID: {{ $log->user_id ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $actionClasses = match($log->action) {
                                        'LOGIN' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'CREATE' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'UPDATE' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'DELETE' => 'bg-rose-100 text-rose-700 border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $actionClasses }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-slate-800 leading-relaxed max-w-md">
                                    {{ $log->description }}
                                </p>
                            </td>

                            {{-- IP & Device --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-[11px] text-slate-500 font-mono">
                                <div class="font-bold text-slate-700">{{ $log->ip_address ?? '127.0.0.1' }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-[180px]" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 24) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    📜
                                </div>
                                <h4 class="font-bold text-slate-700 text-xs">Belum Ada Riwayat Log</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Belum ada catatan aktivitas yang sesuai dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pt-4 border-t border-slate-100 flex justify-center">
            {{ $logs->links() }}
        </div>

    </div>

</div>
@endsection
