<?php

namespace App\Filament\Resources\BiographyResource\Pages;

use App\Filament\Resources\BiographyResource;
use App\Models\Biography;
use App\Services\AiBiographyService;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListBiographies extends ListRecords
{
    protected static string $resource = BiographyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // This is the standard "New Biography" button for manual creation.
            Actions\CreateAction::make(), 

            // ########## START: OUR NEW AI GENERATION ACTION ##########
            Actions\Action::make('createWithAi')
                ->label('Create with AI')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->action(function (array $data, AiBiographyService $aiService) {
                    // This is the code that runs when the form is submitted.
                    // We've moved the logic into a helper function below for cleanliness.
                    $this->createBiographyFromAi($data, $aiService);
                })
                ->form([
                    // This defines the fields in our modal form.
                    TextInput::make('name')
                        ->label("Person's Full Name")
                        ->required(),
                    
                    TextInput::make('focus')
                        ->label('Focus Topic')
                        ->helperText('E.g., "her marriage to Tom Kaulitz" or "his early music career".'),

                    TextInput::make('slug')
                        ->helperText('Optional. Will be auto-generated from the name if left blank.'),

                    Textarea::make('source')
                        ->label('Source Text for Trending Focus (Optional)')
                        ->rows(8)
                        ->helperText('Paste text from a news article or source here. The AI will use ONLY this text for the "Trending Focus" section.'),
                ])
                ->modalWidth('2xl') // Make the modal wider to fit the textarea
                ->modalHeading('Generate New Biography with AI'),
            // ########## END: OUR NEW AI GENERATION ACTION ##########
        ];
    }
    
    /**
     * A helper function to contain the AI generation and database saving logic.
     */
    public function createBiographyFromAi(array $formData, AiBiographyService $aiService): void
    {
        $name = $formData['name'];
        $focus = $formData['focus'];
        $customSlug = $formData['slug'];
        $source = $formData['source'];
        
        // Use the custom slug if provided, otherwise generate one from the name.
        $slug = $customSlug ? Str::slug($customSlug, '-', 'de') : Str::slug($name, '-', 'de');
        
        // Safety check: ensure a biography with this slug doesn't already exist.
        if (Biography::where('slug', $slug)->exists()) {
            Notification::make()
                ->title('Error: Slug already exists')
                ->body("A biography with the slug '{$slug}' is already in the database. Please use a unique slug.")
                ->danger()
                ->send();
            return; // Stop execution
        }
        
        try {
            // Show a "processing" notification to the user so they know it's working.
            Notification::make()->title('Generating AI Content...')
                ->body('This may take up to 3 minutes. Please stay on this page.')
                ->info()
                ->send();

            // Call our reusable AiBiographyService to get the data.
            $aiData = $aiService->generate($name, $focus, $source);
            
            // Save the new biography to the database.
            Biography::create([
                'slug' => $slug,
                'full_name' => $aiData['fullName'] ?? $name,
                'status' => 'under_review', // New content always starts as 'under_review'.
                'content_data' => $aiData,
            ]);

            // Send a success notification.
            Notification::make()->title('Success!')
                ->body("Biography for '{$name}' has been created and is ready for your review.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            // If anything goes wrong, send an error notification.
            Notification::make()->title('AI Generation Failed')
                ->body("An error occurred: " . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
