<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory;

    protected $table = 'interviews';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'status',
        'schedule',
        'remarks',
    ];

    /**
     * Summary of client
     * @return BelongsTo<Client, Interview>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
