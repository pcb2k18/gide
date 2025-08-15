<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SlugRouterController extends Controller
{
    public function show(string $slug)
    {
        // Cache the slug->(type,id) lookup to avoid repeating the UNION query
        $hit = Cache::remember("slug_router:{$slug}", now()->addMinutes(30), function () use ($slug) {
            // One DB round-trip: union posts and biographies, prefer posts
            $union = DB::table('posts')
                ->select('id', 'slug', DB::raw("'post' as type"))
                ->where('slug', $slug)
                ->unionAll(
                    DB::table('biographies')
                        ->select('id', 'slug', DB::raw("'biography' as type"))
                        ->where('slug', $slug)
                );

            // Wrap union in a subquery so we can order/limit
            return DB::query()
                ->fromSub($union, 'u')
                ->orderByRaw("CASE type WHEN 'post' THEN 0 WHEN 'biography' THEN 1 ELSE 2 END")
                ->limit(1)
                ->first();
        });

        if (!$hit) {
            abort(404);
        }

        // Dispatch to the existing controllers so you keep current view logic
        if ($hit->type === 'post') {
            return app(PostController::class)->show($slug);
        }

        return app(BiographyController::class)->show($slug);
    }
}
