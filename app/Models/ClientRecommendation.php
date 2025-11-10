<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRecommendation extends Model
{
    use HasFactory;
    protected $table = 'client_recommendations';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'client_id',
        'assistance_id',
        'referral',
        'amount',
        'moa_id',
        'others'
    ];

    public static function getClientRecommendations($client)
    {
        return self::where('client_id', $client)->get();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function assistance()
    {
        return $this->belongsTo(Assistance::class, 'assistance_id');
    }

    public function moa()
    {
        return $this->belongsTo(ModeOfAssistance::class, 'moa_id');
    }
}