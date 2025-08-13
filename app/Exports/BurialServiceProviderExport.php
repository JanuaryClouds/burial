<?php

namespace App\Exports;

use App\Models\BurialServiceProvider;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class BurialServiceProviderExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return BurialServiceProvider::with(['barangay'])->get();
    }

    public function map($provider): array 
    {
        return [
            $provider->id,
            $provider->name,
            $provider->contact_details,
            $provider->address . ' ' . $provider->barangay?->name ?? '',
            $provider->remarks,
            ExcelDate::dateTimeToExcel(Carbon::parse($provider->created_at)),
            ExcelDate::dateTimeToExcel(Carbon::parse($provider->updated_at)),
        ];
    }

    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "Contact Details",
            "Address",
            "Remarks",
            "Created At",
            "Updated At",
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B'=> NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
