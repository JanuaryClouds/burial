<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSocialInfo extends Model
{
    use HasFactory;

    protected $table = 'client_social_infos';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'relationship_id',
        'civil_id',
        'education_id',
        'income',
        'philhealth',
        'skill',
    ];

    public static function getClientSocial($client)
    {
        return self::where($client, 'client_id')->first();
    }

    /**
     * Summary of relationship
     * @return BelongsTo<Relationship, ClientSocialInfo>
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class, 'relationship_id')->withTrashed();
    }

    /**
     * Summary of education
     * @return BelongsTo<Education, ClientSocialInfo>
     */
    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class, 'education_id');
    }

    /**
     * Summary of civil
     * @return BelongsTo<CivilStatus, ClientSocialInfo>
     */
    public function civil(): BelongsTo
    {
        return $this->belongsTo(CivilStatus::class, 'civil_id');
    }

    /**
     * Summary of client
     * @return BelongsTo<Client, ClientSocialInfo>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
