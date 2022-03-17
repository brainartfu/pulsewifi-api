<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nas_list extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'nas_lists';
    use HasFactory;
    protected $fillable = [
        'id',
        'pdoa',
        'enabled'
    ];
}
