<?php

namespace App\Services;

use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class AiBiographyService
{
    public function generate(string $name, ?string $focusTopic, ?string $sourceText): array
    {
        $jsonStructure = $this->getJsonStructure();
        [$systemPrompt, $userPrompt] = $this->buildPrompts($name, $focusTopic, $sourceText, $jsonStructure);

        config()->set('openai.request_timeout', 180);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => 0.3,
        ]);

        $generatedJson = $response->choices[0]->message->content;
        $data = json_decode($generatedJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse JSON from OpenAI response.');
        }

        return $data;
    }

     public function regenerateSection(string $personName, string $sectionTitle, string $sourceText, ?string $oldContent = ''): string
    {
        $prompt = <<<PROMPT
You are an expert German-language editor. Your task is to rewrite and improve a specific section of a biography based on new source material provided by a human editor.

**BIOGRAPHY SUBJECT:**
{$personName}

**SECTION TO REWRITE:**
{$sectionTitle}

**NEW SOURCE MATERIAL TO USE (Primary Focus):**
---
{$sourceText}
---

**OLD CONTENT (For Context Only):**
---
{$oldContent}
---

**YOUR TASK:**
Using the NEW SOURCE MATERIAL as your primary guide, generate new, improved HTML content for the '{$sectionTitle}' section. The tone must be neutral and encyclopedic. Use HTML tags like `<p>`, `<ul>`, `<li>`, `<strong>`. Your response MUST be ONLY the new HTML content for the section, with no extra text, explanations, or JSON formatting.
PROMPT;

        config()->set('openai.request_timeout', 180);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
            ],
            'temperature' => 0.4,
        ]);
        
        return $response->choices[0]->message->content;
    }
    // ########## END: NEW REGENERATION METHOD ##########

    private function buildPrompts(string $name, ?string $focusTopic, ?string $sourceText, string $jsonStructure): array
    {
        $systemPrompt = <<<PROMPT
You are a meticulous German biographer and SEO content editor. You adhere to the highest standards of quality and E-E-A-T.

**Core Rules:**
1.  **Language:** All output text MUST be in fluent, native German.
2.  **Accuracy:** Do NOT invent facts. If a piece of information is unknown or cannot be verified from reputable sources, use the string "Unbekannt".
3.  **Sourcing:** You MUST cite at least 3 reputable, date-stamped sources (major news, official sites, Wikipedia, academic sources).
4.  **Images:** The `mainImageUrl` MUST be from a source that is clearly licensed for reuse (e.g., Wikimedia Commons, Unsplash). You MUST provide credit and license information. If a suitable image cannot be found, use "Unbekannt".
5.  **Output Format:** You MUST return **valid JSON only** that perfectly matches the user-provided schema.
6.  **HTML Usage:** You may use the following HTML tags inside content fields: `<p>`, `<h2>`, `<h3>`, `<ul>`, `<ol>`, `<li>`, `<a>`, `<strong>`, `<em>`, `<blockquote>`.
PROMPT;

        $focusInstruction = $focusTopic ? "**TRENDING_FOCUS:** \"{$focusTopic}\"" : "**TRENDING_FOCUS:** \"NONE\"";
        $sourceInstruction = $sourceText ? "**SOURCE_TEXT for trendingFocus:**\n{$sourceText}" : "**SOURCE_TEXT for trendingFocus:**\nNONE";

        $userPrompt = <<<PROMPT
**PERSON_NAME:** "{$name}"
{$focusInstruction}
{$sourceInstruction}

**TASK:**
Research the person and generate a complete, highly detailed biography by populating the following JSON structure.

**CONTENT DEPTH REQUIREMENTS:**
- Aim for a minimum of 250-300 words for each main section (`trendingFocus`, `earlyLife`, `career`, `personalLife`).
- Break down complex topics into multiple paragraphs.

**CRITICAL INSTRUCTIONS:**
1.  The `tableOfContents` titles MUST exactly match the `title` fields of the main content sections.
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
    "seo": { "metaTitle": "string", "metaDescription": "string" },
    "authorship": { "authorName": "CelebBio Research Team", "factCheckerName": "Editorial Staff", "lastUpdated": "YYYY-MM-DD" },
    "hero": { "h1Title": "string", "intro": "string (HTML, 50-70 words)", "mainImageUrl": "string or 'Unbekannt'", "imageAltText": "string", "imageCaption": "string" },
    "quickFacts": { "title": "Kurzbiografie auf einen Blick", "facts": [ { "label": "string", "value": "string" } ] },
    "tableOfContents": [ { "title": "string", "anchorId": "string" } ],
    "trendingFocus": { "title": "string", "content": "string (HTML)", "anchorId": "trending-focus" },
    "earlyLife": { "title": "Frühes Leben und Ausbildung", "content": "string (HTML)", "anchorId": "fruehes-leben" },
    "career": { "title": "Karriere", "anchorId": "karriere", "timeline": [ { "year": "string", "event": "string", "description": "string" } ], "sections": [ { "subtitle": "Karrierebeginn", "content": "string (HTML)" } ] },
    "personalLife": { "title": "Privatleben", "content": "string (HTML)", "anchorId": "privatleben" },
    "death": { "title": "Tod", "content": "string (HTML, or empty string '')", "anchorId": "tod" },
    "netWorth": { "title": "Vermögen", "content": "string (HTML)", "anchorId": "vermoegen" },
    "faqs": { "title": "Häufig gestellte Fragen", "anchorId": "faqs", "questions": [ { "question": "string", "answer": "string (HTML)" } ] },
    "sources": { "title": "Quellen und Referenzen", "anchorId": "quellen", "sourceList": [ { "title": "string", "url": "string", "publisher": "string", "publishedDate": "YYYY-MM-DD or 'Unbekannt'" } ] }
}
JSON;
    }
}
