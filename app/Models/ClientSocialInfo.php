<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSocialInfo extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'client_social_infos';

    protected $fillable = [
        'client_uuid',
        'civil_id',
        'education_id',
        'income',
        'philhealth',
        'skill',
    ];

    protected $casts = [
        'income' => 'encrypted',
        'philhealth' => 'encrypted',
        'skill' => 'encrypted',
    ];

    /**
     * Summary of education
     *
     * @return BelongsTo<Education, ClientSocialInfo>
     */
    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class, 'education_id')->withTrashed();
    }

    /**
     * Summary of civil
     *
     * @return BelongsTo<CivilStatus, ClientSocialInfo>
     */
    public function civil(): BelongsTo
    {
        return $this->belongsTo(CivilStatus::class, 'civil_id');
    }

    /**
     * Summary of client
     *
     * @return BelongsTo<Client, ClientSocialInfo>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
