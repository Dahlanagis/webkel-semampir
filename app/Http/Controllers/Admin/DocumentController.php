<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\NavigationMenu;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('files')->withCount('files')->latest()->paginate(10);
        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        return view('admin.documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'file_names' => 'nullable|array',
            'file_names.*' => 'required_with:pdf_documents.*|string|max:255',
            'pdf_documents' => 'nullable|array',
            'pdf_documents.*' => 'required_with:file_names.*|mimes:pdf|max:10240',
        ], [
            'name.required' => 'Nama kategori dokumen wajib diisi.',
            'pdf_documents.*.mimes' => 'File ditolak! Format file tidak sesuai, hanya file PDF (.pdf) yang diperbolehkan.',
            'pdf_documents.*.max' => 'File ditolak! Ukuran file PDF melebihi kapasitas maksimal 10 MB.',
        ]);

        $data = $request->except(['pdf_documents', 'file_names']);
        $data['is_active'] = $request->boolean('is_active', true);

        $document = Document::create($data);

        // Auto sync to NavigationMenu
        NavigationMenu::create([
            'section' => 'dokumen',
            'title' => $document->name,
            'url' => '/dokumen?id=' . $document->id,
            'order' => NavigationMenu::where('section', 'dokumen')->max('order') + 1,
            'is_active' => true,
        ]);

        // Handle File Uploads
        if ($request->has('file_names') && $request->hasFile('pdf_documents')) {
            $names = $request->input('file_names');
            $files = $request->file('pdf_documents');

            foreach ($files as $index => $file) {
                if (isset($names[$index])) {
                    $path = $file->store('docs', 'public');
                    DocumentFile::create([
                        'document_id' => $document->id,
                        'name' => $names[$index],
                        'file_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function show(Document $document)
    {
        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $document->load('files');
        return view('admin.documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'file_names' => 'nullable|array',
            'file_names.*' => 'required_with:pdf_documents.*|string|max:255',
            'pdf_documents' => 'nullable|array',
            'pdf_documents.*' => 'required_with:file_names.*|mimes:pdf|max:10240',
        ], [
            'name.required' => 'Nama kategori dokumen wajib diisi.',
            'pdf_documents.*.mimes' => 'File ditolak! Format file tidak sesuai, hanya file PDF (.pdf) yang diperbolehkan.',
            'pdf_documents.*.max' => 'File ditolak! Ukuran file PDF melebihi kapasitas maksimal 10 MB.',
        ]);

        $data = $request->except(['pdf_documents', 'file_names']);
        $data['is_active'] = $request->boolean('is_active');
        
        $oldName = $document->name;
        $document->update($data);

        // Update Navigation Menu title
        if ($oldName !== $document->name) {
            $navMenu = NavigationMenu::where('section', 'dokumen')
                ->where('url', '/dokumen?id=' . $document->id)
                ->first();
            if ($navMenu) {
                $navMenu->update(['title' => $document->name]);
            }
        }

        // Add New File Uploads
        if ($request->has('file_names') && $request->hasFile('pdf_documents')) {
            $names = $request->input('file_names');
            $files = $request->file('pdf_documents');

            foreach ($files as $index => $file) {
                if (isset($names[$index])) {
                    $path = $file->store('docs', 'public');
                    DocumentFile::create([
                        'document_id' => $document->id,
                        'name' => $names[$index],
                        'file_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document)
    {
        $id = $document->id;
        $document->delete(); // Boot method deletes physical files
        
        NavigationMenu::where('section', 'dokumen')
                ->where('url', '/dokumen?id=' . $id)
                ->delete();

        return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function destroyFile($id)
    {
        $file = DocumentFile::findOrFail($id);
        if ($file->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return response()->json(['success' => true, 'message' => 'File berhasil dihapus']);
    }
}
