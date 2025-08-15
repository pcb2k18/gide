<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use App\Models\Post;

class PageController extends Controller
{
    public function resolve($slug)
    {
        // 1. First, try to find a Biography with this slug.
        $biography = Biography::where('slug', $slug)->first();

        if ($biography) {
            // If found, pass the entire Eloquent model to the show method.
            return app(BiographyController::class)->show($biography);
        }

        // 2. If no biography was found, try to find a Post.
        $post = Post::where('slug', $slug)->where('status', 'published')->first();

        if ($post) {
            // (We will need to update PostController similarly when we build its view)
            return app(PostController::class)->show($post);
        }

        // 3. If nothing was found, return a 404 error.
        abort(404);
    }
}
