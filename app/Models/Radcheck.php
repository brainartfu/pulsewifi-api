<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radcheck extends Model
{
    use HasFactory;
    public $table = "radcheck";
    protected $connection = 'mysql2';
}
