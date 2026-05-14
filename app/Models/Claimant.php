<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Claimant extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'burial_assistance_id',
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'address',
        'barangay_id',
        'city',
        'contact_number',
        'relationship_id',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
        'address' => 'encrypted',
        'contact_number' => 'encrypted',
    ];

    protected $table = 'claimants';

    /**
     * Summary of fullname
     * @return string Returns the full name
     */
    public function fullname(): string
    {
        return $this->first_name.' '.
            ($this->middle_name ? Str::limit($this->middle_name, 1, '.').' ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '');
    }

    /**
     * Summary of age
     * @return int age of the claimant, used in certificates
     */
    public function age(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Summary of fullAddress
     * @return string joins the house no., street name, and barangay name
     */
    public function fullAddress(): string
    {
        return $this->address.', '.$this->barangay?->name;
    }

    /**
     * Summary of client
     * @return BelongsTo<Client, Claimant>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * Summary of burialAssistance
     * @return BelongsTo<BurialAssistance, Claimant>
     */
    public function burialAssistance(): BelongsTo
    {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of relationship
     * @return BelongsTo<Relationship, Claimant>
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class, 'relationship_id', 'id');
    }

    /**
     * Summary of barangay
     * @return BelongsTo<Barangay, Claimant>
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    /**
     * Summary of processLogs
     * @return HasMany<ProcessLog>
     */
    public function processLogs(): HasMany
    {
        return $this->hasMany(ProcessLog::class, 'claimant_id', 'id');
    }

    /**
     * Summary of cheque
     * @return HasOne<Cheque>
     */
    public function cheque(): HasOne
    {
        return $this->hasOne(Cheque::class, 'claimant_id', 'id')->latestOfMany();
    }
}
