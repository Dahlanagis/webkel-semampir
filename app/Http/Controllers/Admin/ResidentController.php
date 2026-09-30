<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentController extends Controller
{
    /**
     * Tampilkan daftar Data Penduduk dengan filter & pencarian.
     */
    public function index(Request $request)
    {
        $query = Resident::latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender') && $request->input('gender') !== 'all') {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->filled('rt') && $request->input('rt') !== 'all') {
            $query->where('rt', $request->input('rt'));
        }

        if ($request->filled('rw') && $request->input('rw') !== 'all') {
            $query->where('rw', $request->input('rw'));
        }

        $residents = $query->paginate(15)->withQueryString();

        // Statistik
        $totalResidents = Resident::count();
        $maleCount = Resident::where('gender', 'L')->count();
        $femaleCount = Resident::where('gender', 'P')->count();
        $rtList = Resident::select('rt')->distinct()->whereNotNull('rt')->orderBy('rt')->pluck('rt');
        $rwList = Resident::select('rw')->distinct()->whereNotNull('rw')->orderBy('rw')->pluck('rw');

        return view('admin.penduduk.index', compact(
            'residents',
            'totalResidents',
            'maleCount',
            'femaleCount',
            'rtList',
            'rwList'
        ));
    }

    /**
     * Simpan data penduduk baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:residents,nik',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'marital_status' => 'nullable|string|max:30',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'phone_number' => 'nullable|string|max:20',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        Resident::create($request->only([
            'nik', 'name', 'gender', 'birth_place', 'birth_date',
            'religion', 'marital_status', 'occupation', 'address',
            'rt', 'rw', 'phone_number',
        ]));

        return back()->with('status', 'Data penduduk baru berhasil ditambahkan.');
    }

    /**
     * Perbarui data penduduk.
     */
    public function update(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);

        $request->validate([
            'nik' => 'required|string|size:16|unique:residents,nik,' . $id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'marital_status' => 'nullable|string|max:30',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'phone_number' => 'nullable|string|max:20',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'name.required' => 'Nama lengkap wajib diisi.',
        ]);

        $resident->update($request->only([
            'nik', 'name', 'gender', 'birth_place', 'birth_date',
            'religion', 'marital_status', 'occupation', 'address',
            'rt', 'rw', 'phone_number',
        ]));

        return back()->with('status', "Data penduduk {$resident->name} berhasil diperbarui.");
    }

    /**
     * Hapus data penduduk.
     */
    public function destroy($id)
    {
        $resident = Resident::findOrFail($id);
        $resident->delete();

        return back()->with('status', "Data penduduk {$resident->name} berhasil dihapus.");
    }

    /**
     * Ekspor data penduduk ke CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $fileName = 'data_penduduk_semampir_' . date('Ymd_His') . '.csv';

        $query = Resident::latest();

        if ($request->filled('gender') && $request->input('gender') !== 'all') {
            $query->where('gender', $request->input('gender'));
        }
        if ($request->filled('rt') && $request->input('rt') !== 'all') {
            $query->where('rt', $request->input('rt'));
        }
        if ($request->filled('rw') && $request->input('rw') !== 'all') {
            $query->where('rw', $request->input('rw'));
        }

        $residents = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($residents) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'NIK', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir',
                'Tanggal Lahir', 'Agama', 'Status Perkawinan', 'Pekerjaan',
                'Alamat', 'RT', 'RW', 'No Telepon',
            ]);

            foreach ($residents as $r) {
                fputcsv($file, [
                    "'" . $r->nik,
                    $r->name,
                    $r->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    $r->birth_place ?? '-',
                    $r->birth_date ? $r->birth_date->format('Y-m-d') : '-',
                    $r->religion ?? '-',
                    $r->marital_status ?? '-',
                    $r->occupation ?? '-',
                    $r->address ?? '-',
                    $r->rt ?? '-',
                    $r->rw ?? '-',
                    $r->phone_number ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
