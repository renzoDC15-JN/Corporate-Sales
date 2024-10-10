<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectsResource\Pages;
use App\Filament\Resources\ProspectsResource\RelationManagers;
use App\Models\Prospects;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->label('First Name')
                        ->maxLength(255)
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('middle_name')
                        ->label('Middle Name')
                        ->maxLength(255)
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('last_name')
                        ->label('Last Name')
                        ->maxLength(255)
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('name_extension')
                        ->label('Extension Name')
                        ->maxLength(255)
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('company')
                        ->maxLength(255)
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('position_title')
                        ->label('Position/Title')
                        ->maxLength(255)
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('salary')
                        ->numeric()
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('mid')
                        ->label('MID')
                        ->maxLength(255)
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\ToggleButtons::make('hloan')
                        ->label('HLOAN')
                        ->boolean()
                        ->inline(true)
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->live()
                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                            $livewire->validateOnly($component->getStatePath());
                        })
                        ->unique(ignoreRecord: true,table: Prospects::class,column: 'email')
                        ->required()
                        ->columnSpan(3),
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
                        ->required()
                        ->columnSpan(3),
                    Select::make('preferred_project')
                        ->label('Preferred Project')
                        ->native(false)
                        ->relationship('preferredProject', 'description') // 'description' can be the display field
                        ->columnSpan(3)
                        ->required(),
                ])->columns(12)->columnSpan(2),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Placeholder::make('prospect_id')
                                    ->label('Prospect ID')
                                    ->content(fn($record)=>$record->prospect_id),
                                Placeholder::make('created_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                                Placeholder::make('updated_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                            ]),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
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
