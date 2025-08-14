<?php

namespace App\Console\Commands;

use App\Models\Biography;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateBiographyData extends Command
{
    /**
     * The signature of the command, updated with the --source option.
     */
    protected $signature = 'app:generate-biography {name} {--focus=} {--slug=} {--source=}';

    /**
     * The command description.
     */
    protected $description = 'Generates a biography using OpenAI and saves it to the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $focusTopic = $this->option('focus');
        $customSlug = $this->option('slug');
        $sourceText = $this->option('source');

        $this->info("Starting biography generation for: {$name}");
        if ($focusTopic) $this->info("Trending Focus: {$focusTopic}");
        if ($sourceText) $this->info("Using provided source text for trending focus.");

        $slug = $customSlug ? Str::slug($customSlug, '-', 'de') : Str::slug($name, '-', 'de');
        $this->info("Using slug: {$slug}");
        
        $jsonStructure = $this->getJsonStructure();
        [$systemPrompt, $userPrompt] = $this->buildPrompts($name, $focusTopic, $sourceText, $jsonStructure);

        try {
            $this->info('Sending request to OpenAI API... (This may take up to 3 minutes)');
            
            config()->set('openai.request_timeout', 180);

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o', // Using the latest model for best results
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.3, // Lower temperature for more factual, less creative output
            ]);

            $generatedJson = $response->choices[0]->message->content;
            $data = json_decode($generatedJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Failed to parse JSON from OpenAI response.');
                return 1;
            }

            $this->info('Successfully received and parsed data from OpenAI.');
            $this->saveToDatabase($slug, $name, $data);

        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
    
 
private function saveToDatabase(string $slug, string $name, array $aiData)
{
    $biography = Biography::updateOrCreate(
        ['slug' => $slug],
        [
            'full_name' => $aiData['fullName'] ?? $name,
            'status' => 'under_review',
            'content_data' => $aiData,
        ]
    );

        $this->info("Biography for '{$biography->full_name}' saved to database successfully.");
        $this->comment("URL: " . config('app.url') . "de/index.php/{$slug}");
    }

// in app/Console/Commands/GenerateBiographyData.php

