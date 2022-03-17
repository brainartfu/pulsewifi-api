<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms_logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'receiver_phone',
        'text',
        'sent_time',
        'pdoa_id'
    ];
}
