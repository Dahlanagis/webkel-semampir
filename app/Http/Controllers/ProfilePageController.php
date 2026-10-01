<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class ProfilePageController extends Controller
{
    public function show($slug)
    {
        if ($slug === 'struktur-organisasi') {
            return redirect()->route('struktur-organisasi');
        }

        $page = Page::where('slug', $slug)
            ->where('category', 'profile')
            ->where('is_active', true)
            ->firstOrFail();

        // otherProfilePages could be used if there's a sidebar navigation in the future
        $otherProfilePages = Page::where('category', 'profile')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('pages.profile-template', compact('page', 'otherProfilePages'));
    }
}
