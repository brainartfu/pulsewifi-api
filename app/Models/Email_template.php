<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email_template extends Model
{
    use HasFactory;
    public $table = "Email_template";
    protected $fillable = [
        'id',
        'name',
        'text',
        'file_path',
        'pdoa_id',
        'status',
    ];
}