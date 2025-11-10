<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBeneficiaryFamily extends Model
{
    use HasFactory;
    protected $table = 'client_beneficiary_families';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'client_id',
        'name',
        'sex_id',
        'age',
        'civil_id',
        'relationship_id',
        'occupation', 
        'income'
    ];

    public static function getClientBeneficiaryFamilies($client)
    {
        return self::where('client_id', $client);
    }

    public function sex()
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    public function civil()
    {
        return $this->belongsTo(CivilStatus::class, 'civil_id');
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}