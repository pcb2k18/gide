<?php

namespace App\Console\Commands;

use App\Models\Biography;
use App\Services\AiBiographyService; // <-- Import our new service
use Illuminate\Console\Command;
use Illuminate\Support\Str;
// We no longer need the OpenAI facade here, as the service handles it.

class GenerateBiographyData extends Command
{
    /**
     * The signature of the command, updated with the --source option.
     */
    protected $signature = 'app:generate-biography {name} {--focus=} {--slug=} {--source=}';

    /**
     * The command description.
     */
    protected $description = 'Generates a biography using the AiBiographyService and saves it to the database.';

    /**
     * Execute the console command.
     * We use dependency injection to get an instance of our service.
     */
    public function handle(AiBiographyService $aiService)
    {
        $name = $this->argument('name');
        $focusTopic = $this->option('focus');
        $customSlug = $this->option('slug');
        $sourceText = $this->option('source');

        $this->info("Starting biography generation for: {$name}");

        $slug = $customSlug ? Str::slug($customSlug, '-', 'de') : Str::slug($name, '-', 'de');
        $this->info("Using slug: {$slug}");
        
        // Prevent overwriting existing biographies from the command line
        if (Biography::where('slug', $slug)->exists()) {
            $this->error("Error: A biography with the slug '{$slug}' already exists in the database.");
            return 1;
        }

        try {
            $this->info('Sending request to OpenAI API via AiBiographyService...');
            
            // --- The core logic is now handled by the service ---
            $aiData = $aiService->generate($name, $focusTopic, $sourceText);

            $this->info('Successfully received and parsed data. Saving to database...');
            
            // Save the data returned by the service
            $biography = Biography::create([
                'slug' => $slug,
                'full_name' => $aiData['fullName'] ?? $name,
                'status' => 'under_review', // Always set new content to 'under_review'
                'content_data' => $aiData,
            ]);
            
            $this->info("Biography for '{$biography->full_name}' saved successfully.");
            $this->comment("URL: " . config('app.url') . "/de/index.php/{$slug}");

        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
