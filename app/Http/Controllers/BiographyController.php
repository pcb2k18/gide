<?php
namespace App\Http\Controllers;
use App\Models\Biography;
use Illuminate\Support\Str;

class BiographyController extends Controller
{
    public function show($slug) // Keep using string parameter since model binding had issues
    {
        // Manually find the biography by slug
        $biography = Biography::where('slug', $slug)->firstOrFail();
        
        // Get the content data
        $data = $biography->content_data ?? [];
        
        // Add top-level data from the model
        $data['slug'] = $biography->slug;
        $data['fullName'] = $biography->full_name;
        $data['status'] = $biography->status;
        $data['updated_at'] = $biography->updated_at;
        
        // Related articles logic
        $relatedBiographies = Biography::whereIn('status', ['reviewed', 'under_review'])
            ->where('slug', '!=', $biography->slug)
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
                'url' => route('biography.show', ['slug' => $relatedBio->slug]),
            ];
        }
        
        $data['related_articles'] = $relatedArticles;
        
        return view('biography.show', [
            'data' => $data,
        ]);
    }
}
