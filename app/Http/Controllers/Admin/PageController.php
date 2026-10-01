<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = \App\Models\Page::latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function edit(\App\Models\Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, \App\Models\Page $page)
    {
        $request->validate([
            'subtitle' => 'nullable|string',
            'badge_text' => 'nullable|string|max:50',
            'type' => 'required|in:standard,sotk',
            'banner_image' => 'nullable|image|max:2048',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = [
            'subtitle' => $request->subtitle,
            'badge_text' => $request->badge_text,
            'type' => $request->type,
            'content' => $request->content,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('banner_image')) {
            if ($page->banner_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($page->banner_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('pages/banners', 'public');
        }

        $page->update($data);

        ActivityLog::record('UPDATE', "Memperbarui halaman statis/profil: {$page->title}");

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }
}
