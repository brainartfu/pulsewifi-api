<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wi_fi_users extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'mac_address',
        'pdoa',
        'password'
    ];
}