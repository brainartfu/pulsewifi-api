<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    use HasFactory;
    public $table = "radacct";
    protected $connection = 'mysql2';
}
