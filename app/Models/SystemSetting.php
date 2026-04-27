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
        'social_welfare_officer',
        'dept_head',
        'department_email',
    ];

    public static function exceptAttributes()
    {
        return [
            'uuid',
            'created_at',
            'updated_at',
        ];
    }
}
