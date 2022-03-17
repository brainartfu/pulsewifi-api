<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms_gateway extends Model
{
    use HasFactory;
    public $table = "sms_gateway";
    protected $fillable = [
        'id',
        'name',
        'key',
        'secret',
        'status',
        'pdoa_id',
    ];
}