<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrackingCode extends Model
{
    use HasFactory;

    protected $table = 'tracking_codes';

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'trackable_id',
        'trackable_type',
        'generated_by',
    ];

    protected static function booted()
    {
        static::creating(function ($code) {
            $code->uuid = Str::uuid();
            $code->code = Str::upper(Str::random(6));
        });
    }

    public function trackable()
    {
        return $this->morphTo();
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
