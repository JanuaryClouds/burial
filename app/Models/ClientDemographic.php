<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDemographic extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'client_demographics';

    protected $fillable = [
        'client_uuid',
        'sex_id',
        'religion_id',
        'nationality_id',
    ];

    public static function getClientDemographic($client)
    {
        return self::where($client, 'client_id')->first();
    }

    /**
     * Summary of sex
     *
     * @return BelongsTo<Sex, ClientDemographic>
     */
    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    /**
     * Summary of religion
     *
     * @return BelongsTo<Religion, ClientDemographic>
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    /**
     * Summary of nationality
     *
     * @return BelongsTo<Nationality, ClientDemographic>
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    /**
     * Summary of client
     *
     * @return BelongsTo<Client, ClientDemographic>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
