<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuneralAssistance extends Model
{
    use HasFactory;

    protected $table = 'funeral_assistances';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'approved_at',
        'forwarded_at',
        'remarks',
    ];

    /**
     * Summary of client
     *
     * @return BelongsTo<Client, FuneralAssistance>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * Summary of trackingNumber
     *
     * @return string returns the tracking number of the client
     */
    public function trackingNumber(): string
    {
        return $this->client->tracking_no;
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

        return $query->whereHas('client', function ($q) {
            $q->where('user_id', auth()->user()->id);
        });
    }

    public function scopeApproved($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->whereNotNull('approved_at');
        }

        return $query->whereHas('client', function ($q) {
            $q->where('user_id', auth()->user()->id);
        })->whereNotNull('approved_at');
    }

    public function scopeForwarded($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->whereNotNull('forwarded_at');
        }

        return $query->whereHas('client', function ($q) {
            $q->where('user_id', auth()->user()->id);
        })->whereNotNull('forwarded_at');
    }
}
