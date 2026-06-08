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
     * @return string returns the tracking number of the client this beneficiary is created from
     */
    public function tracking_no(): string
    {
        return $this->client?->tracking_no;
    }

    /**
     * Summary of fullname
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
     * @return int returns the age of the beneficiary 
     */
    public function age(): int
    {
        return Carbon::parse($this->created_at)->diffInYears($this->date_of_birth);
    }

    /**
     * Summary of assistance
     * @return mixed returns the assistance of the beneficiary, could be burial or funeral
     */
    public function assistance(): mixed
    {
        if ($this->client?->claimant?->count() > 0) {
            return $this->client?->claimant?->burialAssistance;
        }

        if ($this->client?->funeralAssistance?->count() > 0) {
            return $this->client?->funeralAssistance;
        }

        return null;
    }

    /**
     * Summary of sex
     * @return BelongsTo<Sex>
     */
    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    /**
     * Summary of client
     * @return BelongsTo<Client>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Summary of religion
     * @return BelongsTo<Religion>
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    /**
     * Summary of barangay
     * @return BelongsTo<Barangay>
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

        if (! $user->client) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('client_id', $user->client->id);
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

        if (! $user->client) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('client_id', $user->client->id)->whereHas('client.referral');
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

        if (! $user->client) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('client_id', $user->client->id)->whereHas('client.claimant');
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

        if (! $user->client) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('client_id', $user->client->id)->whereHas('client.funeralAssistance');
    }
}
