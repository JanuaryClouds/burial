<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'maintenance_mode',
        'walk_in_intake_enabled',
        'claimant_change_limit',
        'social_welfare_officer',
        'dept_head',
        'department_email',
    ];

    protected $casts = ['claimant_change_limit' => 'integer', 'walk_in_intake_enabled' => 'boolean'];

    public static function walkInIntakeEnabled(): bool
    {
        return (bool) self::query()->first()?->walk_in_intake_enabled;
    }

    public static function exceptAttributes()
    {
        return [
            'uuid',
            'created_at',
            'updated_at',
        ];
    }
}
