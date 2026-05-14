<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'tracking_no',
        'date_of_birth',
        'house_no',
        'street',
        'district_id',
        'barangay_id',
        'city',
        'contact_number',
    ];

    protected static function booted()
    {
        static::creating(function ($client) {
            $year = now()->format('Y');
            $count = self::whereYear('created_at', $year)->count() + 1;

            $client->tracking_no = sprintf('%s-%04d', $year, $count);
        });
    }

    public static function getAllClients()
    {
        return self::all();
    }

    public static function getClientInfo($client)
    {
        return self::where('id', $client)->first();
    }

    /**
     * Summary of user
     * @return BelongsTo<User, Client>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of fullname
     * @return string fullname of the client
     */
    public function fullname(): string
    {
        return $this->user->first_name.' '.
            ($this->user->middle_name ? Str::limit($this->user->middle_name, 1, '.').' ' : '').
            $this->user->last_name.
            ($this->user->suffix ? ' '.$this->user->suffix : '');
    }

    /**
     * Summary of age
     * @return int deducts the date of birth and returns the age
     */
    public function age(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Summary of address
     * @return string joins the house number, street, and barangay name as singular address string
     */
    public function address(): string
    {
        $address = $this->house_no.' '.$this->street.', '.$this->barangay?->name;

        return $address;
    }

    /**
     * Summary of assessment
     * @return HasMany<ClientAssessment>
     */
    public function assessment(): HasMany
    {
        return $this->hasMany(ClientAssessment::class);
    }

    /**
     * Summary of beneficiary
     * @return HasOne<Beneficiary>
     */
    public function beneficiary(): HasOne
    {
        return $this->hasOne(Beneficiary::class);
    }

    /**
     * Summary of family
     * @return HasMany<BeneficiaryFamily>
     */
    public function family(): HasMany
    {
        return $this->hasMany(BeneficiaryFamily::class);
    }

    /**
     * Summary of demographic
     * @return HasOne<ClientDemographic>
     */
    public function demographic(): HasOne
    {
        return $this->hasOne(ClientDemographic::class);
    }

    /**
     * Summary of socialInfo
     * @return HasOne<ClientSocialInfo>
     */
    public function socialInfo(): HasOne
    {
        return $this->hasOne(ClientSocialInfo::class);
    }

    /**
     * Summary of recommendation
     * @return HasMany<ClientRecommendation>
     */
    public function recommendation(): HasMany
    {
        return $this->hasMany(ClientRecommendation::class);
    }

    /**
     * Summary of referral
     * @return HasOne<Referral>
     */
    public function referral(): HasOne
    {
        return $this->hasOne(Referral::class);
    }

    /**
     * Summary of district
     * @return BelongsTo<District, Client>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Summary of barangay
     * @return BelongsTo<Barangay, Client>
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    /**
     * Summary of interviews
     * @return HasMany<Interview>
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Summary of claimant
     * @return HasOne<Claimant>
     */
    public function claimant(): HasOne
    {
        return $this->hasOne(Claimant::class, 'client_id', 'id');
    }

    /**
     * Summary of funeralAssistance
     * @return HasOne<FuneralAssistance>
     */
    public function funeralAssistance(): HasOne
    {
        return $this->hasOne(FuneralAssistance::class, 'client_id', 'id');
    }

    // Scopes
    public function scopeTotal($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query;
        }

        return $query->where('user_id', auth()->user()->id);
    }

    public function scopeReferral($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->whereHas('referral');
        }

        return $query->where('user_id', auth()->user()->id)->whereHas('referral');
    }

    public function scopeBurialAssistance($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->whereHas('claimant');
        }

        return $query->where('user_id', auth()->user()->id)->whereHas('claimant');
    }

    public function scopeFuneralAssistance($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->whereHas('funeralAssistance');
        }

        return $query->where('user_id', auth()->user()->id)->whereHas('funeralAssistance');
    }
}
