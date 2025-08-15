<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use App\Models\Post;

class HomepageController extends Controller
{
    public function index()
    {
        // Fetch the 12 most recently updated, reviewed biographies
        $biographies = Biography::where('status', 'reviewed')
                            ->latest('updated_at')
                            ->limit(12)
                            ->get();

        // Fetch the 6 most recently published guest posts
        $posts = Post::where('status', 'published')
                     ->latest()
                     ->limit(6)
                     ->get();

        return view('homepage', [
            'biographies' => $biographies,
            'posts' => $posts,
        ]);
    }
}
