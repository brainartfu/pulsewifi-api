<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payouts extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'wifi_user_id',
        'amount',
        'tax_amount',
        'payment_method',
        'franchise_id',
        'franchise_amount',
        'distributor_id',
        'distributor_amount',
        'payout_status',
        'payout_details',
        'pdoa_id',
    ];
}
