<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    public $table = "inventory";
    protected $primaryKey = 'id'; // or null

    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'photo',
        'description',
        'brand',
        'model',
        'version',
        'category',
        'price',
        'shipping'
    ];
}