<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDemographic extends Model
{
    use HasFactory;
    protected $table = 'client_demographics';
    protected $fillable = [
        'client_id',
        'sex_id', 
        'religion_id', 
        'nationality_id',
    ];

    public static function getClientDemographic($client)
    {
        return self::where($client, 'client_id')->first();
    }

    public function sex()
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}