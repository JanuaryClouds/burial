<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAssessment extends Model
{
    use HasFactory;

    protected $table = 'client_assessments';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'problem_presented',
        'assessment',
    ];

    public static function getClientAssessment($client)
    {
        return self::where('client_id', $client);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
