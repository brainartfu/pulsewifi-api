<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wifi_router extends Model
{
    use HasFactory;
    public $table = "wifi_router";
    protected $fillable = [
        'id',
        'name',
        'mac_address',
        'location_id',
        'config_version',
        'last_online',
        'owner_id',
        'model_id',
        'pdoa_id',
        'key',
        'secret'
    ];

}