<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RtRw;
use Illuminate\Http\Request;

class RtRwController extends Controller
{
    /**
     * Tampilkan daftar Data RT/RW.
     */
    public function index(Request $request)
    {
        $query = RtRw::orderBy('type')->orderBy('number');

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('head_name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $rtRwList = $query->paginate(20)->withQueryString();

        // Statistik
        $totalRt = RtRw::where('type', 'RT')->count();
        $totalRw = RtRw::where('type', 'RW')->count();
        $totalKk = RtRw::sum('total_kk');

        return view('admin.rt-rw.index', compact('rtRwList', 'totalRt', 'totalRw', 'totalKk'));
    }

    /**
     * Simpan data RT/RW baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:RT,RW',
            'number' => 'required|string|max:10',
            'head_name' => 'required|string|max:255',
            'head_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'total_kk' => 'nullable|integer|min:0',
        ], [
            'type.required' => 'Tipe RT/RW wajib dipilih.',
            'number.required' => 'Nomor RT/RW wajib diisi.',
            'head_name.required' => 'Nama ketua wajib diisi.',
        ]);

        // Cek duplikat
        $exists = RtRw::where('type', $request->input('type'))
            ->where('number', $request->input('number'))
            ->exists();

        if ($exists) {
            return back()->withErrors(['number' => $request->input('type') . ' ' . $request->input('number') . ' sudah terdaftar.'])->withInput();
        }

        $rtRw = RtRw::create([
            'type' => $request->input('type'),
            'number' => $request->input('number'),
            'head_name' => $request->input('head_name'),
            'head_phone' => $request->input('head_phone'),
            'address' => $request->input('address'),
            'total_kk' => $request->input('total_kk', 0),
        ]);

        ActivityLog::record('CREATE', "Menambahkan data kewilayahan {$rtRw->type} {$rtRw->number} (Ketua: {$rtRw->head_name})");

        return back()->with('status', $request->input('type') . ' ' . $request->input('number') . ' berhasil ditambahkan.');
    }

    /**
     * Perbarui data RT/RW.
     */
    public function update(Request $request, $id)
    {
        $rtRw = RtRw::findOrFail($id);

        $request->validate([
            'type' => 'required|in:RT,RW',
            'number' => 'required|string|max:10',
            'head_name' => 'required|string|max:255',
            'head_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'total_kk' => 'nullable|integer|min:0',
        ]);

        // Cek duplikat kecuali diri sendiri
        $exists = RtRw::where('type', $request->input('type'))
            ->where('number', $request->input('number'))
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['number' => $request->input('type') . ' ' . $request->input('number') . ' sudah terdaftar.'])->withInput();
        }

        $rtRw->update([
            'type' => $request->input('type'),
            'number' => $request->input('number'),
            'head_name' => $request->input('head_name'),
            'head_phone' => $request->input('head_phone'),
            'address' => $request->input('address'),
            'total_kk' => $request->input('total_kk', 0),
        ]);

        ActivityLog::record('UPDATE', "Memperbarui data kewilayahan {$rtRw->type} {$rtRw->number} (Ketua: {$rtRw->head_name})");

        return back()->with('status', $rtRw->type . ' ' . $rtRw->number . ' berhasil diperbarui.');
    }

    /**
     * Hapus data RT/RW.
     */
    public function destroy($id)
    {
        $rtRw = RtRw::findOrFail($id);
        $label = $rtRw->type . ' ' . $rtRw->number;
        $rtRw->delete();

        ActivityLog::record('DELETE', "Menghapus data kewilayahan {$label}");

        return back()->with('status', "{$label} berhasil dihapus.");
    }
}
