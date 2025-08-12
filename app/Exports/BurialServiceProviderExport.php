<?php

namespace App\Exports;

use App\Models\BurialServiceProvider;
use Maatwebsite\Excel\Concerns\FromCollection;

class BurialServiceProviderExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return BurialServiceProvider::all();
    }
}
