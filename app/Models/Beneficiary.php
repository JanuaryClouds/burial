<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Beneficiary extends Model
{
    use HasFactory;

    protected $table = 'beneficiaries';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex_id',
        'religion_id',
        'date_of_birth',
        'date_of_death',
        'place_of_birth',
        'barangay_id',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
    ];

    /**
     * Summary of tracking_no
     * @return string returns the tracking number from the client that submitted this beneficiary
     */
    public function tracking_no(): string
    {
        return $this->client->tracking_no;
    }

    /**
     * Summary of fullname
     * @return string joins and returns the full name
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
     * @return int calculates the age
     */
    public function age(): int
    {
        return Carbon::parse($this->created_at)->diffInYears($this->date_of_birth);
    }

    /**
     * Summary of assistance
     * @return BurialAssistance|FuneralAssistance|null returns the burial assistance or funeral assistance or none if no assistance is given
     */
    public function assistance(): BurialAssistance|FuneralAssistance|null
    {
        if ($this->client?->claimant->count() > 0) {
            return $this->client->claimant->burialAssistance;
        }

        if ($this->client?->funeralAssistance->count() > 0) {
            return $this->client->funeralAssistance;
        }

        return null;
    }

    /**
     * Summary of getBeneficiary
     * @return Beneficiary|null returns the beneficiary of the client
     */
    public static function getBeneficiary($client): ?self
    {
        return self::where('client_id', $client)->first();
    }

    /**
     * Summary of sex
     * @return BelongsTo<Sex, Beneficiary>
     */
    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    /**
     * Summary of client
     * @return BelongsTo<Client, Beneficiary>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Summary of religion
     * @return BelongsTo<Religion, Beneficiary>
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    /**
     * Summary of barangay
     * @return BelongsTo<Barangay, Beneficiary>
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
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

        return $query->whereIn('client_id', (string) $user->clients->pluck('id'));
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

        return $query->whereIn('client_id', $user->clients->pluck('id'))->whereHas('client.referral');
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

        return $query->whereIn('client_id', (string) $user->clients->pluck('id'))->whereHas('client.claimant');
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

        return $query->whereIn('client_id', (string) $user->clients->pluck('id'))->whereHas('client.funeralAssistance');
    }
}
