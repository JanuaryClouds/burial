<?php

namespace App\Exports;

use App\Models\BurialService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Str;

class BurialServicesExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return BurialService::with(['relationship', 'barangay', 'provider'])->get();

    }

    public function map($service): array
    {
        return [
            $service->id,
            $service->deceased_firstname,
            $service->deceased_lastname,
            $service->representative,
            $service->representative_phone,
            $service?->representative_email ?? '',
            $service->relationship?->name ?? 'Unknown',
            $service->burial_address.' '.$service->barangay?->name ?? 'Unknown',
            $service->start_of_burial ? Carbon::parse($service->start_of_burial)->format('m-d-Y') : 'Unknown',
            $service->end_of_burial ? Carbon::parse($service->end_of_burial)->format('m-d-Y') : 'Unknown',
            $service->provider?->name ?? 'Unknown',
            'Php '.Str::substr($service->collected_funds, 1, 10),
            $service->remarks,
            Carbon::parse($service->created_at)->format('m-d-Y'),
            Carbon::parse($service->updated_at)->format('m-d-Y'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Representative',
            'Contact Details',
            'Relationship',
            'Address',
            'Start of Burial',
            'End of Burial',
            'Provider',
            'Collected Funds',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
