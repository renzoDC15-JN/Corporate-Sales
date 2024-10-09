<?php

namespace App\Livewire\Prospects;

use App\Models\Prospects;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Homeful\Contacts\Models\Contact;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class CreateProspects extends Component implements HasForms
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
            ->inlineLabel(true)
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->label('First Name')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('middle_name')
                        ->label('Middle Name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->label('Last Name')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('name_extension')
                        ->label('Extension Name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('company')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('position_title')
                        ->label('Position/Title')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\TextInput::make('salary')
                        ->label('Gross Monthly Salary')
                        ->numeric()
                        ->required(),
                    Forms\Components\TextInput::make('mid')
                        ->label('PAG-IBIG Number / MID Number')
                        ->maxLength(255)
                        ->required(),
                    Forms\Components\ToggleButtons::make('hloan')
                        ->label('Existing housing loan with PAG-IBIG?')
                        ->inline(true)
                        ->required()
                        ->boolean(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->live()
                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                            $livewire->validateOnly($component->getStatePath());
                        })
                        ->unique(ignoreRecord: true,table: Contact::class,column: 'email')
                        ->required(),
                    Forms\Components\TextInput::make('mobile_number')
                        ->label('Mobile')
                        ->prefix('+63')
                        ->regex("/^[0-9]+$/")
                        ->minLength(10)
                        ->maxLength(10)
                        ->live()
                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                            $livewire->validateOnly($component->getStatePath());
                        })
                        ->required(),
                ])->columns(1)->columnSpanFull(),

            ])
            ->statePath('data')
            ->model(Prospects::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Prospects::create($data);

        $this->form->model($record)->saveRelationships();

        $this->data =[];
        $this->dispatch('open-modal', id: 'success-modal');
//        dd($this->data);

    }

    public function closeModal()
    {
        $this->dispatch('close-modal', id: 'success-modal');
    }


    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.prospects.create-prospects');
    }
}
