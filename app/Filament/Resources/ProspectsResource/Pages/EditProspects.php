<?php

namespace App\Filament\Resources\ProspectsResource\Pages;

use App\Filament\Resources\ProspectsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProspects extends EditRecord
{
    protected static string $resource = ProspectsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
