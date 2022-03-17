<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdoa_plan extends Model
{
    use HasFactory;
    public $table = "pdoa_plan";
    protected $fillable = [
        'id',
        'plan_name',
        'price',
        'status',
        'max_wifi_router_count',
    ];
}