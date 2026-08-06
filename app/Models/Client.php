<?php

namespace App\Models;

use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'clients';

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'house_no',
        'street',
        'district_id',
        'barangay_id',
        'city',
        'contact_number',
    ];

    protected $casts = [
        'house_no' => 'encrypted',
        'street' => 'encrypted',
        'city' => 'encrypted',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Model Relationships.
    |
    */

    /**
     * Summary of user
     *
     * @return BelongsTo<User, Client>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of application
     *
     * @return HasOne<Application>
     */
    public function application(): HasOne
    {
        return $this->hasOne(Application::class);
    }

    /**
     * Summary of demographic
     *
     * @return HasOne<ClientDemographic>
     */
    public function demographic(): HasOne
    {
        return $this->hasOne(ClientDemographic::class);
    }

    /**
     * Summary of socialInfo
     *
     * @return HasOne<ClientSocialInfo>
     */
    public function socialInfo(): HasOne
    {
        return $this->hasOne(ClientSocialInfo::class);
    }

    /**
     * Summary of district
     *
     * @return BelongsTo<District, Client>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Summary of barangay
     *
     * @return BelongsTo<Barangay, Client>
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    /**
     * Summary of interviews
     *
     * @return HasMany<Interview>
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public static function relations(): array
    {
        return [
            'user',
            'demographic',
            'demographic.sex',
            'demographic.religion',
            'demographic.nationality',
            'socialInfo',
            'socialInfo.education',
            'socialInfo.civil',
            'district',
            'barangay',
            'interviews',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Model functions
    |--------------------------------------------------------------------------
    |
    | Model functions.
    |
    */

    /**
     * Summary of fullname
     */
    public function fullname(): string
    {
        $user = $this->user;

        if (! $user) {
            return '';
        }

        return $user->first_name.' '.
            ($user->middle_name ? Str::limit($user->middle_name, 1, '.').' ' : '').
            $user->last_name.
            ($user->suffix ? ' '.$user->suffix : '');
    }

    /**
     * Summary of age
     *
     * @return int returns the age of the client
     */
    public function age(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Summary of address
     *
     * @return string joins the house number, street, and barangay name
     */
    public function address(): string
    {
        return $this->house_no.' '.$this->street.', '.$this->barangay->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Model scopes
    |--------------------------------------------------------------------------
    |
    | Model scopes.
    |
    */

    // Scopes
    public function scopeTotal(Builder $query)
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
