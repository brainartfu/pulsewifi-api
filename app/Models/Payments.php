<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'wifi_user_id',
        'wifi_user_phone',
        'amount',
        'payment_method',
        'location_id',
        'order_id',
        'payment_status',
        'payment_details',
        'pdoa_id',
    ];
}