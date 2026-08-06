<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'interviews';

    protected $fillable = [
        'client_uuid',
        'status',
        'schedule',
    ];

    /**
     * Summary of client
     *
     * @return BelongsTo<Client, Interview>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
