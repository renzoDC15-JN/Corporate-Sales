<?php

namespace App\Filament\Resources\ProspectsResource\Pages;

use App\Filament\Resources\ProspectsResource;
use App\Notifications\ProspectRegistered;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Homeful\Contacts\Data\ContactData;
use Homeful\Contacts\Models\Contact;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;
use Illuminate\Database\Eloquent\Model;

class EditProspects extends EditRecord
{
    use HasRecordSwitcher;
    protected static string $resource = ProspectsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
//        $data['contact'] = $this->record->contact->toArray();
        $data['contact'] = $this->record->contact==null?[]:$this->record->contact->toArray();
//        $contact_data = ContactData::fromModel(new Contact($data));
        $buyer_address_present =$data['contact']==[]?[]: collect($data['contact']['addresses'])->firstWhere('type', 'present') ?? $this->record->contact['addresses'][0];
        $buyer_address_permanent =$data['contact']==[]?[]:  collect($data['contact']['addresses'])->firstWhere('type', 'permanent') ?? $this->record->contact['addresses'][0];
        $buyer_address_present_filtered = array_diff_key($buyer_address_present, ['type' => '']);
        $buyer_address_permanent_filtered = array_diff_key($buyer_address_permanent, ['type' => '']);
        $same_as_permanent = $buyer_address_present_filtered === $buyer_address_permanent_filtered;

        $buyer_address_present['same_as_permanent']=$same_as_permanent;
        $data['buyer_address_present']=$buyer_address_present;
        $data['buyer_address_permanent']=$buyer_address_permanent;
        $buyer_employment = $data['contact']==[]?[]: collect($data['contact']['employment'])->firstWhere('type', 'buyer') ?? [];
        $data['buyer_employment'] =$buyer_employment;
//        $data['idImage']=$this->record->contact->getFirstMedia('id-images');
//        $data['payslipImage']=$this->record->contact->getFirstMedia('payslip-images');

//        dd($data['idImage'],$data['payslipImage']);
        return $data;
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'name_extension' => $data['name_extension'],
            'company' => $data['company'],
            'position_title' => $data['position_title'],
            'salary' => $data['salary'],
            'mid' => $data['mid'],
            'hloan' => $data['hloan'],
            'email' => $data['email'],
            'mobile_number' => $data['mobile_number'],
            'idImage'=> storage_path('app/public/' . $data['idImage']),
            'payslipImage'=> storage_path('app/public/' . $data['payslipImage']),
        ]);

        $record->contact->update([
            'last_name' => $data['contact']['last_name'],
            'first_name' => $data['contact']['first_name'],
            'middle_name' => $data['contact']['middle_name'],
            'name_suffix' => $data['contact']['name_suffix'],
            'civil_status' => $data['contact']['civil_status'],
            'sex' => $data['contact']['sex'],
            'date_of_birth' => $data['contact']['date_of_birth'],
            'nationality' => $data['contact']['nationality'],
            'email' => $data['contact']['email'],
            'mobile' => $data['contact']['mobile'],
            'other_mobile' => $data['contact']['other_mobile'],
            'landline' => $data['contact']['landline'],
        ]);
        if($data['buyer_address_present']['same_as_permanent']==true){
            $data['buyer_address_permanent']=$data['buyer_address_present'];
        }
        $data['buyer_address_present']['type']='present';
        $data['buyer_address_permanent']['type']='permanent';
        $record->contact['addresses']=[
            $data['buyer_address_present'],
            $data['buyer_address_permanent']
        ];
        $data['buyer_employment']['type']='buyer';
        $record->contact['employment']=[
            $data['buyer_employment']
        ];
//        $record->contact->idImage = config('app.url') . '/' . $data['idImage'];
//        $record->contact->payslipImage = config('app.url') . '/' . $data['payslipImage'];
        $record->contact->addMedia(storage_path('app/public/' . $data['idImage']))
            ->toMediaCollection('id-images');

        $record->contact->addMedia(storage_path('app/public/' . $data['payslipImage']))
            ->toMediaCollection('payslip-images');

        $record->contact->save();
        $record->save();
        return $record;
    }

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
