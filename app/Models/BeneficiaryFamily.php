<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryFamily extends Model
{
    use HasFactory;

    protected $table = 'beneficiary_families';

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
        'income',
    ];

    protected $casts = [
        'name' => 'encrypted',
    ];

    public static function getBeneficiaryFamilies($client)
    {
        return self::where('client_id', $client);
    }

    /**
     * Summary of sex
     *
     * @return BelongsTo<Sex, BeneficiaryFamily>
     */
    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    /**
     * Summary of civil
     *
     * @return BelongsTo<CivilStatus, BeneficiaryFamily>
     */
    public function civil(): BelongsTo
    {
        return $this->belongsTo(CivilStatus::class, 'civil_id');
    }

    /**
     * Summary of relationship
     *
     * @return BelongsTo<Relationship, BeneficiaryFamily>
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class, 'relationship_id')->withTrashed();
    }

    /**
     * Summary of client
     *
     * @return BelongsTo<Client, BeneficiaryFamily>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
