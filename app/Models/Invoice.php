<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
     use HasFactory;

    // which columns can be mass-assigned
    protected $fillable = [
        'lead_guest_name',
        'email',
        'number_of_pax',
        'hotel_accommodation',
        'tour_package',
        'rate_per_pax', // you can keep this or later drop it
        'total_amount',
        'downpayment',
        'balance',
        'status',
        'arrival_date',
        'departure_date',
        'due_date',
        'date_issued',

        'adult_count',
        'adult_rate',
        'infant_count',
        'infant_rate',
        'senior_count',
        'senior_rate',
    ];

    public function schedule(){
        return $this->hasOne(Schedule::class);
    }
    public function logs(): HasMany
    {
        return $this->hasMany(\App\Models\InvoiceLog::class)->latest();
    }
}
