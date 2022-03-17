<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms_template extends Model
{
    use HasFactory;
    public $table = "sms_template";
    protected $fillable = [
        'id',
        'name',
        'dlt_id',
        'sender_id',
        'text',
        'status',
        'pdoa_id',
    ];
}