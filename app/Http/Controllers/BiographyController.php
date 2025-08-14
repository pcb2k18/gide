<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Support\Str;

class BiographyController extends Controller
{
    public function show($slug)
    {
        // Fetch the record. This will 404 if not found.
        $biography = Biography::where('slug', $slug)->firstOrFail();

        // Start with the structured JSON data. This is our base.
        $data = $biography->content_data ?? [];

        // Manually set/overwrite top-level keys from the database columns.
        // This ensures the database is the single source of truth for these items.
        $data['slug'] = $biography->slug;
        $data['fullName'] = $biography->full_name;
        $data['status'] = $biography->status;
        $data['created_at'] = $biography->created_at;
        $data['updated_at'] = $biography->updated_at;

        // --- Build Related Articles ---
        $relatedBiographies = Biography::where('status', 'reviewed')
            ->where('slug', '!=', $slug)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $relatedArticles = [];
        foreach ($relatedBiographies as $relatedBio) {
            $content = $relatedBio->content_data ?? [];
            $relatedArticles[] = [
                'title' => $relatedBio->full_name,
                'excerpt' => Str::limit(strip_tags($content['hero']['intro'] ?? ''), 100),
                'image' => $content['hero']['mainImageUrl'] ?? null,
                'url' => route('biography.show', ['slug' => $relatedBio->slug]),
            ];
        }
        $data['related_articles'] = $relatedArticles;

        // Pass the final, perfectly structured data array to the view.
        return view('biography.show', ['data' => $data]);
    }
}
