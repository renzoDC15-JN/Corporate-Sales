<?php

namespace App\Livewire\Prospects;

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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Homeful\Contacts\Actions\PersistContactAction;
use Homeful\Contacts\Models\Contact;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ClientInformationSheet extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Prospects $record;
    public string $screenSize=''; // Default value is 'desktop'
    public bool $has_data = false;

    public function mount(Prospects $prospect): void
    {
        $this->record = $prospect;
        if(!empty($this->record->contact_id)){
            $this->has_data=true;
//            $this->dispatch('open-modal', id: 'hasdata-modal');
        }
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->live()
            ->reactive()
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Information')->schema([
                            //Personal Information
                            Forms\Components\Fieldset::make('Personal')->schema([
                                TextInput::make('buyer.last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                TextInput::make('buyer.first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),

                                TextInput::make('buyer.middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(255)
                                    ->required(fn (Get $get): bool => ! $get('no_middle_name'))
                                    ->readOnly(fn (Get $get): bool => $get('no_middle_name'))
//                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                    ->columnSpan(3),
                                Select::make('buyer.name_suffix')
                                    ->label('Suffix')
                                    ->required()
                                    ->native(false)
                                    ->options(NameSuffix::all()->pluck('description','code'))
                                    ->columnSpan(2),
                                Forms\Components\Checkbox::make('no_middle_name')
                                    ->live()
                                    ->inline(false)
                                    ->afterStateUpdated(function(Get $get,Set $set){
                                        $set('buyer.middle_name',null);
//                                                if ($get('no_middle_name')) {
//                                                }
                                    })
                                    ->columnSpan(1),
                                Select::make('buyer.civil_status')
                                    ->live()
                                    ->label('Civil Status')
                                    ->required()
                                    ->native(false)
                                    ->options(CivilStatus::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer.gender')
                                    ->label('Gender')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'Male'=>'Male',
                                        'Female'=>'Female'
                                    ])
                                    ->columnSpan(3),
                                DatePicker::make('buyer.date_of_birth')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(3),
                                Select::make('buyer.nationality')
                                    ->label('Nationality')
                                    ->required()
                                    ->native(false)
                                    ->options(Nationality::all()->pluck('description','code'))
                                    ->columnSpan(3),
                            ])->columns(12)->columnSpanFull(),
                            \Filament\Forms\Components\Fieldset::make('Contact Information')
                                ->schema([
                                    Forms\Components\TextInput::make('buyer.email')
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

                                    Forms\Components\TextInput::make('buyer.mobile')
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

                                    Forms\Components\TextInput::make('buyer.other_mobile')
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

                                    Forms\Components\TextInput::make('buyer.landline')
                                        ->label('Landline')
                                        ->columnSpan(3),
                                ])->columns(12)->columnSpanFull(),
                            //Address
                            \Filament\Forms\Components\Fieldset::make('Address')
                                ->schema([
                                    Forms\Components\Fieldset::make('Present')->schema([
                                        Select::make('buyer.address.present.ownership')
                                            ->options(HomeOwnership::all()->pluck('description','code'))
                                            ->native(false)
                                            ->required()
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.country')
                                            ->searchable()
                                            ->options(Country::all()->pluck('description','code'))
                                            ->native(false)
                                            ->live()
                                            ->required()
                                            ->columnSpan(3),
                                        TextInput::make('buyer.address.present.postal_code')
                                            ->minLength(4)
                                            ->maxLength(4)
                                            ->required()
                                            ->columnSpan(3),
                                        Checkbox::make('buyer.address.present.same_as_permanent')
                                            ->label('Same as Permanent')
                                            ->inline(false)
                                            ->live()
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.region')
                                            ->searchable()
                                            ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('buyer.address.present.province', '');
                                                $set('buyer.address.present.city', '');
                                                $set('buyer.address.present.barangay', '');
                                            })
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.province')
                                            ->searchable()
                                            ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                ->where('region_code', $get('buyer.address.present.region'))
                                                ->pluck('province_description', 'province_code'))
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('buyer.address.present.city', '');
                                                $set('buyer.address.present.barangay', '');
                                            })
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.city')
                                            ->searchable()
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                ->where('province_code', $get('buyer.address.present.province'))
                                                ->pluck('city_municipality_description', 'city_municipality_code'))
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('buyer.address.present.barangay', '');
                                            })
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.barangay')
                                            ->searchable()
                                            ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                ->where('region_code', $get('buyer.address.present.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                ->where('city_municipality_code', $get('buyer.address.present.city'))
                                                ->pluck('barangay_description', 'barangay_code')
                                            )
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->native(false)
                                            ->live()
                                            ->columnSpan(3),
                                        TextInput::make('buyer.address.present.address')
                                            ->label(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
//                                        ->hint('Unit Number, House/Building/Street No, Street Name')
                                            ->placeholder(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH')
                                            ->autocapitalize('words')
                                            ->maxLength(255)
                                            ->live()
                                            ->columnSpan(12),
                                        Placeholder::make('buyer.address.present.full_address')
                                            ->label('Full Address')
                                            ->live()
                                            ->content(function (Get $get): string {
                                                $region = PhilippineRegion::where('region_code', $get('buyer.address.present.region'))->first();
                                                $province = PhilippineProvince::where('province_code', $get('buyer.address.present.province'))->first();
                                                $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.present.city'))->first();
                                                $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.present.barangay'))->first();
                                                $address = $get('buyer.address.present.address');
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
                                        fn(Get $get) => $get('buyer.address.present.same_as_permanent') == null ? [
                                            Forms\Components\Fieldset::make('Permanent')->schema([
                                                Group::make()->schema([
                                                    Select::make('buyer.address.permanent.ownership')
                                                        ->options(HomeOwnership::all()->pluck('description','code'))
                                                        ->native(false)
                                                        ->required()
                                                        ->columnSpan(3),
                                                    Select::make('buyer.address.permanent.country')
                                                        ->searchable()
                                                        ->options(Country::all()->pluck('description','code'))
                                                        ->native(false)
                                                        ->live()
                                                        ->required()
                                                        ->columnSpan(3),
                                                    TextInput::make('buyer.address.permanent.postal_code')
                                                        ->minLength(4)
                                                        ->maxLength(4)
                                                        ->required()
                                                        ->columnSpan(3),
                                                ])
                                                    ->columns(12)->columnSpanFull(),


                                                Select::make('buyer.address.permanent.region')
                                                    ->searchable()
                                                    ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        $set('buyer.address.permanent.province', '');
                                                        $set('buyer.address.permanent.city', '');
                                                        $set('buyer.address.permanent.barangay', '');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer.address.permanent.province')
                                                    ->searchable()
                                                    ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                        ->where('region_code', $get('buyer.address.permanent.region'))
                                                        ->pluck('province_description', 'province_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        $set('buyer.address.permanent.city', '');
                                                        $set('buyer.address.permanent.barangay', '');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer.address.permanent.city')
                                                    ->searchable()
                                                    ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                        ->where('province_code', $get('buyer.address.permanent.province'))
                                                        ->pluck('city_municipality_description', 'city_municipality_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        $set('buyer.address.permanent.barangay', '');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer.address.permanent.barangay')
                                                    ->searchable()
                                                    ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                        ->where('region_code', $get('buyer.address.permanent.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                        ->where('city_municipality_code', $get('buyer.address.permanent.city'))
                                                        ->pluck('barangay_description', 'barangay_code')
                                                    )
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->columnSpan(3),
                                                TextInput::make('buyer.address.permanent.address')
                                                    ->label(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                    ->placeholder(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH')
                                                    ->autocapitalize('words')
                                                    ->maxLength(255)
                                                    ->live()
                                                    ->columnSpan(12),
                                                Placeholder::make('buyer.address.permanent.full_address')
                                                    ->label('Full Address')
                                                    ->live()
                                                    ->content(function (Get $get): string {
                                                        $region = PhilippineRegion::where('region_code', $get('buyer.address.permanent.region'))->first();
                                                        $province = PhilippineProvince::where('province_code', $get('buyer.address.permanent.province'))->first();
                                                        $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.permanent.city'))->first();
                                                        $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.permanent.barangay'))->first();
                                                        $address = $get('buyer.address.permanent.address');
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
                                Select::make('buyer_employment.type')
                                    ->label('Employment Type')
                                    ->live()
                                    ->required()
                                    ->native(false)
                                    ->options(EmploymentType::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer_employment.status')
                                    ->label('Employment Status')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->native(false)
                                    ->options(EmploymentStatus::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer_employment.tenure')
                                    ->label('Tenure')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->native(false)
                                    ->options(Tenure::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer_employment.position')
                                    ->label('Current Position')
                                    ->native(false)
                                    ->options(CurrentPosition::all()->pluck('description','code'))
                                    ->searchable()
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.rank')
                                    ->label('Rank')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Select::make('buyer_employment.work_industry')
                                    ->label('Work Industry')
                                    ->required()
                                    ->native(false)
                                    ->options(WorkIndustry::all()->pluck('description','code'))
                                    ->searchable()
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.gross_monthly_income')
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
                                    TextInput::make('buyer_employment.id.pag_ibig')
                                        ->label('PAG-IBIG Number')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(3),
                                    TextInput::make('buyer_employment.id.sss_gsis')
                                        ->label('SSS/GSIS Number')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(3),
                                ])->columnSpanFull()->columns(12),


                            ])->columns(12)->columnSpanFull(),
                            //Employer
                            Forms\Components\Fieldset::make('Employer/Business')->schema([
                                TextInput::make('buyer_employment.employer.employer_business_name')
                                    ->label('Employer / Business Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.employer.contact_person')
                                    ->label('Contact Person')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.employer.employer_email')
                                    ->label('Email')
                                    ->email()
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.employer.mobile')
                                    ->label('Contact Number')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
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
                                            $set('buyer_employment.employer.address.province','');
                                            $set('buyer_employment.employer.address.city','');
                                            $set('buyer_employment.employer.address.barangay','');
                                        })
                                        ->columnSpan(3),
                                    Select::make('buyer_employment.employer.address.province')
                                        ->searchable()
                                        ->options(fn (Get $get): Collection => PhilippineProvince::query()
                                            ->where('region_code', $get('buyer_employment.employer.address.region'))
                                            ->pluck('province_description', 'province_code'))
                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                        ->native(false)
                                        ->live()
                                        ->afterStateUpdated(function(Set $set, $state){
                                            $set('buyer_employment.employer.address.city','');
                                            $set('buyer_employment.employer.address.barangay','');
                                        })
                                        ->columnSpan(3),
                                    Select::make('buyer_employment.employer.address.city')
                                        ->searchable()
                                        ->options(fn (Get $get): Collection => PhilippineCity::query()
                                            ->where('province_code', $get('buyer_employment.employer.address.province'))
                                            ->pluck('city_municipality_description', 'city_municipality_code'))
                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                        ->native(false)
                                        ->live()
                                        ->afterStateUpdated(function(Set $set, $state){
                                            $set('buyer_employment.employer.address.barangay','');
                                        })
                                        ->columnSpan(3),
                                    Select::make('buyer_employment.employer.address.barangay')
                                        ->searchable()
                                        ->options(fn (Get $get): Collection =>PhilippineBarangay::query()
                                            ->where('region_code', $get('buyer_employment.employer.address.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                            ->where('city_municipality_code', $get('buyer_employment.employer.address.city'))
                                            ->pluck('barangay_description', 'barangay_code')
                                        )
                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                        ->native(false)
                                        ->live()
                                        ->columnSpan(3),
                                    TextInput::make('buyer_employment.employer.address.address')
                                        ->label(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                        ->placeholder(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH')
                                        ->autocapitalize('words')
                                        ->maxLength(255)
                                        ->live()
                                        ->columnSpan(12),


                                ])->columns(12)->columnSpanFull(),
                            ])->columns(12)->columnSpanFull(),
                            Forms\Components\Fieldset::make('Documents')
                                ->schema([
                                    FileUpload::make('valid_id')
                                        ->label('Valid ID')
                                        ->required(),
                                    FileUpload::make('payslip')
                                        ->label('Latest 1 month payslip')
                                        ->required(),
                                ])->columns(1)
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
                        ])->hidden(fn (Get $get): bool => $get('buyer.civil_status')!=CivilStatus::where('description','Married')->first()->code &&  $get('buyer.civil_status')!=null),
                    ])->visible(fn():bool => $this->screenSize === 'desktop' || $this->screenSize === 'md'),
                    //Mobile View
                    Group::make()
                    ->schema([
                        Forms\Components\Fieldset::make()
                            ->label('Buyers Information')
                            ->schema([
                                TextInput::make('buyer.last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('buyer.first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('buyer.middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(255)
                                    ->required(fn (Get $get): bool => ! $get('no_middle_name'))
                                    ->readOnly(fn (Get $get): bool => $get('no_middle_name')),
                                Select::make('buyer.name_suffix')
                                    ->label('Suffix')
                                    ->required()
                                    ->native(false)
                                    ->options(NameSuffix::all()->pluck('description','code')),
                                Forms\Components\Checkbox::make('no_middle_name')
                                    ->live()
                                    ->label('I have no middle name')
                                    ->inline(true)
                                    ->afterStateUpdated(function(Get $get,Set $set){
                                        $set('buyer.middle_name',null);
//                                                if ($get('no_middle_name')) {
//                                                }
                                    }),
                                Select::make('buyer.civil_status')
                                    ->live()
                                    ->label('Civil Status')
                                    ->required()
                                    ->native(false)
                                    ->options(CivilStatus::all()->pluck('description','code')),
                                Select::make('buyer.gender')
                                    ->label('Gender')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'Male'=>'Male',
                                        'Female'=>'Female'
                                    ]),
                                DatePicker::make('buyer.date_of_birth')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->native(false),
                                Select::make('buyer.nationality')
                                    ->label('Nationality')
                                    ->required()
                                    ->native(false)
                                    ->options(Nationality::all()->pluck('description','code')),
                                Forms\Components\TextInput::make('buyer.email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->unique(ignoreRecord: true,table: Contact::class,column: 'email'),

                                Forms\Components\TextInput::make('buyer.mobile')
                                    ->label('Mobile')
                                    ->required()
                                    ->prefix('+63')
                                    ->regex("/^[0-9]+$/")
                                    ->minLength(10)
                                    ->maxLength(10)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    }),

                                Forms\Components\TextInput::make('buyer.other_mobile')
                                    ->label('Other Mobile')
                                    ->prefix('+63')
                                    ->regex("/^[0-9]+$/")
                                    ->minLength(10)
                                    ->maxLength(10)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    }),

                                Forms\Components\TextInput::make('buyer.landline')
                                    ->label('Landline'),
                            ]),
                        Forms\Components\Fieldset::make('Buyer Present Address')->schema([
                            Select::make('buyer.address.present.ownership')
                                ->options(HomeOwnership::all()->pluck('description','code'))
                                ->native(false)
                                ->required(),
                            Select::make('buyer.address.present.country')
                                ->searchable()
                                ->options(Country::all()->pluck('description','code'))
                                ->native(false)
                                ->live()
                                ->required(),
                            TextInput::make('buyer.address.present.postal_code')
                                ->minLength(4)
                                ->maxLength(4)
                                ->required(),
                            Checkbox::make('buyer.address.present.same_as_permanent')
                                ->label('Same as Permanent')
                                ->inline(false)
                                ->live(),
                            Select::make('buyer.address.present.region')
                                ->searchable()
                                ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $set('buyer.address.present.province', '');
                                    $set('buyer.address.present.city', '');
                                    $set('buyer.address.present.barangay', '');
                                }),
                            Select::make('buyer.address.present.province')
                                ->searchable()
                                ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                    ->where('region_code', $get('buyer.address.present.region'))
                                    ->pluck('province_description', 'province_code'))
                                ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $set('buyer.address.present.city', '');
                                    $set('buyer.address.present.barangay', '');
                                }),
                            Select::make('buyer.address.present.city')
                                ->searchable()
                                ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                ->options(fn(Get $get): Collection => PhilippineCity::query()
                                    ->where('province_code', $get('buyer.address.present.province'))
                                    ->pluck('city_municipality_description', 'city_municipality_code'))
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $set('buyer.address.present.barangay', '');
                                }),
                            Select::make('buyer.address.present.barangay')
                                ->searchable()
                                ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                    ->where('region_code', $get('buyer.address.present.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                    ->where('city_municipality_code', $get('buyer.address.present.city'))
                                    ->pluck('barangay_description', 'barangay_code')
                                )
                                ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                ->native(false)
                                ->live(),
                            TextInput::make('buyer.address.present.address')
                                ->label(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
//                                        ->hint('Unit Number, House/Building/Street No, Street Name')
                                ->placeholder(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                ->required(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH')
                                ->autocapitalize('words')
                                ->maxLength(255)
                                ->live(),
                            Placeholder::make('buyer.address.present.full_address')
                                ->label('Full Address')
                                ->live()
                                ->content(function (Get $get): string {
                                    $region = PhilippineRegion::where('region_code', $get('buyer.address.present.region'))->first();
                                    $province = PhilippineProvince::where('province_code', $get('buyer.address.present.province'))->first();
                                    $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.present.city'))->first();
                                    $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.present.barangay'))->first();
                                    $address = $get('buyer.address.present.address');
                                    $addressParts = array_filter([
                                        $address,
                                        $barangay != null ? $barangay->barangay_description : '',
                                        $city != null ? $city->city_municipality_description : '',
                                        $province != null ? $province->province_description : '',
                                        $region != null ? $region->region_description : '',
                                    ]);
                                    return implode(', ', $addressParts);
                                })
                        ]),
                        Group::make()->schema(
                            fn(Get $get) => $get('buyer.address.present.same_as_permanent') == null ? [
                                Forms\Components\Fieldset::make('Buyer Permanent Address')->schema([
                                    Group::make()->schema([
                                        Select::make('buyer.address.permanent.ownership')
                                            ->options(HomeOwnership::all()->pluck('description','code'))
                                            ->native(false)
                                            ->required(),
                                        Select::make('buyer.address.permanent.country')
                                            ->searchable()
                                            ->options(Country::all()->pluck('description','code'))
                                            ->native(false)
                                            ->live()
                                            ->required(),
                                        TextInput::make('buyer.address.permanent.postal_code')
                                            ->minLength(4)
                                            ->maxLength(4)
                                            ->required(),
                                    ]),


                                    Select::make('buyer.address.permanent.region')
                                        ->searchable()
                                        ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                        ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                        ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                        ->native(false)
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            $set('buyer.address.permanent.province', '');
                                            $set('buyer.address.permanent.city', '');
                                            $set('buyer.address.permanent.barangay', '');
                                        }),
                                    Select::make('buyer.address.permanent.province')
                                        ->searchable()
                                        ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                            ->where('region_code', $get('buyer.address.permanent.region'))
                                            ->pluck('province_description', 'province_code'))
                                        ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                        ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                        ->native(false)
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            $set('buyer.address.permanent.city', '');
                                            $set('buyer.address.permanent.barangay', '');
                                        }),
                                    Select::make('buyer.address.permanent.city')
                                        ->searchable()
                                        ->options(fn(Get $get): Collection => PhilippineCity::query()
                                            ->where('province_code', $get('buyer.address.permanent.province'))
                                            ->pluck('city_municipality_description', 'city_municipality_code'))
                                        ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                        ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                        ->native(false)
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            $set('buyer.address.permanent.barangay', '');
                                        }),
                                    Select::make('buyer.address.permanent.barangay')
                                        ->searchable()
                                        ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                            ->where('region_code', $get('buyer.address.permanent.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                            ->where('city_municipality_code', $get('buyer.address.permanent.city'))
                                            ->pluck('barangay_description', 'barangay_code')
                                        )
                                        ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                        ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                        ->native(false)
                                        ->live(),
                                    TextInput::make('buyer.address.permanent.address')
                                        ->label(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                        ->placeholder(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                        ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH')
                                        ->autocapitalize('words')
                                        ->maxLength(255)
                                        ->live(),
                                    Placeholder::make('buyer.address.permanent.full_address')
                                        ->label('Full Address')
                                        ->live()
                                        ->content(function (Get $get): string {
                                            $region = PhilippineRegion::where('region_code', $get('buyer.address.permanent.region'))->first();
                                            $province = PhilippineProvince::where('province_code', $get('buyer.address.permanent.province'))->first();
                                            $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.permanent.city'))->first();
                                            $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.permanent.barangay'))->first();
                                            $address = $get('buyer.address.permanent.address');
                                            $addressParts = array_filter([
                                                $address,
                                                $barangay != null ? $barangay->barangay_description : '',
                                                $city != null ? $city->city_municipality_description : '',
                                                $province != null ? $province->province_description : '',
                                                $region != null ? $region->region_description : '',
                                            ]);
                                            return implode(', ', $addressParts);
                                        }),


                                ])->columns(12)->columnSpanFull(),
                            ] : []
                        ),
                        \Filament\Forms\Components\Fieldset::make('Buyers Employment')->schema([
                            Select::make('buyer_employment.type')
                                ->label('Employment Type')
                                ->live()
                                ->required()
                                ->native(false)
                                ->options(EmploymentType::all()->pluck('description','code')),
                            Select::make('buyer_employment.status')
                                ->label('Employment Status')
                                ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->native(false)
                                ->options(EmploymentStatus::all()->pluck('description','code')),
                            Select::make('buyer_employment.tenure')
                                ->label('Tenure')
                                ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->native(false)
                                ->options(Tenure::all()->pluck('description','code')),
                            Select::make('buyer_employment.position')
                                ->label('Current Position')
                                ->native(false)
                                ->options(CurrentPosition::all()->pluck('description','code'))
                                ->searchable()
                                ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code),
                            TextInput::make('buyer_employment.rank')
                                ->label('Rank')
                                ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->maxLength(255),
                            Select::make('buyer_employment.work_industry')
                                ->label('Work Industry')
                                ->required()
                                ->native(false)
                                ->options(WorkIndustry::all()->pluck('description','code'))
                                ->searchable(),
                            TextInput::make('buyer_employment.gross_monthly_income')
                                ->label('Gross Monthly Income')
                                ->numeric()
                                ->prefix('PHP')
                                ->required()
                                ->maxLength(255),
                            Group::make()->schema([
                                TextInput::make('buyer_employment.id.tin')
                                    ->label('Tax Identification Number')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('buyer_employment.id.pag_ibig')
                                    ->label('PAG-IBIG Number')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('buyer_employment.id.sss_gsis')
                                    ->label('SSS/GSIS Number')
                                    ->required()
                                    ->maxLength(255),
                            ]),


                        ]),
                        Forms\Components\Fieldset::make('Buyer Employer/Business')->schema([
                            TextInput::make('buyer_employment.employer.employer_business_name')
                                ->label('Employer / Business Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('buyer_employment.employer.contact_person')
                                ->label('Contact Person')
                                ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->maxLength(255),
                            TextInput::make('buyer_employment.employer.employer_email')
                                ->label('Email')
                                ->email()
                                ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->maxLength(255),
                            TextInput::make('buyer_employment.employer.mobile')
                                ->label('Contact Number')
                                ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                ->prefix('+63')
                                ->regex("/^[0-9]+$/")
                                ->minLength(10)
                                ->maxLength(10)
                                ->live(),
                            TextInput::make('buyer_employment.employer.year_established')
                                ->label('Year Established')
                                ->required()
                                ->numeric(),
                        ]),
                        Forms\Components\Fieldset::make('Buyer Employment/Employer Address')->schema([
                            Group::make()
                                ->schema([
                                    Select::make('buyer_employment.employer.address.country')
                                        ->searchable()
                                        ->options(Country::all()->pluck('description','code'))
                                        ->native(false)
                                        ->live()
                                        ->required(),
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
                                    $set('buyer_employment.employer.address.province','');
                                    $set('buyer_employment.employer.address.city','');
                                    $set('buyer_employment.employer.address.barangay','');
                                }),
                            Select::make('buyer_employment.employer.address.province')
                                ->searchable()
                                ->options(fn (Get $get): Collection => PhilippineProvince::query()
                                    ->where('region_code', $get('buyer_employment.employer.address.region'))
                                    ->pluck('province_description', 'province_code'))
                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function(Set $set, $state){
                                    $set('buyer_employment.employer.address.city','');
                                    $set('buyer_employment.employer.address.barangay','');
                                }),
                            Select::make('buyer_employment.employer.address.city')
                                ->searchable()
                                ->options(fn (Get $get): Collection => PhilippineCity::query()
                                    ->where('province_code', $get('buyer_employment.employer.address.province'))
                                    ->pluck('city_municipality_description', 'city_municipality_code'))
                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function(Set $set, $state){
                                    $set('buyer_employment.employer.address.barangay','');
                                }),
                            Select::make('buyer_employment.employer.address.barangay')
                                ->searchable()
                                ->options(fn (Get $get): Collection =>PhilippineBarangay::query()
                                    ->where('region_code', $get('buyer_employment.employer.address.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                    ->where('city_municipality_code', $get('buyer_employment.employer.address.city'))
                                    ->pluck('barangay_description', 'barangay_code')
                                )
                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                ->native(false)
                                ->live(),
                            TextInput::make('buyer_employment.employer.address.address')
                                ->label(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                ->placeholder(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH')
                                ->autocapitalize('words')
                                ->maxLength(255)
                                ->live(),
                        ]),

                        Forms\Components\Fieldset::make('Spouse Personal')->schema([
                            TextInput::make('spouse.last_name')
                                ->label('Last Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('spouse.first_name')
                                ->label('First Name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('spouse.middle_name')
                                ->label('Middle Name')
                                ->maxLength(255)
                                ->required(fn (Get $get): bool => ! $get('spouse.no_middle_name'))
                                ->readOnly(fn (Get $get): bool =>  $get('spouse.no_middle_name')),
                            Select::make('spouse.name_suffix')
                                ->label('Suffix')
                                ->required()
                                ->native(false)
                                ->options(NameSuffix::all()->pluck('description','code')),

                            Forms\Components\Checkbox::make('spouse.no_middle_name')
                                ->label('I have no middle name')
                                ->live()
                                ->inline(false)
                                ->afterStateUpdated(function(Get $get,Set $set){
                                    $set('spouse.middle_name',null);
//                                                if ($get('no_middle_name')) {
//                                                }
                                }),
                            Select::make('spouse.civil_status')
                                ->label('Civil Status')
                                ->required()
                                ->native(false)
                                ->options(CivilStatus::all()->pluck('description','code')),
                            Select::make('spouse.gender')
                                ->label('Gender')
                                ->required()
                                ->native(false)
                                ->options([
                                    'Male'=>'Male',
                                    'Female'=>'Female'
                                ]),
                            DatePicker::make('spouse.date_of_birth')
                                ->label('Date of Birth')
                                ->required()
                                ->native(false),
                            Select::make('spouse.nationality')
                                ->label('Nationality')
                                ->required()
                                ->native(false)
                                ->options(Nationality::all()->pluck('description','code')),
                            Forms\Components\TextInput::make('spouse.email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->live()
                                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                    $livewire->validateOnly($component->getStatePath());
                                }),

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
                                }),

                            Forms\Components\TextInput::make('spouse.other_mobile')
                                ->label('Other Mobile')
                                ->prefix('+63')
                                ->regex("/^[0-9]+$/")
                                ->minLength(10)
                                ->maxLength(10)
                                ->live()
                                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                    $livewire->validateOnly($component->getStatePath());
                                }),

                            Forms\Components\TextInput::make('spouse.landline')
                                ->label('Landline'),
                        ])->hidden(fn (Get $get): bool => $get('buyer.civil_status')!=CivilStatus::where('description','Married')->first()->code &&  $get('buyer.civil_status')!=null),
                        Forms\Components\Fieldset::make('Documents')
                            ->schema([
                                FileUpload::make('valid_id')
                                    ->label('Valid ID')
                                    ->required(),
                                FileUpload::make('payslip')
                                    ->label('Latest 1 month payslip')
                                    ->required(),
                            ])
                    ])->columnSpanFull()->columns(1)
                        ->visible(fn():bool => $this->screenSize === 'mobile' || $this->screenSize === 'mobile'),
                //Mobile View End
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        while (Contact::where('reference_code', $uuid)->exists()) {
            $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        }
        $attribs =[
            'reference_code'=> $uuid,
            'first_name' => $data['buyer']['first_name'],
            'middle_name' => $data['buyer']['middle_name'],
            'last_name' => $data['buyer']['last_name'],
            'name_suffix' => $data['buyer']['name_suffix'],
            'civil_status' => $data['buyer']['civil_status'],
            'sex' => $data['buyer']['gender'],
            'nationality' => $data['buyer']['nationality'],
            'date_of_birth' => $data['buyer']['date_of_birth'],
            'email' => $data['buyer']['email'],
            'mobile' => $data['buyer']['mobile'],
            'other_mobile' => $data['buyer']['other_mobile'],
            'landline' => $data['buyer']['landline'],

            // Create spouse if data is provided
            'spouse' => [
                'first_name' => $data['spouse']['first_name'] ?? null,
                'middle_name' => $data['spouse']['middle_name'] ?? null,
                'last_name' => $data['spouse']['last_name'] ?? null,
                'name_suffix' => $data['spouse']['name_suffix'] ?? null,
                'civil_status' => $data['spouse']['civil_status'] ?? null,
                'sex' => $data['spouse']['gender'] ?? null,
                'nationality' => $data['spouse']['nationality'] ?? null,
                'date_of_birth' => $data['spouse']['date_of_birth'] ?? null,
                'email' => $data['spouse']['email'] ?? null,
                'mobile' => $data['spouse']['mobile'] ?? null,
                'landline' => $data['spouse']['landline'] ?? null,
                'mothers_maiden_name' => $data['spouse']['mothers_maiden_name'] ?? null,
            ],

            // Add addresses
            'addresses' => [
                [
                    'type' => 'present',
                    'full_address' => $data['buyer']['address']['present']['full_address'] ?? null,
                    'sublocality' => $data['buyer']['address']['present']['barangay'] ?? null,
                    'locality' => $data['buyer']['address']['present']['city'] ?? null,
                    'administrative_area' => $data['buyer']['address']['present']['province'] ?? null,
                    'region' => $data['buyer']['address']['present']['region'] ?? null,
                    'postal_code' => $data['buyer']['address']['present']['postal_code'] ?? null,
                    'block' => $data['buyer']['address']['present']['block'] ?? null,
                    'street' => $data['buyer']['address']['present']['street'] ?? null,
                    'ownership' => $data['buyer']['address']['present']['ownership'] ?? null,
                    'country' => $data['buyer']['address']['present']['country'] ?? '',
                    'address1' =>$data['buyer']['address']['present']['address'] ?? null,
                ],

            ],

            // Add employment
            'employment' => [
                [
                    'type'=>'buyer',
                    'employment_status' => $data['buyer_employment']['status'] ?? null,
                    'monthly_gross_income' => $data['buyer_employment']['gross_monthly_income'] ?? null,
                    'current_position' => $data['buyer_employment']['position'] ?? null,
                    'employment_type' => $data['buyer_employment']['type'] ?? null,
                    'years_in_service' => $data['buyer_employment']['tenure'] ?? null,
                    'employer' => [
                        'name' => $data['buyer_employment']['employer']['employer_business_name'] ?? null,
                        'industry' => $data['buyer_employment']['employer']['work_industry'] ?? null,
                        'nationality' => $data['buyer_employment']['employer']['nationality'] ?? null,
                        'contact_no' => $data['buyer_employment']['employer']['mobile'] ?? null,
                        'year_established' => $data['buyer_employment']['employer']['year_established'] ?? null,
                        'total_number_of_employees' => $data['buyer_employment']['employer']['total_number_of_employees'] ?? null,
                        'email' => $data['buyer_employment']['employer']['employer_email'] ?? null,
                        'fax' => $data['buyer_employment']['employer']['fax'] ?? null,

                        // Expanding the employer address structure
                        'address' => [
                            'full_address' => $data['buyer_employment']['employer']['address']['full_address'] ?? null,
                            'locality' => $data['buyer_employment']['employer']['address']['locality'] ?? null,
                            'administrative_area' => $data['buyer_employment']['employer']['address']['administrative_area'] ?? null,
                            'country' => $data['buyer_employment']['employer']['address']['country'] ?? null,
                            'address' => $data['buyer_employment']['employer']['address']['address'] ?? null,
                            'ownership' =>'company',
                            'type'=>'company',
                        ]
                    ],
                    'id' => [
                        'tin' => $data['buyer_employment']['id']['tin'] ?? null,
                        'pagibig' => $data['buyer_employment']['id']['pag_ibig'] ?? null,
                        'sss' => $data['buyer_employment']['id']['sss_gsis'] ?? null,
                        'gsis' => $data['buyer_employment']['id']['sss_gsis'] ?? null,
                    ],
                    'character_reference'=> $data['buyer_employment']['character_reference']??'',
                ],
            ],
            'order'=>null,
            'idImage' => $data['valid_id']??null,
            'payslipImage' => $data['payslip']??null,
        ];

        if ($data['buyer']['address']['present']['same_as_permanent']==true){
            $attribs['addresses'][1]=[
                'type' => 'permanent',
                'full_address' => $data['buyer']['address']['present']['full_address'] ?? null,
                'sublocality' => $data['buyer']['address']['present']['barangay'] ?? null,
                'locality' => $data['buyer']['address']['present']['city'] ?? null,
                'administrative_area' => $data['buyer']['address']['present']['province'] ?? null,
                'region' => $data['buyer']['address']['present']['region'] ?? null,
                'postal_code' => $data['buyer']['address']['present']['postal_code'] ?? null,
                'block' => $data['buyer']['address']['present']['block'] ?? null,
                'street' => $data['buyer']['address']['present']['street'] ?? null,
                'ownership' => $data['buyer']['address']['present']['ownership'] ?? null,
                'country' => $data['buyer']['address']['present']['country'] ?? '',
                'address1' =>$data['buyer']['address']['present']['address'] ?? null,
            ];
        }else{
            $attribs['addresses'][1]=[
                'type' => 'permanent',
                'full_address' => $data['buyer']['address']['permanent']['full_address'] ?? null,
                'sublocality' => $data['buyer']['address']['permanent']['barangay'] ?? null,
                'locality' => $data['buyer']['address']['permanent']['city'] ?? null,
                'administrative_area' => $data['buyer']['address']['permanent']['province'] ?? null,
                'region' => $data['buyer']['address']['permanent']['region'] ?? null,
                'postal_code' => $data['buyer']['address']['permanent']['postal_code'] ?? null,
                'block' => $data['buyer']['address']['permanent']['block'] ?? null,
                'street' => $data['buyer']['address']['permanent']['street'] ?? null,
                'ownership' => $data['buyer']['address']['permanent']['ownership'] ?? null,
                'country' => $data['buyer']['address']['permanent']['country'] ?? '',
                'address1' =>$data['buyer']['address']['permanent']['address'] ?? null,
            ];
        }
        $action = app(PersistContactAction::class);
        $validator = Validator::make($attribs, $action->rules());

        if ($validator->fails()) {
            dd($validator);
            throw new ValidationException($validator);
        }
        $validated = $validator->validated();
        $contact = $action->run($validated);
        $this->record->contact_id=Contact::where('reference_code', $uuid)->first()->id;
        $this->record->save();
        $this->dispatch('open-modal', id: 'success-modal');
    }

    public function render(): View
    {
        return view('livewire.prospects.client-information-sheet');
    }
}
