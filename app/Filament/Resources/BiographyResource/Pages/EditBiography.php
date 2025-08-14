<?php

namespace App\Filament\Resources\BiographyResource\Pages;

use App\Filament\Resources\BiographyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBiography extends EditRecord
{
    protected static string $resource = BiographyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // ########## START: AUTOMATIC STATUS UPDATE LOGIC ##########
    
    /**
     * Mutate the form data before it is saved.
     * This is where we automatically change the status to 'reviewed'.
     *
     * @param  array  $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = 'reviewed';
 
        return $data;
    }

    // ########## END: AUTOMATIC STATUS UPDATE LOGIC ##########
}
