<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'slug',
        'order_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'total_amount',
        'status'
    ];

    public function user(){
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function order(){
        return $this->hasOne(Orders::class, 'id', 'order_id');
    }

    public function products(){
        return $this->hasMany(OrderProducts::class, 'order_id', 'order_id');
    }
}
