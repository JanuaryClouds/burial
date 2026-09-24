<?php

namespace App\Models;

use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
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
    public function scopeTotal($query)
    {
        if (! Auth::user()) {
            return $query->whereRaw('1 = 0');
        }

        if (Auth::user()->roles()->count() > 0) {
            return $query->whereHas('application');
        }

        return $query->where('user_id', Auth::id())
            ->whereHas('application');
    }

    public function scopePerMonth($query)
    {
        if (! Auth::user()) {
            return $query->whereRaw('1 = 0');
        }

        $query->with(['application']);

        if (Auth::user()->roles()->count() > 0) {
            $query->whereHas('application');
        } else {
            $query->whereHas('user', function ($query) {
                $query->where('id', Auth::id());
            })
                ->whereHas('application');
        }

        return $query
            ->selectRaw('YEAR(created_at) as year')
            ->selectRaw('MONTH(created_at) as month')
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)');
    }

    public function scopeCurrentMonth($query)
    {
        if (! Auth::user()) {
            return $query->whereRaw('1 = 0');
        }

        $query->with(['application']);

        if (Auth::user()->roles()->count() > 0) {
            $query->with(['application']);
        } else {
            $query->with(['application'])
                ->whereHas('user', function ($query) {
                    $query->where('id', Auth::id());
                });
        }

        return $query->whereHas('application', function ($query) {
            $query->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month);
        });
    }
}
