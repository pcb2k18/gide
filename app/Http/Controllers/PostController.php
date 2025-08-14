<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show($slug)
    {
        // Find the post in the database where the slug matches and the status is 'published'.
        // firstOrFail() will automatically throw a 404 error if not found.
        $post = Post::where('slug', $slug)
                    ->where('status', 'published')
                    ->firstOrFail();

        // Pass the post data to the view.
        return view('post.show', ['post' => $post]);
    }
}
