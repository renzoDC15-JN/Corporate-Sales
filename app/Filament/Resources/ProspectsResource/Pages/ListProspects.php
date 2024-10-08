<?php

namespace App\Filament\Resources\ProspectsResource\Pages;

use App\Filament\Resources\ProspectsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProspects extends ListRecords
{
    protected static string $resource = ProspectsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
