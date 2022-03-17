<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email_logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'receiver_email',
        'subject',
        'sent_time',
        'pdoa_id'
    ];
}
