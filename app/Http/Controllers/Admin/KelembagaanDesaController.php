<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LembagaDesa;
use App\Models\BumdesDetail;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KelembagaanDesaController extends Controller
{
    /**
     * Tampilkan daftar Kelembagaan Desa (LKD & BUMDes).
     */
    public function index(Request $request)
    {
        $query = LembagaDesa::with('bumdesDetail')->latest();

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('nama_lembaga', 'like', "%{$search}%")
                  ->orWhere('singkatan', 'like', "%{$search}%")
                  ->orWhere('nama_ketua', 'like', "%{$search}%")
                  ->orWhere('nomor_sk_pendirian', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_lembaga', $request->input('jenis'));
        }

        if ($request->filled('status')) {
            $query->where('status_aktif', $request->input('status') === '1');
        }

        $lembagas = $query->paginate(12)->withQueryString();

        // Statistik Dashboard Kelembagaan
        $totalLembaga = LembagaDesa::count();
        $totalLkd = LembagaDesa::where('jenis_lembaga', 'LKD')->count();
        $totalBumdes = LembagaDesa::where('jenis_lembaga', 'BUMDes')->count();
        $totalAktif = LembagaDesa::where('status_aktif', true)->count();

        // Hitung perkiraan unit usaha BUMDes berjalan
        $bumdesDetails = BumdesDetail::whereNotNull('daftar_unit_usaha')->pluck('daftar_unit_usaha');
        $unitUsahaCount = 0;
        foreach ($bumdesDetails as $units) {
            $parts = array_filter(array_map('trim', explode(',', $units)));
            $unitUsahaCount += count($parts);
        }

        return view('admin.kelembagaan.index', compact(
            'lembagas',
            'totalLembaga',
            'totalLkd',
            'totalBumdes',
            'totalAktif',
            'unitUsahaCount'
        ));
    }

    /**
     * Simpan data Lembaga Desa baru (termasuk detail BUMDes jika relevan).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'jenis_lembaga' => 'required|in:LKD,BUMDes,Lembaga Pemerintahan,Lembaga Adat',
            'singkatan' => 'nullable|string|max:50',
            'nomor_sk_pendirian' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'dasar_hukum' => 'nullable|string|max:255',
            'nama_ketua' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:50',
            'alamat_kantor' => 'nullable|string|max:500',
            'deskripsi_profil' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',

            // Validasi Khusus BUMDes
            'nomor_badan_hukum_kemenkumham' => 'nullable|string|max:100',
            'tahun_pendirian' => 'nullable|integer|min:1900|max:2100',
            'npwp_bumdes' => 'nullable|string|max:50',
            'kategori_status' => 'nullable|in:Perintis,Berkembang,Maju,Mandiri',
            'permodalan_awal' => 'nullable|numeric|min:0',
            'total_aset' => 'nullable|numeric|min:0',
            'omzet_terakhir' => 'nullable|numeric|min:0',
            'daftar_unit_usaha' => 'nullable|string',
            'nama_penasihat' => 'nullable|string|max:255',
            'nama_pelaksana_operasional' => 'nullable|string|max:255',
            'nama_pengawas' => 'nullable|string|max:255',
        ], [
            'nama_lembaga.required' => 'Nama lembaga desa wajib diisi.',
            'jenis_lembaga.required' => 'Pilih jenis kelembagaan yang sesuai.',
        ]);

        $slug = Str::slug($request->input('nama_lembaga'));
        if (LembagaDesa::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $lembaga = LembagaDesa::create([
            'nama_lembaga' => trim($request->input('nama_lembaga')),
            'singkatan' => trim($request->input('singkatan') ?? ''),
            'slug' => $slug,
            'jenis_lembaga' => $request->input('jenis_lembaga'),
            'nomor_sk_pendirian' => trim($request->input('nomor_sk_pendirian') ?? ''),
            'tanggal_sk' => $request->input('tanggal_sk'),
            'dasar_hukum' => trim($request->input('dasar_hukum') ?? ''),
            'nama_ketua' => trim($request->input('nama_ketua') ?? ''),
            'kontak' => trim($request->input('kontak') ?? ''),
            'alamat_kantor' => trim($request->input('alamat_kantor') ?? ''),
            'deskripsi_profil' => $request->input('deskripsi_profil'),
            'status_aktif' => $request->boolean('status_aktif', true),
        ]);

        // Simpan detail BUMDes jika jenisnya BUMDes
        if ($lembaga->jenis_lembaga === 'BUMDes') {
            BumdesDetail::create([
                'lembaga_id' => $lembaga->id,
                'nomor_badan_hukum_kemenkumham' => trim($request->input('nomor_badan_hukum_kemenkumham') ?? ''),
                'tahun_pendirian' => $request->filled('tahun_pendirian') ? (int)$request->input('tahun_pendirian') : null,
                'npwp_bumdes' => trim($request->input('npwp_bumdes') ?? ''),
                'kategori_status' => $request->input('kategori_status', 'Berkembang'),
                'permodalan_awal' => $request->input('permodalan_awal', 0),
                'total_aset' => $request->input('total_aset', 0),
                'omzet_terakhir' => $request->input('omzet_terakhir', 0),
                'daftar_unit_usaha' => trim($request->input('daftar_unit_usaha') ?? ''),
                'nama_penasihat' => trim($request->input('nama_penasihat') ?? ''),
                'nama_pelaksana_operasional' => trim($request->input('nama_pelaksana_operasional') ?? ''),
                'nama_pengawas' => trim($request->input('nama_pengawas') ?? ''),
            ]);
        }

        ActivityLog::record('CREATE', "Menambahkan kelembagaan desa: {$lembaga->nama_lembaga} ({$lembaga->jenis_lembaga})");

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kelembagaan '{$lembaga->nama_lembaga}' berhasil ditambahkan.",
                'data' => $lembaga->load('bumdesDetail'),
            ]);
        }

        return redirect()->route('admin.kelembagaan.index')->with('status', "Kelembagaan '{$lembaga->nama_lembaga}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data Lembaga Desa & detail BUMDes.
     */
    public function update(Request $request, $id)
    {
        $lembaga = LembagaDesa::findOrFail($id);

        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'jenis_lembaga' => 'required|in:LKD,BUMDes,Lembaga Pemerintahan,Lembaga Adat',
            'singkatan' => 'nullable|string|max:50',
            'nomor_sk_pendirian' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'dasar_hukum' => 'nullable|string|max:255',
            'nama_ketua' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:50',
            'alamat_kantor' => 'nullable|string|max:500',
            'deskripsi_profil' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',

            // Validasi Khusus BUMDes
            'nomor_badan_hukum_kemenkumham' => 'nullable|string|max:100',
            'tahun_pendirian' => 'nullable|integer|min:1900|max:2100',
            'npwp_bumdes' => 'nullable|string|max:50',
            'kategori_status' => 'nullable|in:Perintis,Berkembang,Maju,Mandiri',
            'permodalan_awal' => 'nullable|numeric|min:0',
            'total_aset' => 'nullable|numeric|min:0',
            'omzet_terakhir' => 'nullable|numeric|min:0',
            'daftar_unit_usaha' => 'nullable|string',
            'nama_penasihat' => 'nullable|string|max:255',
            'nama_pelaksana_operasional' => 'nullable|string|max:255',
            'nama_pengawas' => 'nullable|string|max:255',
        ], [
            'nama_lembaga.required' => 'Nama lembaga desa wajib diisi.',
            'jenis_lembaga.required' => 'Pilih jenis kelembagaan yang sesuai.',
        ]);

        $slug = $lembaga->slug;
        if ($lembaga->nama_lembaga !== $request->input('nama_lembaga')) {
            $slug = Str::slug($request->input('nama_lembaga'));
            if (LembagaDesa::where('slug', $slug)->where('id', '!=', $lembaga->id)->exists()) {
                $slug = $slug . '-' . time();
            }
        }

        $lembaga->update([
            'nama_lembaga' => trim($request->input('nama_lembaga')),
            'singkatan' => trim($request->input('singkatan') ?? ''),
            'slug' => $slug,
            'jenis_lembaga' => $request->input('jenis_lembaga'),
            'nomor_sk_pendirian' => trim($request->input('nomor_sk_pendirian') ?? ''),
            'tanggal_sk' => $request->input('tanggal_sk'),
            'dasar_hukum' => trim($request->input('dasar_hukum') ?? ''),
            'nama_ketua' => trim($request->input('nama_ketua') ?? ''),
            'kontak' => trim($request->input('kontak') ?? ''),
            'alamat_kantor' => trim($request->input('alamat_kantor') ?? ''),
            'deskripsi_profil' => $request->input('deskripsi_profil'),
            'status_aktif' => $request->boolean('status_aktif', true),
        ]);

        // Simpan / Perbarui Detail BUMDes
        if ($lembaga->jenis_lembaga === 'BUMDes') {
            BumdesDetail::updateOrCreate(
                ['lembaga_id' => $lembaga->id],
                [
                    'nomor_badan_hukum_kemenkumham' => trim($request->input('nomor_badan_hukum_kemenkumham') ?? ''),
                    'tahun_pendirian' => $request->filled('tahun_pendirian') ? (int)$request->input('tahun_pendirian') : null,
                    'npwp_bumdes' => trim($request->input('npwp_bumdes') ?? ''),
                    'kategori_status' => $request->input('kategori_status', 'Berkembang'),
                    'permodalan_awal' => $request->input('permodalan_awal', 0),
                    'total_aset' => $request->input('total_aset', 0),
                    'omzet_terakhir' => $request->input('omzet_terakhir', 0),
                    'daftar_unit_usaha' => trim($request->input('daftar_unit_usaha') ?? ''),
                    'nama_penasihat' => trim($request->input('nama_penasihat') ?? ''),
                    'nama_pelaksana_operasional' => trim($request->input('nama_pelaksana_operasional') ?? ''),
                    'nama_pengawas' => trim($request->input('nama_pengawas') ?? ''),
                ]
            );
        }

        ActivityLog::record('UPDATE', "Memperbarui data kelembagaan desa: {$lembaga->nama_lembaga}");

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data '{$lembaga->nama_lembaga}' berhasil diperbarui.",
                'data' => $lembaga->load('bumdesDetail'),
            ]);
        }

        return redirect()->route('admin.kelembagaan.index')->with('status', "Data '{$lembaga->nama_lembaga}' berhasil diperbarui.");
    }

    /**
     * Ubah status aktif / nonaktif lembaga.
     */
    public function toggle(Request $request, $id)
    {
        $lembaga = LembagaDesa::findOrFail($id);
        $lembaga->status_aktif = !$lembaga->status_aktif;
        $lembaga->save();

        $statusText = $lembaga->status_aktif ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::record('UPDATE', "Status kelembagaan '{$lembaga->nama_lembaga}' {$statusText}");

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status_aktif' => $lembaga->status_aktif,
                'message' => "Lembaga '{$lembaga->nama_lembaga}' berhasil {$statusText}.",
            ]);
        }

        return back()->with('status', "Lembaga '{$lembaga->nama_lembaga}' berhasil {$statusText}.");
    }

    /**
     * Hapus data kelembagaan desa.
     */
    public function destroy(Request $request, $id)
    {
        $lembaga = LembagaDesa::findOrFail($id);
        $nama = $lembaga->nama_lembaga;
        $lembaga->delete();

        ActivityLog::record('DELETE', "Menghapus kelembagaan desa: {$nama}");

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kelembagaan '{$nama}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('admin.kelembagaan.index')->with('status', "Kelembagaan '{$nama}' berhasil dihapus.");
    }
}
