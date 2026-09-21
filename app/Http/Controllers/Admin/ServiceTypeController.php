<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceTypeController extends Controller
{
    /**
     * Tampilkan daftar Master Layanan & Jenis Surat Terintegrasi.
     */
    public function index(Request $request)
    {
        $query = ServiceType::orderBy('order', 'asc')->latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $serviceTypes = $query->paginate(12)->withQueryString();

        $totalServices = ServiceType::count();
        $activeServices = ServiceType::where('is_active', true)->count();
        $homepageServices = ServiceType::where('show_on_homepage', true)->count();

        return view('admin.jenis-layanan.index', compact('serviceTypes', 'totalServices', 'activeServices', 'homepageServices'));
    }

    /**
     * Simpan layanan & jenis surat baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name',
            'code' => 'nullable|string|max:20|unique:service_types,code',
            'icon' => 'nullable|string|max:50',
            'badge_label' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'string|max:255',
            'order' => 'nullable|integer|min:0',
            'action_url' => 'nullable|string|max:255',
            'pdf_document' => 'nullable|file|mimes:pdf|max:5120',
            'sop_description' => 'nullable|string',
            'operational_hours' => 'nullable|string|max:255',
            'estimated_time' => 'nullable|string|max:255',
            'cost' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama Layanan / Jenis Surat wajib diisi.',
            'name.unique' => 'Nama Layanan sudah terdaftar dalam sistem.',
            'code.unique' => 'Kode singkatan surat sudah terdaftar.',
        ]);

            $documents = array_values(array_filter($request->input('required_documents', [])));
        
        $pdfPath = null;
        if ($request->hasFile('pdf_document')) {
            $pdfPath = $request->file('pdf_document')->store('services/pdf', 'public');
        }

        ServiceType::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'code' => strtoupper($request->input('code')),
            'icon' => $request->input('icon', '📜'),
            'badge_label' => $request->input('badge_label'),
            'description' => $request->input('description'),
            'required_documents' => $documents,
            'order' => $request->integer('order', 0),
            'show_on_homepage' => $request->boolean('show_on_homepage', true),
            'is_online_request' => $request->boolean('is_online_request', false),
            'action_url' => $request->input('action_url'),
            'is_active' => $request->boolean('is_active', true),
            'pdf_document' => $pdfPath,
            'sop_description' => $request->input('sop_description'),
            'operational_hours' => $request->input('operational_hours'),
            'estimated_time' => $request->input('estimated_time'),
            'cost' => $request->input('cost'),
        ]);

        return back()->with('status', 'Master Layanan & Jenis Surat baru berhasil ditambahkan.');
    }

    /**
     * Perbarui data layanan & jenis surat.
     */
    public function update(Request $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name,' . $id,
            'code' => 'nullable|string|max:20|unique:service_types,code,' . $id,
            'icon' => 'nullable|string|max:50',
            'badge_label' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'string|max:255',
            'order' => 'nullable|integer|min:0',
            'action_url' => 'nullable|string|max:255',
            'pdf_document' => 'nullable|file|mimes:pdf|max:5120',
            'sop_description' => 'nullable|string',
            'operational_hours' => 'nullable|string|max:255',
            'estimated_time' => 'nullable|string|max:255',
            'cost' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama Layanan / Jenis Surat wajib diisi.',
            'name.unique' => 'Nama Layanan sudah terdaftar.',
            'code.unique' => 'Kode singkatan surat sudah terdaftar.',
        ]);

        $documents = array_values(array_filter($request->input('required_documents', [])));

        $data = [
            'name' => $request->input('name'),
            'code' => strtoupper($request->input('code')),
            'icon' => $request->input('icon', '📜'),
            'badge_label' => $request->input('badge_label'),
            'description' => $request->input('description'),
            'required_documents' => $documents,
            'order' => $request->integer('order', 0),
            'show_on_homepage' => $request->boolean('show_on_homepage'),
            'is_online_request' => $request->boolean('is_online_request', false),
            'action_url' => $request->input('action_url'),
            'is_active' => $request->boolean('is_active'),
            'sop_description' => $request->input('sop_description'),
            'operational_hours' => $request->input('operational_hours'),
            'estimated_time' => $request->input('estimated_time'),
            'cost' => $request->input('cost'),
        ];

        if ($request->hasFile('pdf_document')) {
            if ($serviceType->pdf_document && Storage::disk('public')->exists($serviceType->pdf_document)) {
                Storage::disk('public')->delete($serviceType->pdf_document);
            }
            $data['pdf_document'] = $request->file('pdf_document')->store('services/pdf', 'public');
        }

        $serviceType->update($data);

        return back()->with('status', "Data layanan {$serviceType->name} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif layanan.
     */
    public function toggleStatus($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        $serviceType->update([
            'is_active' => !$serviceType->is_active,
        ]);

        $statusText = $serviceType->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('status', "Layanan {$serviceType->name} berhasil {$statusText}.");
    }

    /**
     * Toggle tampilkan di beranda.
     */
    public function toggleHomepage($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        $serviceType->update([
            'show_on_homepage' => !$serviceType->show_on_homepage,
        ]);

        $statusText = $serviceType->show_on_homepage ? 'ditampilkan di beranda' : 'disembunyikan dari beranda';
        return back()->with('status', "Layanan {$serviceType->name} berhasil {$statusText}.");
    }

    /**
     * Hapus layanan & jenis surat.
     */
    public function destroy($id)
    {
        $serviceType = ServiceType::findOrFail($id);

        if ($serviceType->pdf_document && Storage::disk('public')->exists($serviceType->pdf_document)) {
            Storage::disk('public')->delete($serviceType->pdf_document);
        }

        $serviceType->delete();
        return back()->with('status', "Layanan {$serviceType->name} berhasil dihapus.");
    }
}
