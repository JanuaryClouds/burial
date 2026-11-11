<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationDocumentFile extends Model
{
    protected $fillable = ['application_uuid', 'document_requirement_id', 'storage', 'folder', 'file_name'];
}
