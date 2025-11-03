<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rejection extends Model
{
    use HasFactory;

    protected $table = "rejections";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'burial_assistance_id',
        'reason',
    ];

    public function burialAssistance() {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }
}
