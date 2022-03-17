<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_ip_logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'src_ip',
        'dest_ip',
        'protocol',
        'port',
        'username',
        'src_port',
        'dest_port',
        'client_device_ip',
        'client_device_ip_type',
        'client_device_translated_ip',
        'pdoa_id'
    ];
}
