<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // or null

    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'owner_id',
        'model_ids',
        'fee_description',
        'fee_amount',
        'total_amount',
        'details',
        'processed',
        'non_processed',
        'status',
        'pdoa_id',
    ];

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'owner_id');
    }

    public function pdoa()
    {
        return $this->hasOne(PDOA::class, 'id', 'pdoa_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoices::class, 'order_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'id');
    }
}