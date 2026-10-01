<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    /**
     * Tampilkan daftar agenda kegiatan kelurahan.
     */
    public function index(Request $request)
    {
        $query = Agenda::with('category')->latest('date');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('month')) {
            $month = $request->input('month'); // YYYY-MM
            $query->where('date', 'like', "{$month}%");
        }

        $agendas = $query->paginate(12)->withQueryString();
        $categories = Category::orderByRaw("CASE WHEN type = 'agenda' THEN 0 ELSE 1 END")
            ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Statistik Cepat Agenda
        $totalCount = Agenda::count();
        $upcomingCount = Agenda::where('date', '>=', now()->toDateString())->where('status', '!=', 'cancelled')->count();
        $thisMonthCount = Agenda::whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count();
        $completedCount = Agenda::where('status', 'completed')->count();

        return view('admin.agenda.index', compact(
            'agendas',
            'categories',
            'totalCount',
            'upcomingCount',
            'thisMonthCount',
            'completedCount'
        ));
    }

    /**
     * Simpan agenda kegiatan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'custom_category' => 'nullable|string|max:100',
            'date' => 'required|date',
            'time_start' => 'nullable|string|max:20',
            'time_end' => 'nullable|string|max:20',
            'location' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'coordinator' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Judul atau nama kegiatan wajib diisi.',
            'date.required' => 'Tanggal pelaksanaan kegiatan wajib diisi.',
            'location.required' => 'Lokasi atau tempat kegiatan wajib diisi.',
        ]);

        $categoryId = \App\Models\Category::resolveId($request->input('category_id'), $request->input('custom_category'), 'agenda');

        $slug = Str::slug($request->input('title')) . '-' . date('Ymd', strtotime($request->input('date')));
        if (Agenda::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $agenda = Agenda::create([
            'title' => $request->input('title'),
            'slug' => $slug,
            'category_id' => $categoryId,
            'date' => $request->input('date'),
            'time_start' => $request->input('time_start'),
            'time_end' => $request->input('time_end'),
            'location' => $request->input('location'),
            'organizer' => $request->input('organizer'),
            'coordinator' => $request->input('coordinator'),
            'status' => $request->input('status', 'upcoming'),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record('CREATE', "Menambahkan agenda kegiatan baru: {$agenda->title}");

        return back()->with('status', "Agenda '{$agenda->title}' berhasil ditambahkan ke jadwal kegiatan.");
    }

    /**
     * Perbarui data agenda kegiatan.
     */
    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'custom_category' => 'nullable|string|max:100',
            'date' => 'required|date',
            'time_start' => 'nullable|string|max:20',
            'time_end' => 'nullable|string|max:20',
            'location' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'coordinator' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'Judul atau nama kegiatan wajib diisi.',
            'date.required' => 'Tanggal pelaksanaan kegiatan wajib diisi.',
            'location.required' => 'Lokasi atau tempat kegiatan wajib diisi.',
        ]);

        $categoryId = \App\Models\Category::resolveId($request->input('category_id'), $request->input('custom_category'), 'agenda');

        $agenda->update([
            'title' => $request->input('title'),
            'category_id' => $categoryId,
            'date' => $request->input('date'),
            'time_start' => $request->input('time_start'),
            'time_end' => $request->input('time_end'),
            'location' => $request->input('location'),
            'organizer' => $request->input('organizer'),
            'coordinator' => $request->input('coordinator'),
            'status' => $request->input('status', 'upcoming'),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record('UPDATE', "Memperbarui agenda kegiatan: {$agenda->title}");

        return back()->with('status', "Agenda '{$agenda->title}' berhasil diperbarui.");
    }

    /**
     * Toggle status aktif agenda.
     */
    public function toggle($id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->update([
            'is_active' => !$agenda->is_active,
        ]);

        $statusText = $agenda->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::record('UPDATE', "Status agenda {$agenda->title} {$statusText}");

        return back()->with('status', "Agenda '{$agenda->title}' berhasil {$statusText}.");
    }

    /**
     * Hapus data agenda kegiatan.
     */
    public function destroy($id)
    {
        $agenda = Agenda::findOrFail($id);
        $title = $agenda->title;
        $agenda->delete();

        ActivityLog::record('DELETE', "Menghapus agenda kegiatan: {$title}");

        return back()->with('status', "Agenda '{$title}' berhasil dihapus.");
    }
}
