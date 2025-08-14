<?php

namespace App\Filament\Resources\BiographyResource\Pages;

use App\Filament\Resources\BiographyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBiography extends EditRecord
{
    protected static string $resource = BiographyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * This method completely overrides Filament's default save logic,
     * giving us full control to prevent data loss.
     *
     * @param  Model  $record  The existing Biography record from the database.
     * @param  array  $data    The new data from the form.
     * @return Model The saved record.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Debug: Log the incoming data structure
        \Log::info('EditBiography - Incoming data structure:', [
            'keys' => array_keys($data),
            'has_content_data' => isset($data['content_data']),
            'content_data_type' => isset($data['content_data']) ? gettype($data['content_data']) : 'not_set'
        ]);

        // 1. Separate the top-level data from the content_data
        $topLevelData = [
            'full_name' => $data['full_name'] ?? $record->full_name,
            'slug'      => $data['slug'] ?? $record->slug,
            'status'    => 'reviewed', // Set status to 'reviewed' on every save
        ];

        // 2. Start with the existing content_data from the record
        $existingContentData = $record->content_data ?? [];

        // 3. Handle the content_data based on how it's structured
        $newContentData = $this->processContentData($data, $existingContentData);

        // 4. Merge all data back into a single array for saving
        $finalData = array_merge($topLevelData, ['content_data' => $newContentData]);

        // Debug: Log what we're about to save
        \Log::info('EditBiography - Final data to save:', [
            'top_level_keys' => array_keys($finalData),
            'content_data_keys' => array_keys($finalData['content_data'] ?? [])
        ]);

        // 5. Update the record with the final, complete data
        $record->update($finalData);

        return $record;
    }

    /**
     * Process the content_data from the form, handling both nested arrays and dot notation
     */
    private function processContentData(array $formData, array $existingContentData): array
    {
        // If content_data comes as a nested array (modern Filament behavior)
        if (isset($formData['content_data']) && is_array($formData['content_data'])) {
            \Log::info('Processing nested content_data array');
            return $this->mergeContentDataArrays($existingContentData, $formData['content_data']);
        }

        // If content_data comes as dot notation fields (legacy behavior)
        $contentData = $existingContentData;
        $foundDotNotation = false;

        foreach ($formData as $key => $value) {
            if (str_starts_with($key, 'content_data.')) {
                $foundDotNotation = true;
                $path = substr($key, 13); // Remove 'content_data.' prefix
                data_set($contentData, $path, $value);
            }
        }

        if ($foundDotNotation) {
            \Log::info('Processing dot notation content_data fields');
        } else {
            \Log::warning('No content_data found in form submission');
        }

        return $contentData;
    }

    /**
     * Recursively merge content data arrays, preserving existing data
     * when new data is null or empty arrays.
     */
    private function mergeContentDataArrays(array $existing, array $new): array
    {
        foreach ($new as $key => $value) {
            if (is_array($value)) {
                if (isset($existing[$key]) && is_array($existing[$key])) {
                    // Recursively merge nested arrays
                    $existing[$key] = $this->mergeContentDataArrays($existing[$key], $value);
                } else {
                    // New nested array, use it directly
                    $existing[$key] = $value;
                }
            } else {
                // For non-array values, only update if the new value is not null
                // This prevents overwriting existing data with empty form fields
                if ($value !== null && $value !== '') {
                    $existing[$key] = $value;
                } elseif (!isset($existing[$key])) {
                    // If the key doesn't exist in existing data, set it even if empty
                    $existing[$key] = $value;
                }
            }
        }

        return $existing;
    }

    /**
     * Override the mutateFormDataBeforeSave method for additional debugging
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        \Log::info('EditBiography - Form data before mutation:', [
            'data_keys' => array_keys($data),
            'content_data_exists' => isset($data['content_data']),
            'sample_content_data' => isset($data['content_data']) ? array_slice($data['content_data'], 0, 3, true) : 'not_set'
        ]);

        return parent::mutateFormDataBeforeSave($data);
    }
}
