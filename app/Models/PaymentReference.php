<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReference extends Model
{
    protected $guarded = [];
    use HasFactory;
    protected $fillable = [
        'id',
        'order_id',
        'razorpay_order_id',
        'razorpay_payment_reference',
        'status',
        'razorpay_response',
        'updated_via'
    ];
}
