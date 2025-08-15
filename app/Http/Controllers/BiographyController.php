<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Support\Str;

class BiographyController extends Controller
{
    /**
     * This method is now designed to be called by another controller (like PageController).
     * It takes a pre-fetched Biography model as input.
     */
    public function show(Biography $biography)
    {
        // 1. The biography is already fetched, so we start with the content data.
        $data = $biography->content_data ?? [];

        // 2. Add top-level data from the model itself for consistency.
        $data['slug'] = $biography->slug;
        $data['fullName'] = $biography->full_name;
        $data['status'] = $biography->status;
        $data['updated_at'] = $biography->updated_at;

        // --- 3. Dynamic Internal Linking Logic for "Related Articles" ---
        
        $relatedBiographies = Biography::where('status', 'reviewed')
            ->where('slug', '!=', $biography->slug) // Exclude the current page
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $relatedArticles = [];
        foreach ($relatedBiographies as $relatedBio) {
            $relatedData = $relatedBio->content_data ?? [];
            $relatedArticles[] = [
                'title' => $relatedBio->full_name,
                'excerpt' => Str::limit(strip_tags($relatedData['hero']['intro'] ?? ''), 100),
                'image' => $relatedData['hero']['mainImageUrl'] ?? null,
                // ########## THE FIX IS HERE ##########
                // Use the correct master route name: 'page.show'
                'url' => route('page.show', ['slug' => $relatedBio->slug]),
            ];
        }
        
        $data['related_articles'] = $relatedArticles;

        // --- 4. Return the view with the fully prepared data object ---
        return view('biography.show', [
            'data' => $data,
        ]);
    }
}
