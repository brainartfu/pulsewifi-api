<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'product_slug',
        'product_name',
        'fee_amount'
    ];
}