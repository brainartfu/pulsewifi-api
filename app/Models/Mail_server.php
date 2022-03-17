<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail_server extends Model
{
    use HasFactory;
    public $table = "mail_server";
    protected $fillable = [
        'id',
        'name',
        'sender_name',
        'sender_email',
        'api_key',
        'status',
        'pdoa_id',
    ];
}