<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRouteRestriction extends Model
{
    use HasFactory;
    protected $table = 'user_route_restrictions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    
    protected $fillable = [
        'uuid',
        'user_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
