<?php

namespace App\Exports;

use App\Models\BurialAssistanceRequest;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class BurialAssistanceRequestsExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
    public function collection()
    {
        try {
            return BurialAssistanceRequest::with(['relationship', 'barangay'])->get();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function map($request): array
    {
        return [
            $request->uuid,
            $request->deceased_firstname,
            $request->deceased_lastname,
            $request->representative,
            $request->representative_phone,
            $request?->representative_email ?? '',
            $request->relationship?->name ?? 'Unknown',
            ($request->burial_address ?? '') . ' ' . ($request->barangay?->name ?? 'Unknown'),
            $request->start_of_burial ? ExcelDate::dateTimeToExcel(Carbon::parse($request->start_of_burial)) : null,
            $request->end_of_burial ? ExcelDate::dateTimeToExcel(Carbon::parse($request->end_of_burial)) : null,
            $request->status,
            $request->remarks,
            ExcelDate::dateTimeToExcel(Carbon::parse($request->created_at)),
            ExcelDate::dateTimeToExcel(Carbon::parse($request->updated_at)),
        ];
    }

    public function headings(): array
    {
        return [
            'UUID',
            'First Name',
            'Last Name',
            'Representative',
            'Contact Details',
            'Relationship',
            'Address',
            'Start of Burial',
            'End of Burial',
            'Status',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,            // UUID
            'E' => NumberFormat::FORMAT_TEXT,            // Contact
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,   // Start of Burial
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,   // End of Burial
            'L' => NumberFormat::FORMAT_DATE_DDMMYYYY,   // Created At
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,   // Updated At
        ];
    }
}
