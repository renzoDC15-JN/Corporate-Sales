<?php

namespace App\Models;

use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Prospects extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'id',
        'prospect_id',
        'last_name',
        'first_name',
        'middle_name',
        'name_extension',
        'company',
        'position_title',
        'salary',
        'mid',
        'hloan',
        'email',
        'mobile_number',
        'preferred_project',
        'contact_id',
        'employee_id_number',
        'has_pagibig_number',
        'pagibig_id',
        'civil_status_code',
        'gender_code',
        'date_of_birth',
        'ownership_code',
        'rent_amount',
        'employment_status',
        'employment_tenure',
        'location'
//        'idImage',
//        'payslipImage'
    ];
    public function preferredProject()
    {
        return $this->belongsTo(Project::class, 'preferred_project', 'code');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    protected static function booted(): void
    {
        static::creating(function ($prospect) {
            if (empty($prospect->id)) {
                $prospect->id = Str::uuid(); // Assign a UUID to the 'id' column
            }
            $latest = static::latest('created_at')->first();

            if ($latest) {
                $number = intval(substr($latest->prospect_id, -4)) + 1;
            } else {
                $number = 1;
            }
            $prospect->prospect_id = 'JN-CORP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
    public function routeNotificationForEngageSpark(): string
    {
        return $this->mobile_number;
    }
    public function getMobileNumber(): PhoneNumber
    {
        return new PhoneNumber($this->mobile_number, 'PH');
    }

    public function setMobileNumber($value): void
    {
        if ($value instanceof PhoneNumber) {
            $this->attributes['mobile_number'] = $value->formatE164();
        } elseif (is_string($value)) {
            $this->attributes['mobile_number'] = $value;
        } else {
            throw new \InvalidArgumentException('Mobile must be a string or an instance of PhoneNumber.');
        }
    }

}
