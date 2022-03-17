<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment_setting extends Model
{
    use HasFactory;
    public $table = "payment_setting";
    protected $fillable = [
        'id',
        'name',
        'key',
        'secret',
        'type',
        'callback_url',
        'status',
        'pdoa_id',
    ];
}