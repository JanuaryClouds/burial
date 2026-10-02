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
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;
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
        'pwd',
        'created_by',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
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
        return Carbon::parse($this->date_of_birth)->diffInYears($this->date_of_death);
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

    /**
     * Summary of address
     *
     * @return MorphOne<Address, Beneficiary>
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /*
    |--------------------------------------------------------------------------
    | Model Functions
    |--------------------------------------------------------------------------
    |
    | Custom model functions.
    |
    */

    /**
     * Summary of address
     */
    public function fullAddress(): string
    {
        $address = $this->address;

        if (! $address) {
            return '';
        }

        return $address->full();
    }

    public static function relations()
    {
        return [
            'sex',
            'religion',
            'address',
            'family',
            'family.sex',
            'family.civil',
            'family.relationship',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Model Scopes
    |--------------------------------------------------------------------------
    |
    | Scope functions to be used in the model.
    |
    */

    public function scopeIndex(
        $query,
        ?string $userId = null,
    ) {
        return $query->with([
            'application',
            'application.client',
            'application.client.user',
            'application.client.interviews',
            'application.assessment',
            'application.recommendations',
            'application.recommendations.funeralAssistanceType',
            'application.referral',
            'application.rejection',
            'application.cancellation',
            'religion',
        ])
            ->when($userId, function ($query) use ($userId) {
                $query->where('created_by', $userId);
            });
    }

    public function scopeTotal($query, ?string $startDate = null, ?string $endDate = null)
    {
        $user = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->roles()->exists()) {
            return $query->with('application')
                ->whereHas('application', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                });
        }

        return $query->with('application')
            ->whereHas('application', function ($query) use ($startDate, $endDate, $user) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
                $query->whereHas('client', function ($query) use ($user) {
                    $query->where('created_by', $user->id);
                });
            });
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

    public function scopePerAgeGroup(
        $query,
        ?string $startDate = null,
        ?string $endDate = null,
    ) {
        return $query
            ->with('application')
            ->whereHas('application', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw("
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, date_of_death) BETWEEN 0 AND 17 THEN '0-17'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, date_of_death) BETWEEN 18 AND 30 THEN '18-30'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, date_of_death) BETWEEN 31 AND 45 THEN '31-45'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, date_of_death) BETWEEN 46 AND 60 THEN '46-60'
                    ELSE '61+'
                END AS age_group,
                COUNT(*) AS total
            ")
            ->groupBy('age_group');
    }

    public function scopePerReligion(
        $query,
        ?string $startDate = null,
        ?string $endDate = null,
    ) {
        return $query
            ->with('application')
            ->whereHas('application')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date_of_death', [$startDate, $endDate]);
            })
            ->selectRaw('religion_id, COUNT(*) as total')
            ->with('religion')
            ->groupBy('religion_id');
    }

    public function scopePerNatality(
        $query,
        ?string $startDate = null,
        ?string $endDate = null,
    ) {
        return $query
            ->with('application')
            ->whereHas('application', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereRaw('TIMESTAMPDIFF(DAY, date_of_birth, date_of_death) BETWEEN 0 AND 28')
            ->selectRaw("
                CASE
                    WHEN TIMESTAMPDIFF(DAY, date_of_birth, date_of_death) BETWEEN 0 AND 7 THEN 'Perinatal Deaths'
                    WHEN TIMESTAMPDIFF(DAY, date_of_birth, date_of_death) BETWEEN 8 AND 28 THEN 'Neonatal Deaths'
                    ELSE ''
                END AS natality_group
            ")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('natality_group');
    }

    public function scopeOnlyPwd($query, ?string $startDate = null, ?string $endDate = null)
    {
        return $query
            ->where('pwd', 1)
            ->with('application')
            ->whereHas('application', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
    }
}