private function buildPrompts(string $name, ?string $focusTopic, ?string $sourceText, string $jsonStructure): array
{
    // System prompt remains the same, defining the AI's core rules
    $systemPrompt = <<<PROMPT
You are a meticulous German biographer and SEO content editor. You adhere to the highest standards of quality and E-E-A-T.

**Core Rules:**
1.  **Language:** All output text MUST be in fluent, native German.
2.  **Accuracy:** Do NOT invent facts. If a piece of information is unknown or cannot be verified from reputable sources, use the string "Unbekannt".
3.  **Sourcing:** You MUST cite at least 3 reputable, date-stamped sources (major news, official sites, Wikipedia, academic sources). Do not use forums or unreliable blogs.
4.  **Images:** The `mainImageUrl` MUST be from a source that is clearly licensed for reuse (e.g., Wikimedia Commons, Unsplash, Pexels, or a press kit). You MUST provide credit and license information. If a suitable image cannot be found, use "Unbekannt".
5.  **Output Format:** You MUST return **valid JSON only** that perfectly matches the user-provided schema. Do not add any extra text, comments, or markdown.
6.  **HTML Usage:** You may use the following HTML tags inside content fields: `<p>`, `<h2>`, `<h3>`, `<ul>`, `<ol>`, `<li>`, `<a>`, `<strong>`, `<em>`, `<blockquote>`.
PROMPT;

    // Prepare the dynamic parts of the user prompt
    $focusInstruction = $focusTopic
        ? "**TRENDING_FOCUS:** \"{$focusTopic}\""
        : "**TRENDING_FOCUS:** \"NONE\"";

    $sourceInstruction = $sourceText
        ? "**SOURCE_TEXT for trendingFocus (Use ONLY for that section if provided; otherwise, research normally):**\n{$sourceText}"
        : "**SOURCE_TEXT for trendingFocus (Use ONLY for that section if provided; otherwise, research normally):**\nNONE";

    // ########## THE NEW, UPGRADED USER PROMPT IS HERE ##########
    $userPrompt = <<<PROMPT
**PERSON_NAME:** "{$name}"
{$focusInstruction}
{$sourceInstruction}

**TASK:**
Research the person and generate a complete and highly detailed biography by populating the following JSON structure. The tone should be informative, neutral, and encyclopedic. All content MUST be written in German. Use HTML paragraph tags `<p>` for formatting.

**CONTENT DEPTH REQUIREMENTS:**
- For the `trendingFocus`, `earlyLife`, `career`, and `personalLife` sections, the content should be comprehensive and detailed, aiming for a minimum of 150-200 words for each section.
- Break down complex topics into multiple paragraphs to ensure high-quality, readable content.

**CRITICAL INSTRUCTIONS:**
1.  Ensure the `tableOfContents` titles exactly match the `title` fields of the main content sections.
2.  Provide at least 5 career timeline items.

**SCHEMA:**
{$jsonStructure}
PROMPT;

    return [$systemPrompt, $userPrompt];
}

    private function getJsonStructure(): string
    {
        return <<<JSON
{
    "fullName": "string",
    "seo": {
        "metaTitle": "string",
        "metaDescription": "string"
    },
    "authorship": {
        "authorName": "CelebBio Research Team",
        "factCheckerName": "Editorial Staff",
        "lastUpdated": "YYYY-MM-DD"
    },
    "hero": {
        "h1Title": "string (Format: '[Full Name] – Biografie, Alter, Familie, Karriere, und Fakten')",
        "intro": "string (HTML, 50-70 word summary in German)",
        "mainImageUrl": "string or 'Unbekannt'",
        "imageAltText": "string (Descriptive alt text in German)",
        "imageCaption": "string (Brief, informative caption in German)"
    },
    "quickFacts": {
        "title": "Kurzbiografie auf einen Blick",
        "facts": [
            { "label": "Vollständiger Name", "value": "string" },
            { "label": "Geburtstag", "value": "YYYY-MM-DD or 'Unbekannt'" },
            { "label": "Alter", "value": "string" }
        ]
    },
    "tableOfContents": [
        { "title": "string (Must match a section title)", "anchorId": "string" }
    ],
    "trendingFocus": {
        "title": "string (The trending topic H2)",
        "content": "string (Detailed HTML content)",
        "anchorId": "trending-focus"
    },
    "earlyLife": {
        "title": "Frühes Leben und Ausbildung",
        "content": "string (Detailed HTML content)",
        "anchorId": "fruehes-leben"
    },
    "career": {
        "title": "Karriere",
        "anchorId": "karriere",
        "timeline": [
            { "year": "string", "event": "string", "description": "string" }
        ],
        "sections": [
            { "subtitle": "Karrierebeginn", "content": "string (HTML)" }
        ]
    },
    "personalLife": {
        "title": "Privatleben",
        "content": "string (Detailed HTML content)",
        "anchorId": "privatleben"
    },
    "death": {
        "title": "Tod",
        "content": "string (HTML content, or an empty string '' if not applicable)",
        "anchorId": "tod"
    },
    "netWorth": {
        "title": "Vermögen",
        "content": "string (Detailed HTML content)",
        "anchorId": "vermoegen"
    },
    "faqs": {
        "title": "Häufig gestellte Fragen",
        "anchorId": "faqs",
        "questions": [
            { "question": "string", "answer": "string (HTML)" }
        ]
    },
    "sources": {
        "title": "Quellen und Referenzen",
        "anchorId": "quellen",
        "sourceList": [
            { "title": "string (Title of the source page)", "url": "string (Full URL)", "publisher": "string", "publishedDate": "YYYY-MM-DD or 'Unbekannt'" }
        ]
    }
}
JSON;
    }
}
