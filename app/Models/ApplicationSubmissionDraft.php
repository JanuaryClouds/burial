<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, array{storage: string, folder: string, file_name: string}>|null $documents
 * @property int $version
 * @property int $relationship_id
 */
class ApplicationSubmissionDraft extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $hidden = ['documents'];

    protected function casts(): array
    {
        return ['documents' => 'encrypted:array', 'version' => 'integer'];
    }
}
