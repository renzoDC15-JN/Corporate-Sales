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
                Forms\Components\Tabs::make()
                    ->persistTabInQueryString()
                    ->schema([
                        Forms\Components\Tabs\Tab::make('Prospect')
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
//
                            ])->columns(12),
                        Forms\Components\Tabs\Tab::make('Client Information Sheet')
                            ->schema([
                                Forms\Components\Tabs::make()
                                    ->persistTabInQueryString()
                                ->schema([
                                    Forms\Components\Tabs\Tab::make('Personal Information')->schema([
                                        //Personal Information
                                        Forms\Components\Fieldset::make('Personal')->schema([
                                            TextInput::make('contact.last_name')
                                                ->label('Last Name')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('contact.first_name')
                                                ->label('First Name')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),

                                            TextInput::make('contact.middle_name')
                                                ->label('Middle Name')
                                                ->maxLength(255)
//                                                ->required(fn (Get $get): bool => ! $get('no_middle_name'))
//                                                ->readOnly(fn (Get $get): bool => $get('no_middle_name'))
//                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                                ->columnSpan(3),
                                            Select::make('contact.name_suffix')
                                                ->label('Suffix')
                                                ->required()
                                                ->native(false)
                                                ->options(NameSuffix::all()->pluck('description','code'))
                                                ->columnSpan(3),
//                                            Forms\Components\Checkbox::make('no_middle_name')
//                                                ->live()
//                                                ->inline(false)
//                                                ->afterStateUpdated(function(Get $get,Set $set){
//                                                    $set('buyer.middle_name',null);
////                                                if ($get('no_middle_name')) {
////                                                }
//                                                })
//                                                ->columnSpan(1),
                                            Select::make('contact.civil_status')
                                                ->live()
                                                ->label('Civil Status')
                                                ->required()
                                                ->native(false)
                                                ->options(CivilStatus::all()->pluck('description','code'))
                                                ->columnSpan(3),
                                            Select::make('contact.sex')
                                                ->label('Gender')
                                                ->required()
                                                ->native(false)
                                                ->options([
                                                    'Male'=>'Male',
                                                    'Female'=>'Female'
                                                ])
                                                ->columnSpan(3),
                                            DatePicker::make('contact.date_of_birth')
                                                ->label('Date of Birth')
                                                ->required()
                                                ->native(false)
                                                ->columnSpan(3),
                                            Select::make('contact.nationality')
                                                ->label('Nationality')
                                                ->required()
                                                ->native(false)
                                                ->options(Nationality::all()->pluck('description','code'))
                                                ->columnSpan(3),
                                        ])->columns(12)->columnSpanFull(),
                                        \Filament\Forms\Components\Fieldset::make('Contact Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('contact.email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $livewire->validateOnly($component->getStatePath());
                                                    })
//                                                    ->unique(ignoreRecord: true,table: Contact::class,column: 'email')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('contact.mobile')
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

                                                Forms\Components\TextInput::make('contact.other_mobile')
                                                    ->label('Other Mobile')
                                                    ->prefix('+63')
                                                    ->regex("/^[0-9]+$/")
                                                    ->minLength(10)
                                                    ->maxLength(10)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $livewire->validateOnly($component->getStatePath());
                                                    })
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('contact.landline')
                                                    ->label('Landline')
                                                    ->columnSpan(3),
                                            ])->columns(12)->columnSpanFull(),
                                        //Address
                                        \Filament\Forms\Components\Fieldset::make('Address')
                                            ->schema([
                                                Forms\Components\Fieldset::make('Present')->schema([
                                                    Select::make('buyer_address_present.ownership')
                                                        ->options(HomeOwnership::all()->pluck('description','code'))
                                                        ->native(false)
                                                        ->required()
                                                        ->columnSpan(3),
                                                    Select::make('buyer_address_present.country')
                                                        ->searchable()
                                                        ->options(Country::all()->pluck('description','code'))
                                                        ->native(false)
                                                        ->live()
                                                        ->required()
                                                        ->columnSpan(3),
                                                    TextInput::make('buyer_address_present.postal_code')
                                                        ->minLength(4)
                                                        ->maxLength(4)
                                                        ->required()
                                                        ->columnSpan(3),
                                                    Checkbox::make('buyer_address_present.same_as_permanent')
                                                        ->label('Same as Permanent')
                                                        ->inline(false)
                                                        ->live()
                                                        ->columnSpan(3),
                                                    Select::make('buyer_address_present.region')
                                                        ->label('Region')
                                                        ->searchable()
                                                        ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                                        ->required(fn(Get $get):bool => $get('buyer_address_present.country') == 'PH')
                                                        ->hidden(fn(Get $get):bool => $get('buyer_address_present.country') != 'PH'&&$get('buyer_address_present.country')!=null)
                                                        ->native(false)
                                                        ->live()
                                                        ->afterStateUpdated(function (Set $set, $state) {
                                                            $set('buyer_address_present.administrative_area', '');
                                                            $set('buyer_address_present.locality', '');
                                                            $set('buyer_address_present.sublocality', '');
                                                        })
                                                        ->columnSpan(3),
                                                    Select::make('buyer_address_present.administrative_area')
                                                        ->label('Province')
                                                        ->searchable()
                                                        ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                            ->where('region_code', $get('buyer_address_present.administrative_area'))
                                                            ->pluck('province_description', 'province_code'))
                                                        ->required(fn(Get $get):bool => $get('buyer_address_present.country') == 'PH')
                                                        ->hidden(fn(Get $get):bool => $get('buyer_address_present.country') != 'PH'&&$get('buyer_address_present.country')!=null)
                                                        ->native(false)
                                                        ->live()
                                                        ->afterStateUpdated(function (Set $set, $state) {
                                                            $set('buyer_address_present.locality', '');
                                                            $set('buyer_address_present.sublocality', '');
                                                        })
                                                        ->columnSpan(3),
                                                    Select::make('buyer_address_present.locality')
                                                        ->label('City')
                                                        ->searchable()
                                                        ->required(fn(Get $get):bool => $get('buyer_address_present.country') == 'PH')
                                                        ->hidden(fn(Get $get):bool => $get('buyer_address_present.country') != 'PH'&&$get('buyer_address_present.country')!=null)
                                                        ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                            ->where('province_code', $get('buyer_address_present.administrative_area'))
                                                            ->pluck('city_municipality_description', 'city_municipality_code'))
                                                        ->native(false)
                                                        ->live()
                                                        ->afterStateUpdated(function (Set $set, $state) {
                                                            $set('buyer_address_present.sublocality', '');
                                                        })
                                                        ->columnSpan(3),
                                                    Select::make('buyer_address_present.sublocality')
                                                        ->label('Barangay')
                                                        ->searchable()
                                                        ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                            ->where('region_code', $get('buyer_address_present.administrative_area'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                            ->where('city_municipality_code', $get('buyer_address_present.locality'))
                                                            ->pluck('barangay_description', 'barangay_code')
                                                        )
                                                        ->required(fn(Get $get):bool => $get('buyer_address_present.country') == 'PH')
                                                        ->hidden(fn(Get $get):bool => $get('buyer_address_present.country') != 'PH'&&$get('buyer_address_present.country')!=null)
                                                        ->native(false)
                                                        ->live()
                                                        ->columnSpan(3),
                                                    TextInput::make('buyer_address_present.address1')
                                                        ->label('Address')
                                                        ->label(fn(Get $get)=>$get('buyer_address_present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
//                                        ->hint('Unit Number, House/Building/Street No, Street Name')
                                                        ->placeholder(fn(Get $get)=>$get('buyer_address_present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                        ->required(fn(Get $get):bool => $get('buyer_address_present.country') != 'PH')
                                                        ->autocapitalize('words')
                                                        ->maxLength(255)
                                                        ->live()
                                                        ->columnSpan(12),
                                                    Placeholder::make('buyer_address_present.full_address')
                                                        ->label('Full Address')
                                                        ->live()
                                                        ->content(function (Get $get): string {
                                                            $region = PhilippineRegion::where('region_code', $get('buyer_address_present.region'))->first();
                                                            $province = PhilippineProvince::where('province_code', $get('buyer_address_present.administrative_area'))->first();
                                                            $city = PhilippineCity::where('city_municipality_code', $get('buyer_address_present.city'))->first();
                                                            $barangay = PhilippineBarangay::where('barangay_code', $get('buyer_address_present.locality'))->first();
                                                            $address = $get('buyer_address_present.address1');
                                                            $addressParts = array_filter([
                                                                $address,
                                                                $barangay != null ? $barangay->barangay_description : '',
                                                                $city != null ? $city->city_municipality_description : '',
                                                                $province != null ? $province->province_description : '',
                                                                $region != null ? $region->region_description : '',
                                                            ]);
                                                            return implode(', ', $addressParts);
                                                        })->columnSpanFull()


                                                ])->columns(12)->columnSpanFull(),
                                                Group::make()->schema(
                                                    fn(Get $get) => $get('buyer_address_present.same_as_permanent') == null ? [
                                                        Forms\Components\Fieldset::make('Permanent')->schema([
                                                            Group::make()->schema([
                                                                Select::make('buyer_address_permanent.ownership')
                                                                    ->options(HomeOwnership::all()->pluck('description','code'))
                                                                    ->native(false)
                                                                    ->required()
                                                                    ->columnSpan(3),
                                                                Select::make('buyer_address_permanent.country')
                                                                    ->searchable()
                                                                    ->options(Country::all()->pluck('description','code'))
                                                                    ->native(false)
                                                                    ->live()
                                                                    ->required()
                                                                    ->columnSpan(3),
                                                                TextInput::make('buyer_address_permanent.postal_code')
                                                                    ->minLength(4)
                                                                    ->maxLength(4)
                                                                    ->required()
                                                                    ->columnSpan(3),
                                                            ])
                                                                ->columns(12)->columnSpanFull(),


                                                            Select::make('buyer_address_permanent.region')
                                                                ->searchable()
                                                                ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                                                ->required(fn(Get $get):bool => $get('buyer_address_permanent.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('buyer_address_permanent.country') != 'PH'&&$get('buyer_address_permanent.country')!=null)
                                                                ->native(false)
                                                                ->live()
                                                                ->afterStateUpdated(function (Set $set, $state) {
                                                                    $set('buyer_address_permanent.administrative_area', '');
                                                                    $set('buyer_address_permanent.locality', '');
                                                                    $set('buyer_address_permanent.sublocality', '');
                                                                })
                                                                ->columnSpan(3),
                                                            Select::make('buyer_address_permanent.administrative_area')
                                                                ->searchable()
                                                                ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                                    ->where('region_code', $get('buyer_address_permanent.region'))
                                                                    ->pluck('province_description', 'province_code'))
                                                                ->required(fn(Get $get):bool => $get('buyer_address_permanent.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('buyer_address_permanent.country') != 'PH'&&$get('buyer_address_permanent.country')!=null)
                                                                ->native(false)
                                                                ->live()
                                                                ->afterStateUpdated(function (Set $set, $state) {
                                                                    $set('buyer_address_permanent.locality', '');
                                                                    $set('buyer_address_permanent.sublocality', '');
                                                                })
                                                                ->columnSpan(3),
                                                            Select::make('$buyer_address_permanent.locality')
                                                                ->label('City')
                                                                ->searchable()
                                                                ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                                    ->where('province_code', $get('$buyer_address_permanent.administrative_area'))
                                                                    ->pluck('city_municipality_description', 'city_municipality_code'))
                                                                ->required(fn(Get $get):bool => $get('buyer_address_permanent.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('buyer_address_permanent.country') != 'PH'&&$get('buyer_address_permanent.country')!=null)
                                                                ->native(false)
                                                                ->live()
                                                                ->afterStateUpdated(function (Set $set, $state) {
                                                                    $set('buyer_address_permanent.sublocality', '');
                                                                })
                                                                ->columnSpan(3),
                                                            Select::make('buyer_address_permanent.sublocality')
                                                                ->label('Barangay')
                                                                ->searchable()
                                                                ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                                    ->where('region_code', $get('buyer_address_permanent.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                                    ->where('city_municipality_code', $get('buyer_address_permanent.locality'))
                                                                    ->pluck('barangay_description', 'barangay_code')
                                                                )
                                                                ->required(fn(Get $get):bool => $get('buyer_address_permanent.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('buyer_address_permanent.country') != 'PH'&&$get('buyer_address_permanent.country')!=null)
                                                                ->native(false)
                                                                ->live()
                                                                ->columnSpan(3),
                                                            TextInput::make('buyer_address_permanent.address')
                                                                ->label(fn(Get $get)=>$get('buyer_address_permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                                ->placeholder(fn(Get $get)=>$get('$buyer_address_permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                                ->required(fn(Get $get):bool => $get('buyer_address_permanent.country') != 'PH')
                                                                ->autocapitalize('words')
                                                                ->maxLength(255)
                                                                ->live()
                                                                ->columnSpan(12),
                                                            Placeholder::make('buyer_address_permanent.full_address')
                                                                ->label('Full Address')
                                                                ->live()
                                                                ->content(function (Get $get): string {
                                                                    $region = PhilippineRegion::where('region_code', $get('buyer_address_permanent.region'))->first();
                                                                    $province = PhilippineProvince::where('province_code', $get('buyer_address_permanent.administrative_area'))->first();
                                                                    $city = PhilippineCity::where('city_municipality_code', $get('buyer_address_permanent.locality'))->first();
                                                                    $barangay = PhilippineBarangay::where('barangay_code', $get('buyer_address_permanent.sublocality'))->first();
                                                                    $address = $get('buyer_address_permanent.address');
                                                                    $addressParts = array_filter([
                                                                        $address,
                                                                        $barangay != null ? $barangay->barangay_description : '',
                                                                        $city != null ? $city->city_municipality_description : '',
                                                                        $province != null ? $province->province_description : '',
                                                                        $region != null ? $region->region_description : '',
                                                                    ]);
                                                                    return implode(', ', $addressParts);
                                                                })->columnSpan(12),


                                                        ])->columns(12)->columnSpanFull(),
                                                    ] : []
                                                )->columns(12)->columnSpanFull(),
                                            ])->columns(12)->columnSpanFull(),
                                        //Employment
                                        \Filament\Forms\Components\Fieldset::make('Employment')->schema([
                                            Select::make('buyer_employment.employment_type')
                                                ->label('Employment Type')
                                                ->live()
                                                ->required()
                                                ->native(false)
                                                ->options(EmploymentType::all()->pluck('description','code'))
                                                ->columnSpan(3),
                                            Select::make('buyer_employment.employment_status')
                                                ->label('Employment Status')
                                                ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->native(false)
                                                ->options(EmploymentStatus::all()->pluck('description','code'))
                                                ->columnSpan(3),
                                            Select::make('buyer_employment.years_in_service')
                                                ->label('Tenure')
                                                ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->native(false)
                                                ->options(Tenure::all()->pluck('description','code'))
                                                ->columnSpan(3),
                                            Select::make('buyer_employment.current_position')
                                                ->label('Current Position')
                                                ->native(false)
                                                ->options(CurrentPosition::all()->pluck('description','code'))
                                                ->searchable()
                                                ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.rank')
                                                ->label('Rank')
                                                ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            Select::make('buyer_employment.employer.industry')
                                                ->label('Work Industry')
                                                ->required()
                                                ->native(false)
                                                ->options(WorkIndustry::all()->pluck('description','code'))
                                                ->searchable()
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.monthly_gross_income')
                                                ->label('Gross Monthly Income')
                                                ->numeric()
                                                ->prefix('PHP')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            Group::make()->schema([
                                                TextInput::make('buyer_employment.id.tin')
                                                    ->label('Tax Identification Number')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
                                                TextInput::make('buyer_employment.id.pagibig')
                                                    ->label('PAG-IBIG Number')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
                                                TextInput::make('buyer_employment.id.sss')
                                                    ->label('SSS/GSIS Number')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
                                            ])->columnSpanFull()->columns(12),


                                        ])->columns(12)->columnSpanFull(),
                                        //Employer
                                        Forms\Components\Fieldset::make('Employer/Business')->schema([
                                            TextInput::make('buyer_employment.employer.name')
                                                ->label('Employer / Business Name')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.contact_person')
                                                ->label('Contact Person')
                                                ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.email')
                                                ->label('Email')
                                                ->email()
                                                ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.contact_no')
                                                ->label('Contact Number')
                                                ->required(fn (Get $get): bool =>   $get('buyer_employment.employment_type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.employment_type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                ->prefix('+63')
                                                ->regex("/^[0-9]+$/")
                                                ->minLength(10)
                                                ->maxLength(10)
                                                ->live()
//                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
////                                            $livewire->validateOnly($component->getStatePath());
//                                        })
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.year_established')
                                                ->label('Year Established')
                                                ->required()
                                                ->numeric()
                                                ->columnSpan(3),
//                                        Select::make('employment.employer.years_of_operation')
//                                            ->label('Years of Operation')
//                                            ->required()
//                                            ->native(false)
//                                            ->options(YearsOfOperation::all()->pluck('description','code'))
//                                            ->columnSpan(3),
                                            Forms\Components\Fieldset::make('Address')->schema([
                                                Group::make()
                                                    ->schema([
                                                        Select::make('buyer_employment.employer.address.country')
                                                            ->searchable()
                                                            ->options(Country::all()->pluck('description','code'))
                                                            ->native(false)
                                                            ->live()
                                                            ->required()
                                                            ->columnSpan(3),
                                                    ])
                                                    ->columns(12)
                                                    ->columnSpanFull(),
                                                Select::make('buyer_employment.employer.address.region')
                                                    ->searchable()
                                                    ->options(PhilippineRegion::all()->pluck('region_description','region_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function(Set $set, $state){
                                                        $set('buyer_employment.employer.address.administrative_area','');
                                                        $set('buyer_employment.employer.address.city','');
                                                        $set('buyer_employment.employer.address.barangay','');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer_employment.employer.address.administrative_area')
                                                    ->label('Province')
                                                    ->searchable()
                                                    ->options(fn (Get $get): Collection => PhilippineProvince::query()
                                                        ->where('region_code', $get('buyer_employment.employer.address.region'))
                                                        ->pluck('province_description', 'province_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function(Set $set, $state){
                                                        $set('buyer_employment.employer.address.locality','');
                                                        $set('buyer_employment.employer.address.sublocality','');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer_employment.employer.address.locality')
                                                    ->label('locality')
                                                    ->searchable()
                                                    ->options(fn (Get $get): Collection => PhilippineCity::query()
                                                        ->where('province_code', $get('buyer_employment.employer.address.administrative_area'))
                                                        ->pluck('city_municipality_description', 'city_municipality_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function(Set $set, $state){
                                                        $set('buyer_employment.employer.address.sublocality','');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer_employment.employer.address.sublocality')
                                                    ->label('sublocality')
                                                    ->searchable()
                                                    ->options(fn (Get $get): Collection =>PhilippineBarangay::query()
                                                        ->where('region_code', $get('buyer_employment.employer.address.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                        ->where('city_municipality_code', $get('buyer_employment.employer.address.locality'))
                                                        ->pluck('barangay_description', 'barangay_code')
                                                    )
                                                    ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->columnSpan(3),
                                                TextInput::make('buyer_employment.employer.address.address1')
                                                    ->label('Address')
                                                    ->label(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                    ->placeholder(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                    ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH')
                                                    ->autocapitalize('words')
                                                    ->maxLength(255)
                                                    ->live()
                                                    ->columnSpan(12),


                                            ])->columns(12)->columnSpanFull(),
                                        ])->columns(12)->columnSpanFull(),
//                                        Forms\Components\Fieldset::make('Documents')
//                                            ->schema([
//                                                SpatieMediaLibraryFileUpload::make('idImage')
//                                                    ->label('Valid ID')
//                                                    ->openable()
//                                                    ->downloadable()
//                                                    ->collection('id-images')
//                                                    ->required(),
//                                                SpatieMediaLibraryFileUpload::make('payslipImage')
//                                                    ->label('Latest 1 month payslip')
//                                                    ->openable()
//                                                    ->downloadable()
//                                                    ->collection('payslip-images')
//                                                    ->required(),
//                                                FileUpload::make('idImage')
//                                                    ->label('Valid ID')
//                                                    ->image()
//                                                    ->openable()
//                                                    ->downloadable()
//                                                    ->required(),
//                                                FileUpload::make('payslipImage')
//                                                    ->label('Latest 1 month payslip')
//                                                    ->image()
//                                                    ->openable()
//                                                    ->downloadable()
//                                                    ->required(),
//                                            ])->columns(1)
                                    ]),
                                    //Spouse
                                    Forms\Components\Tabs\Tab::make('Spouse')->schema([
                                        //Personal Information
                                        Forms\Components\Fieldset::make('Personal')->schema([
                                            TextInput::make('spouse.last_name')
                                                ->label('Last Name')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('spouse.first_name')
                                                ->label('First Name')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),

                                            TextInput::make('spouse.middle_name')
                                                ->label('Middle Name')
                                                ->maxLength(255)
                                                ->required(fn (Get $get): bool => ! $get('spouse.no_middle_name'))
                                                ->readOnly(fn (Get $get): bool =>  $get('spouse.no_middle_name'))
//                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                                ->columnSpan(3),
                                            Select::make('spouse.name_suffix')
                                                ->label('Suffix')
                                                ->required()
                                                ->native(false)
                                                ->options(NameSuffix::all()->pluck('description','code'))
                                                ->columnSpan(2),

                                            Forms\Components\Checkbox::make('spouse.no_middle_name')
                                                ->live()
                                                ->inline(false)
                                                ->afterStateUpdated(function(Get $get,Set $set){
                                                    $set('spouse.middle_name',null);
//                                                if ($get('no_middle_name')) {
//                                                }
                                                })
                                                ->columnSpan(1),
                                            Select::make('spouse.civil_status')
                                                ->label('Civil Status')
                                                ->required()
                                                ->native(false)
                                                ->options(CivilStatus::all()->pluck('description','code'))
                                                ->columnSpan(3),
                                            Select::make('spouse.gender')
                                                ->label('Gender')
                                                ->required()
                                                ->native(false)
                                                ->options([
                                                    'Male'=>'Male',
                                                    'Female'=>'Female'
                                                ])
                                                ->columnSpan(3),
                                            DatePicker::make('spouse.date_of_birth')
                                                ->label('Date of Birth')
                                                ->required()
                                                ->native(false)
                                                ->columnSpan(3),
                                            Select::make('spouse.nationality')
                                                ->label('Nationality')
                                                ->required()
                                                ->native(false)
                                                ->options(Nationality::all()->pluck('description','code'))
                                                ->columnSpan(3),
                                        ])->columns(12)->columnSpanFull(),
                                        \Filament\Forms\Components\Fieldset::make('Contact Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('spouse.email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $livewire->validateOnly($component->getStatePath());
                                                    })
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('spouse.mobile')
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

                                                Forms\Components\TextInput::make('spouse.other_mobile')
                                                    ->label('Other Mobile')
                                                    ->prefix('+63')
                                                    ->regex("/^[0-9]+$/")
                                                    ->minLength(10)
                                                    ->maxLength(10)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $livewire->validateOnly($component->getStatePath());
                                                    })
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('spouse.landline')
                                                    ->label('Landline')
                                                    ->columnSpan(3),
                                            ])->columns(12)->columnSpanFull(),
                                    ])->hidden(fn (Get $get): bool => $get('contact.civil_status')!=CivilStatus::where('description','Married')->first()->code &&  $get('contact.civil_status')!=null),
                                ])
                            ])->visibleOn('edit'),
                    ])
                    ->columnSpan(5),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Placeholder::make('prospect_id')
                                    ->label('Prospect ID')
                                    ->content(fn($record)=>$record->prospect_id??''),
                                Placeholder::make('IdImage')
                                    ->label('Valid Id')
                                    ->content(fn($record) =>$record->contact==null|| $record->contact->getFirstMediaUrl('id-images') ==''?'No File Found': new HtmlString(
                                        '<a href="' . $record->contact->getFirstMediaUrl('id-images') . '" target="_blank">View Valid Id</a>'
                                    )),
                                Placeholder::make('payslipImage')
                                    ->label('Payslip')
                                    ->content(fn($record) =>$record->contact==null || $record->contact->getFirstMediaUrl('payslip-images') ==''?'No File Found':  new HtmlString(
                                        '<a href="' . $record->contact->getFirstMediaUrl('payslip-images') . '" target="_blank">View Payslip</a>'
                                    )),
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
