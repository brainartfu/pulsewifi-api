<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiRouterModel extends Model
{
    use HasFactory;
    public $table = "wifi_router_model";
    protected $fillable = [
        'id',
        'name',
        'images',
        'description',
        'status',
        'price',
        'brand',
        'model',
        'hardware_version',
        'ean',
        'package_info',
        'category',
        'shipping',
    ];

}