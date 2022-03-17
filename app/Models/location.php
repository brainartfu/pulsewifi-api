<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    public $table = "location";
    protected $fillable = [
        'id',
        'name',
        'owner_id',
        'pdoa_id',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
    ];
}