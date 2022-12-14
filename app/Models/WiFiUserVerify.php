<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiFiUserVerify extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_phone',
        'otp',
        'challenge',
        'usermac',
        'url_code',
        'os',
        'location_id',
        'group_id',
        'status'
    ];
}