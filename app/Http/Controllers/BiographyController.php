<?php
namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Support\Str;

class BiographyController extends Controller
{
    public function show($slug)
    {
        $biography = Biography::where('slug', $slug)->firstOrFail();
        
        $data = $biography->content_data;
        $data['status'] = $biography->status;
        $data['updated_at'] = $biography->updated_at;
        $data['fullName'] = $biography->full_name;

        // Add missing title and anchorId fields
        $data = $this->addMissingFields($data);

        // Generate table of contents
        $data['tableOfContents'] = $this->generateTableOfContents($data);

        // --- Related Articles ---
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

        return view('biography.show', ['data' => $data]);
    }

    private function addMissingFields($data)
    {
        // Add titles and anchorIds for sections
        $sections = [
            'trendingFocus' => ['title' => 'Aktuelle Entwicklungen', 'anchorId' => 'trending-focus'],
            'earlyLife' => ['title' => 'Frühe Jahre', 'anchorId' => 'early-life'],
            'career' => ['title' => 'Karriere', 'anchorId' => 'career'],
            'personalLife' => ['title' => 'Privatleben', 'anchorId' => 'personal-life'],
            'death' => ['title' => 'Tod', 'anchorId' => 'death'],
            'netWorth' => ['title' => 'Vermögen', 'anchorId' => 'net-worth']
        ];

        foreach ($sections as $sectionKey => $defaults) {
            if (isset($data[$sectionKey])) {
                $data[$sectionKey]['title'] = $data[$sectionKey]['title'] ?? $defaults['title'];
                $data[$sectionKey]['anchorId'] = $data[$sectionKey]['anchorId'] ?? $defaults['anchorId'];
            }
        }

        return $data;
    }

    private function generateTableOfContents($data)
    {
        $toc = [];
        
        $sections = [
            'trendingFocus' => 'Aktuelle Entwicklungen',
            'earlyLife' => 'Frühe Jahre', 
            'career' => 'Karriere',
            'personalLife' => 'Privatleben',
            'death' => 'Tod',
            'netWorth' => 'Vermögen'
        ];

        foreach ($sections as $key => $title) {
            if (!empty($data[$key]['content']) || !empty($data[$key]['timeline']) || !empty($data[$key]['sections'])) {
                $toc[] = [
                    'title' => $data[$key]['title'] ?? $title,
                    'anchorId' => $data[$key]['anchorId'] ?? str_replace('_', '-', strtolower($key))
                ];
            }
        }

        // Always add FAQs if they exist
        if (!empty($data['faqs']['questions'])) {
            $toc[] = [
                'title' => 'Häufig gestellte Fragen',
                'anchorId' => 'faqs'
            ];
        }

        // Always add Sources if they exist
        if (!empty($data['sources']['sourceList'])) {
            $toc[] = [
                'title' => 'Quellen und Referenzen', 
                'anchorId' => 'quellen'
            ];
        }

        return $toc;
    }
}
