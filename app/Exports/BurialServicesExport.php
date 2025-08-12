<?php

namespace App\Exports;

use App\Models\BurialService;
use Maatwebsite\Excel\Concerns\FromCollection;

class BurialServicesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return BurialService::all();
    }
}
