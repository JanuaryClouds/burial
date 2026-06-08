<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'type',
        'remarks',
        'others',
    ];

    public static function getClientRecommendations($client)
    {
        return self::where('client_id', $client)->get();
    }

    /**
     * Summary of client
     * @return BelongsTo<Client, ClientRecommendation>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Summary of assistance
     * @return BelongsTo<Assistance, ClientRecommendation>
     */
    public function assistance(): BelongsTo
    {
        return $this->belongsTo(Assistance::class, 'assistance_id');
    }

    /**
     * Summary of moa
     * @return BelongsTo<ModeOfAssistance, ClientRecommendation>
     */
    public function moa(): BelongsTo
    {
        return $this->belongsTo(ModeOfAssistance::class, 'moa_id');
    }
}
