<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Category;
use App\Http\Controllers\Admin\VillageProfileController;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Tampilkan halaman publik agenda kegiatan warga kelurahan.
     */
    public function index(Request $request)
    {
        $villageProfile = VillageProfileController::getProfileData();
        $query = Agenda::with('category')->where('is_active', true);

        // Filter Tab (upcoming, this_month, all, completed)
        $tab = $request->input('tab', 'upcoming');

        if ($tab === 'upcoming') {
            $query->where('date', '>=', now()->toDateString())
                  ->where('status', '!=', 'cancelled')
                  ->orderBy('date', 'asc')
                  ->orderBy('time_start', 'asc');
        } elseif ($tab === 'this_month') {
            $query->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                  ->orderBy('date', 'asc');
        } elseif ($tab === 'completed') {
            $query->where(function ($q) {
                $q->where('status', 'completed')
                  ->orWhere('date', '<', now()->toDateString());
            })->latest('date');
        } else {
            // all
            $query->latest('date');
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        $agendas = $query->paginate(9)->withQueryString();
        $categories = Category::where('type', 'agenda')->get();

        // Count for stats
        $upcomingCount = Agenda::where('is_active', true)->where('date', '>=', now()->toDateString())->where('status', '!=', 'cancelled')->count();
        $thisMonthCount = Agenda::where('is_active', true)->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count();
        $totalCount = Agenda::where('is_active', true)->count();

        return view('agenda', compact(
            'agendas',
            'categories',
            'villageProfile',
            'tab',
            'upcomingCount',
            'thisMonthCount',
            'totalCount'
        ));
    }

    /**
     * Tampilkan detail satu agenda kegiatan.
     */
    public function show($slug)
    {
        $villageProfile = VillageProfileController::getProfileData();
        $agenda = Agenda::with('category')->where('is_active', true)->where('slug', $slug)->firstOrFail();
        
        $relatedAgendas = Agenda::where('is_active', true)
            ->where('id', '!=', $agenda->id)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->take(4)
            ->get();

        return view('agenda-detail', compact('agenda', 'villageProfile', 'relatedAgendas'));
    }
}
