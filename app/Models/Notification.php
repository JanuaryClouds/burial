<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'class',
        'notifiable_id',
        'notifiable_type',
        'payload',
        'read_at',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
