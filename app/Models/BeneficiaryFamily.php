<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryFamily extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'beneficiary_families';

    protected $fillable = [
        'beneficiary_uuid',
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
        'income' => 'encrypted',
        'occupation' => 'encrypted',
    ];

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
     * Summary of beneficiary
     *
     * @return BelongsTo<Beneficiary, BeneficiaryFamily>
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_uuid');
    }

    public static function relations(): array
    {
        return [
            'sex',
            'civil',
            'relationship',
            'beneficiary',
        ];
    }
}
