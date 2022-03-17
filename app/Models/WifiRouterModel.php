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
        'description',
        'images',
        'status',
        'price',
    ];

}