<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimantChange extends Model
{
    use HasFactory;
    protected $fillable = [
        'burial_assistance_id',
        'old_claimant_id',
        'new_claimant_id',
        'changed_at',
        'reason_for_change',
    ];
    protected $table = 'claimant_changes';
}
