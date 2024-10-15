<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectsResource\Pages;
use App\Filament\Resources\ProspectsResource\RelationManagers;
use App\Models\CivilStatus;
use App\Models\Country;
use App\Models\CurrentPosition;
use App\Models\EmploymentStatus;
use App\Models\EmploymentType;
use App\Models\HomeOwnership;
use App\Models\NameSuffix;
use App\Models\Nationality;
use App\Models\PhilippineBarangay;
use App\Models\PhilippineCity;
use App\Models\PhilippineProvince;
use App\Models\PhilippineRegion;
use App\Models\Prospects;
use App\Models\Tenure;
use App\Models\WorkIndustry;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ProspectsResource extends Resource
{
    protected static ?string $model = Prospects::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'last_name';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema(
                        [
                            Forms\Components\Fieldset::make('Personal')->schema([
                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->hint('with ex. Sr. Jr.')
                                    ->maxLength(255)
                                    ->columnSpan(3),

                                TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Select::make('civil_status_code')
                                    ->live()
                                    ->label('Civil Status')
                                    ->required()
                                    ->native(false)
                                    ->options(CivilStatus::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('gender_code')
                                    ->label('Gender')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'Male'=>'Male',
                                        'Female'=>'Female'
                                    ])
                                    ->columnSpan(3),
                                DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->hint(function ($state): string {
                                        if ($state) {
                                            $dateOfBirth = \Carbon\Carbon::parse($state);
                                            $age = $dateOfBirth->age;
                                            return "Age: $age years";
                                        }
                                        return 'Age: 0 year';
                                    })
                                    ->hintColor('primary')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(3),
//

                                Select::make('ownership_code')
                                    ->label('Home Ownersip')
                                    ->options(HomeOwnership::all()->pluck('description','code'))
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(3),
                                TextInput::make('rent_amount')
                                    ->label('Rent Amount')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(fn(Get $get):bool=>$get('ownership_code')==HomeOwnership::where('description','Rented')->first()->code)
                                    ->hidden(fn(Get $get):bool=>$get('ownership_code')!=HomeOwnership::where('description','Rented')->first()->code || $get('ownership_code')==null)
                                    ->columnSpan(3),

                                Group::make()
                                    ->schema([
                                        Forms\Components\ToggleButtons::make('has_pagibig_number')
                                            ->label('Are you a Pag-Ibig Member?')
                                            ->inline(true)
                                            ->required()
                                            ->boolean()
                                            ->live()
                                            ->columnSpan(3),
                                        TextInput::make('mid')
                                            ->label('What is your Pag-Ibig MID #?')
                                            ->required(fn (Get $get): bool=>$get('has_pagibig_number')==true||$get('has_pagibig_number')==null)
                                            ->hidden(fn (Get $get): bool=>$get('has_pagibig_number')!=true||$get('has_pagibig_number')==null)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        Forms\Components\ToggleButtons::make('hloan')
                                            ->label('Do you have an existing housing loan with PAG-IBIG?')
                                            ->inline(true)
                                            ->required()
                                            ->required(fn (Get $get): bool=>$get('has_pagibig_number')==true||$get('has_pagibig_number')==null)
                                            ->hidden(fn (Get $get): bool=>$get('has_pagibig_number')!=true||$get('has_pagibig_number')==null)
                                            ->boolean()
                                            ->columnSpan(3),
                                    ])->columns(12)->columnSpanFull(),

                            ])->columns(12)->columnSpanFull(),
                            \Filament\Forms\Components\Fieldset::make('Contact Information')
                                ->schema([
                                    Forms\Components\TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->unique(ignoreRecord: true,table: Contact::class,column: 'email')
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('mobile')
                                        ->label('Mobile')
                                        ->required()
                                        ->prefix('+63')
                                        ->regex("/^[0-9]+$/")
                                        ->minLength(10)
                                        ->maxLength(10)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->columnSpan(3),

//                                    Forms\Components\TextInput::make('buyer.other_mobile')
//                                        ->label('Other Mobile')
//                                        ->prefix('+63')
//                                        ->regex("/^[0-9]+$/")
//                                        ->minLength(10)
//                                        ->maxLength(10)
//                                        ->live()
//                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
//                                            $livewire->validateOnly($component->getStatePath());
//                                        })
//                                        ->columnSpan(3),
//
//                                    Forms\Components\TextInput::make('buyer.landline')
//                                        ->label('Landline')
//                                        ->columnSpan(3),
                                ])->columns(12)->columnSpanFull(),
                            Forms\Components\Fieldset::make('Employment')
                                ->schema([
                                    Select::make('employment_status')
                                        ->label('Employment Status')
                                        ->required()
                                        ->native(false)
                                        ->options(EmploymentStatus::all()->pluck('description','code'))
                                        ->columnSpan(3),
                                    Select::make('employment_tenure')
                                        ->label('Tenure')
                                        ->required()
                                        ->native(false)
                                        ->options(Tenure::all()->pluck('description','code'))
                                        ->columnSpan(3),
                                    TextInput::make('salary')
                                        ->label('Gross Monthly Income')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->required()
                                        ->columnSpan(3),
                                    TextInput::make('employee_id_number')
                                        ->label('Employee ID Number')
                                        ->required()
                                        ->columnSpan(3),
                                ])->columns(12)->columnSpanFull(),
                        ]
                    )
                    ->columnSpan(5),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Placeholder::make('prospect_id')
                                    ->label('Prospect ID')
                                    ->content(fn($record)=>$record->prospect_id??''),
//                                Placeholder::make('')
//                                    ->label('Preferred Project')
//                                    ->content(fn($record)=>$record->preferredProject->description??'')
//                                    ->hiddenOn('create'),
//                                Placeholder::make('IdImage')
//                                    ->label('Valid Id')
//                                    ->content(fn($record) =>$record->contact==null|| $record->contact->getFirstMediaUrl('id-images') ==''?'No File Found': new HtmlString(
//                                        '<a href="' . $record->contact->getFirstMediaUrl('id-images') . '" target="_blank">View Valid Id</a>'
//                                    ))->hiddenOn('create'),
//                                Placeholder::make('payslipImage')
//                                    ->label('Payslip')
//                                    ->content(fn($record) =>$record->contact==null || $record->contact->getFirstMediaUrl('payslip-images') ==''?'No File Found':  new HtmlString(
//                                        '<a href="' . $record->contact->getFirstMediaUrl('payslip-images') . '" target="_blank">View Payslip</a>'
//                                    ))->hiddenOn('create'),
                                Placeholder::make('created_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                                Placeholder::make('updated_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                            ]),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('10')
            ->defaultPaginationPageOption(50)
            ->extremePaginationLinks()
            ->defaultSort('created_at','desc')
            ->columns([
                Tables\Columns\TextColumn::make('prospect_id')
                    ->label('Prospect ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Name')
                    ->formatStateUsing(function (Model $record){
                        return $record->last_name.' '.$record->name_extension.', '.$record->first_name.' '.$record->middle_name;
                    })
                    ->searchable(['last_name','first_name','middle_name','name_extension']),
                Tables\Columns\TextColumn::make('company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('salary')
                    ->formatStateUsing(fn (string $state): string => '₱' . number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('mid')
                    ->label('PAGIBIG MID')
                    ->searchable(),
                Tables\Columns\IconColumn::make('hloan')
                    ->boolean()
                    ->label('Exsiting Loan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspects::route('/'),
            'create' => Pages\CreateProspects::route('/create'),
            'edit' => Pages\EditProspects::route('/{record}/edit'),
        ];
    }
}
