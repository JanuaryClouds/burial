<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;
    protected $fillable = [
        'burial_assistance_id',
        'cheque_number',
        'issued_date',
        'date_claimed',
    ];
    protected $table = 'cheques';

    public function burialAssistance() {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }
}
