<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network_setting extends Model
{
    use HasFactory;
    public $table = "network_setting";
    protected $fillable = [
        'id',
        'guestEssid',
        'pdoa_id',
        'freeWiFi',
        'freeBandwidth',
        'freeDailySession',
        'freeDataLimit',
        'serverWhitelist',
        'domainWhitelist',
    ];
}