<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Petugas Staf Pelayanan.
     */
    public function index(Request $request)
    {
        // Fitur permohonan surat online telah dihapus secara permanen pada sistem.
        // Menggunakan LengthAwarePaginator kosong agar fungsi ->links() di view tidak error.
        $latestRequests = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $latestRequests->withPath($request->url());

        // Karena fitur dihapus, statistik direset ke 0
        $pendingCount = 0;
        $processingCount = 0;
        $completedTodayCount = 0;
        $totalRequestsCount = 0;

        $serviceTypes = \App\Models\ServiceType::where('is_active', true)->get();

        return view('staff.dashboard', compact(
            'latestRequests',
            'pendingCount',
            'processingCount',
            'completedTodayCount',
            'totalRequestsCount',
            'serviceTypes'
        ));
    }

}
