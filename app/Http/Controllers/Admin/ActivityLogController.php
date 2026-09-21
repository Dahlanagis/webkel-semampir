<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity log listing with filters.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', strtoupper($request->action));
        }

        $logs = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', now()->today())->count(),
            'creates' => ActivityLog::where('action', 'CREATE')->count(),
            'updates' => ActivityLog::where('action', 'UPDATE')->count(),
            'logins' => ActivityLog::where('action', 'LOGIN')->count(),
        ];

        return view('admin.activity-log.index', compact('logs', 'stats'));
    }

    /**
     * Clear all activity logs.
     */
    public function clear()
    {
        ActivityLog::truncate();

        ActivityLog::record('DELETE', 'Membersihkan seluruh riwayat log aktivitas sistem.');

        return redirect()->route('admin.activity-log.index')->with('success', 'Riwayat log aktivitas berhasil dibersihkan.');
    }
}
