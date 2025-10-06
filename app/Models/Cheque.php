<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;
    protected $fillable = [
        'burial_assistance_id',
        'claimant_id',
        'obr_number',
        'cheque_number',
        'dv_number',
        'amount',
        'date_issued',
        'date_claimed',
        'status'
    ];
    protected $table = 'cheques';

    public function burialAssistance() {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    public function claimant() {
        return $this->belongsTo(Claimant::class, 'claimant_id', 'id');
    }
}
