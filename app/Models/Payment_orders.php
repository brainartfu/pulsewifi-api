<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment_orders extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'internet_plan_id ',
        'wifi_user_id',
        'franchise_id',
        'status',
        'amount',
    ];
}