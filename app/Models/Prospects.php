<?php

namespace App\Models;

use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;

class Prospects extends Model
{
    use HasFactory;
    protected $fillable = [
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
    ];
    protected static function booted(): void
    {
        static::creating(function ($prospect) {
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
