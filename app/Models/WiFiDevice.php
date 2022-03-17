<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiFiDevice extends Model
{
    use HasFactory;
    public $table = "wi_fi_devices";
    protected $fillable = [
        'id',
        'phone',
        'otp',
        'challenge',
        'usermac',
        'url_code',
        'os',
        'brand',
        'location_id',
        'pdoa',
        'status',
        'wifi_user_account_id',
        'otp_generate_time'
    ];
}
