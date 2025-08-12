<?php

namespace App\Exports;

use App\Models\burialAssistanceRequest;
use Maatwebsite\Excel\Concerns\FromCollection;

class BurialAssistanceRequestsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return burialAssistanceRequest::all();
    }
}
