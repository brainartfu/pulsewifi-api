<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'phone',
        'active_package',
        'package_status',
        'data_consume',
        'duration',
        'connected_devices',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'expired_at',
        'last_recharged'
    ];
}