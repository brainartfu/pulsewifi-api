<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiFiOrder extends Model
{
    use HasFactory;
    public $table = "wi_fi_orders";

    protected $fillable = [
        'id',
        'phone',
        'internet_plan_id',
        'amount',
        'franchise_id',
        'pdoa_id',
        'location_id',
        'status',
        'payment_reference',
        'location_id',
        'wifi_user_account_id',
        'pdoa'
        ];
    }

