<?php

namespace App\Livewire;

use App\Models\Prospects;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Homeful\Contacts\Models\Contact;
use Livewire\Component;

class PreRegister extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(3),
                TextInput::make('middle_name')
                    ->label('Middle Name')
                    ->maxLength(255)
                    ->columnSpan(3),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(3),
                TextInput::make('name_extension')
                    ->label('Extension Name')
                    ->maxLength(255)
                    ->columnSpan(3),
                TextInput::make('company')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(3),
                TextInput::make('position_title')
                    ->label('Position/Title')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(3),
                TextInput::make('salary')
                    ->numeric()
                    ->required()
                    ->columnSpan(3),
                TextInput::make('mid')
                    ->label('MID')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(3),
                TextInput::make('hloan')
                    ->label('HLOAN')
                    ->maxLength(255)
                    ->required()
                    ->columnSpan(3),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->live()
                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->unique(ignoreRecord: true,table: Contact::class,column: 'email')
                    ->required()
                    ->columnSpan(3),
                TextInput::make('mobile_number')
                    ->label('Mobile')
                    ->prefix('+63')
                    ->regex("/^[0-9]+$/")
                    ->minLength(10)
                    ->maxLength(10)
                    ->live()
                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->required()
                    ->columnSpan(3),
            ])
            ->statePath('data')
            ->model(Prospects::class);
    }
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pre-register');
    }
}
