<?php

namespace App\Filament\Actions;

use App\Services\AiBiographyService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Livewire\Component;

class RegenerateSectionContent extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'regenerateSectionContent';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Regenerate Section')
            ->icon('heroicon-o-sparkles')
            ->modalWidth('2xl')
            ->modalHeading('Regenerate Section Content with AI')
            ->form([
                Textarea::make('sourceText')
                    ->label('New Source Text')
                    ->helperText('Paste new source information here. The AI will use this to rewrite the section content.')
                    ->required()
                    ->rows(15),
            ])
            ->action(function (array $data, Component $livewire, AiBiographyService $aiService, array $arguments) {
                // Get the state path of the field this action is attached to
                $statePath = $arguments['statePath'];
                $sectionKey = $arguments['sectionKey'];
                $personName = $livewire->record->full_name;

                Notification::make()->title('Regenerating section...')->info()->send();

                try {
                    // We need to create a new, specialized method in our AI service
                    $newContent = $aiService->regenerateSection(
                        $personName,
                        $sectionKey,
                        $data['sourceText'],
                        $livewire->data[$statePath] // Pass the old content for context
                    );
                    
                    // Update the form field on the screen without a page reload
                    $livewire->data[$statePath] = $newContent;

                    Notification::make()->title('Section Regenerated Successfully')->success()->send();

                } catch (\Exception $e) {
                    Notification::make()->title('AI Regeneration Failed')->body($e->getMessage())->danger()->send();
                }
            });
    }
}
