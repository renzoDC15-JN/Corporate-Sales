<?php

namespace App\Filament\Resources\ProspectsResource\Pages;

use App\Filament\Resources\ProspectsResource;
use App\Notifications\ProspectRegistered;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;
use Illuminate\Database\Eloquent\Model;

class EditProspects extends EditRecord
{
    use HasRecordSwitcher;
    protected static string $resource = ProspectsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Send Registration Succesfull Notification')
                ->action(function (Model $record){
                    $record->notify(new ProspectRegistered($record));
            }),
        ];
    }
}
