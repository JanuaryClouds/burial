<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function trackingNumber()
    {
        return $this->client?->tracking_no;
    }

    public function tracker()
    {
        return $this->hasOne(TrackingCode::class, 'trackable_id', 'id');
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
