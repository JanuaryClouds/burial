<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBeneficiary extends Model
{
    use HasFactory;
    protected $table = 'client_beneficiaries';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'client_id', 
        'first_name',
        'middle_name',
        'last_name',
        'sex_id',
        'date_of_birth',
        'place_of_birth',
    ];

    public static function getClientBeneficiary($client)
    {
        return self::where('client_id', $client)->first();
    }

    public function sex()
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}