<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handler extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'department',
        'is_active',
    ];
    protected $table = 'handlers';
}
