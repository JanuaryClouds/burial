<?php

namespace App\Models;

use App\Traits\HasRelationSets;
use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Beneficiary extends Model
{
    use HasFactory, HasRelationSets, HasUuid;

    protected $table = 'beneficiaries';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex_id',
        'religion_id',
        'date_of_birth',
        'date_of_death',
        'house_no',
        'street',
        'barangay_id',
        'district_id',
        'city',
        'created_by',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
        'house_no' => 'encrypted',
        'street' => 'encrypted',
        'city' => 'encrypted',
    ];

    /**
     * Summary of fullname
     *
     * @return string returns the full name of the beneficiary, with the middlename being shortened
     */
    public function fullname(): string
    {
        return $this->first_name.' '.
            ($this->middle_name ? Str::substr($this->middle_name, 0, 1).'. ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '');
    }

    /**
     * Summary of age
     *
     * @return int returns the age of the beneficiary
     */
    public function age(): int
    {
        return Carbon::parse($this->created_at)->diffInYears($this->date_of_birth);
    }

    /**
     * Summary of sex
     *
     * @return BelongsTo<Sex, Beneficiary>
     */
    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    /**
     * Summary of application
     *
     * @return HasOne<Application>
     */
    public function application(): HasOne
    {
        return $this->hasOne(Application::class, 'beneficiary_uuid', 'uuid');
    }

    /**
     * Summary of religion
     *
     * @return BelongsTo<Religion, Beneficiary>
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    /**
     * Summary of barangay
     *
     * @return BelongsTo<Barangay, Beneficiary>
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    /**
     * Summary of district
     *
     * @return BelongsTo<District, Beneficiary>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Summary of family
     *
     * @return HasMany<BeneficiaryFamily, Beneficiary>
     */
    public function family(): HasMany
    {
        return $this->hasMany(BeneficiaryFamily::class, 'beneficiary_uuid', 'uuid');
    }

    /**
     * Summary of user
     *
     * @return BelongsTo<User, Beneficiary>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Model Functions
    |--------------------------------------------------------------------------
    |
    | Custom model functions.
    |
    */

    public static function relations()
    {
        return [
            'sex',
            'religion',
            'barangay',
            'district',
            'family',
            'family.sex',
            'family.civil',
            'family.relationship',
        ];
    }

    // Scopes

    public function scopeTotal($query)
    {
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->roles()->exists()) {
            return $query;
        }

        return $query->whereHas('client', function ($query) use ($user) {
            $query->whereIn('id', $user->clients->pluck('id'));
        });
    }

    public function scopeReferral($query)
    {
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->roles()->exists()) {
            return $query->whereHas('client.referral');
        }

        return $query->whereHas('client', function ($query) use ($user) {
            $query->whereIn('id', $user->clients->pluck('id'))->whereHas('referral');
        });
    }

    public function scopeBurialAssistance($query)
    {
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->roles()->exists()) {
            return $query->whereHas('client.claimant');
        }

        return $query->whereHas('client', function ($query) use ($user) {
            $query->whereIn('id', $user->clients->pluck('id'))->whereHas('claimant');
        });
    }

    public function scopeFuneralAssistance($query)
    {
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->roles()->count() > 0) {
            return $query->whereHas('client.funeralAssistance');
        }

        return $query->whereHas('client', function ($query) use ($user) {
            $query->whereIn('id', $user->clients->pluck('id'))->whereHas('funeralAssistance');
        });
    }
}
