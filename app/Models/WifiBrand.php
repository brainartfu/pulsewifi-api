<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiBrand extends Model
{
    use HasFactory;
    public $table = "wi_fi_brand";
    protected $primaryKey = 'id'; // or null

    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'logo'
    ];
}