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
            $code->uuid = (string) Str::uuid();
            $code->code = self::generateUniqueCode();
        });
    }

    protected static function generateUniqueCode(int $max_attempts = 10): string
    {
        for ($i = 0; $i < $max_attempts; $i++) {
            $code = Str::upper(Str::random(6));

            if (! static::where('code', $code)->exists()) {
                return $code;
            }
        }

        throw new \RuntimeException('Failed to generate unique code');
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
