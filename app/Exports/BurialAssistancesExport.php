<?php

namespace App\Exports;

use App\Models\BurialAssistance;
use App\Models\Claimants;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;

class BurialAssistancesExport implements FromCollection, WithStyles, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return BurialAssistance::with([
            'claimant.relationship',
            'claimant.barangay',
            'deceased',
            'deceased.gender',
            'claimant',
            'processLogs',
            'claimantChanges',
        ])
            ->get()
            ->map(function($app){
                $dob = Carbon::parse($app->deceased->date_of_birth);
                $dod = Carbon::parse($app->deceased->date_of_death);

                $age = $dob->diffInYears($dod);
                return [
                    $app->tracking_no,
                    $app->application_date,
                    $app->swa,
                    $app->encoder,
                    $app->claimant->last_name,
                    $app->claimant->first_name,
                    $app->claimant?->middle_name,
                    $app->claimant?->suffix,
                    $app->deceased->last_name,
                    $app->deceased->first_name,
                    $app->deceased?->middle_name,
                    $app->deceased?->suffix,
                    $app->claimant->relationship->name,
                    $app->deceased->date_of_birth,
                    $age,
                    $app->deceased->gender == 1 ? 'Male' : 'Female',
                    $app->claimant->barangay->name,
                    $app->claimant->address,
                    $app->funeraria,
                    $app->amount,
                    $app->deceased->date_of_death,
                    $app->claimant->mobile_number,
                ];
            });
    }

    public function headings(): array {
        return [
            array_merge(
                [
                    'Tracking No.', // A1:A3
                    'Date of Application', // B1:B3
                    'Worker / SWA (Social Worker Assessment)', // C1:C3
                    'ENCODED BY', // D1:D3
                    'CLAIMANT', // E1:H2
                ],
                array_fill(0, 3, ''),
                ['DECEASED'], // I1:L2,
                array_fill(0, 3, ''),
                [
                    'RELATIONSHIP OF DECEASED TO THE CLAIMANT',
                    'DATE OF BIRTH',
                    'AGE OF DECEASED',
                    'GENDER',
                    'BARANGAY',
                    'ADDRESS',
                    'FUNERARIA',
                    'AMOUNT',
                    'DATE OF DEATH',
                    'MOBILE NUMBER',
                    'Ms. Maricar',
                ],
                array_fill(0, 3,''),
                [
                    'Admin Staff',
                    'Out of Worker (Compliance)',
                ],
                array_fill(0, 3,''),
                ['Ms, Maricar'],
                array_fill(0,4,''),
                [
                    'Ms. Emma', 
                    '',
                    'Ms. Nikki',
                    '',
                    'Date forwarded to BAO',
                    'BUDGET',
                ],
                array_fill(0,3,''),
                [
                    'Accounting',
                    'Treasury',
                    'Availability of Cheque',
                    '',
                    'Date Claimed',
                    'Change Claimant',
                ],
                array_fill(0,24,''),
                [
                    'STATUS',
                    'Remarks',
                    'INITIAL CHECKING BY'
                ],
            ),
            [
                '',
                'Date Out'
            ],
            [
                '',
                '',
                '',
                '',
                'LAST NAME',
                'GIVEN NAME',
                'MIDDLE NAME',
                'EXT',
                'LAST NAME',
                'GIVEN NAME',
                'MIDDLE NAME',
                'EXT',
            ]
        ];
    }

    public function styles(Worksheet $sheet) {
        $sheet->getStyle('A:BM')
            ->getFont()
            ->setName('Book Antiqua');
        
        $sheet->getStyle('A1:BM3')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('f4b084');
        $sheet->getStyle('A1:BM3')
            ->getFont()
            ->setBold(true);
        $sheet->getStyle('A:BM')
            ->getAlignment()
            ->setWrapText(true);
        $sheet->getStyle('A:BM')
            ->getAlignment()
            ->setVertical('center');
        $sheet->getStyle('A:BM')
            ->getAlignment()
            ->setHorizontal('center');
        $sheet->getStyle('A1:BM3')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('medium');

        for ($i = 1; $i <= 65; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setWidth(20);
        }

        foreach (range('E', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->mergeCells('A1:A3');
        $sheet->mergeCells('B1:B3');
        $sheet->mergeCells('C1:C3');
        $sheet->mergeCells('D1:D3');
        $sheet->mergeCells('E1:H2');
        $sheet->mergeCells('I1:L2');
        $sheet->mergeCells('M1:M3');
        $sheet->mergeCells('N1:N3');
        $sheet->mergeCells('O1:O3');
        $sheet->mergeCells('P1:P3');
        $sheet->mergeCells('Q1:Q3');
        $sheet->mergeCells('R1:R3');
        $sheet->mergeCells('S1:S3');
        $sheet->mergeCells('T1:T3');
        $sheet->mergeCells('U1:U3');
        $sheet->mergeCells('V1:V3');
        $sheet->mergeCells('W1:Y1');
        $sheet->mergeCells('W1:Y1');
        $sheet->mergeCells('X2:Y3');
        $sheet->mergeCells('Y2:Y3');
        $sheet->mergeCells('Z2:Z3');
        $sheet->mergeCells('AA1:AC1');
        $sheet->mergeCells('AA2:AC3');
        $sheet->mergeCells('AB2:AB3');
        $sheet->mergeCells('AC2:AC3');
        $sheet->mergeCells('AD2:AD3');
        $sheet->mergeCells('AE2:AE3');
        $sheet->mergeCells('AF2:AF3');
        $sheet->mergeCells('AG2:AG3');
        $sheet->mergeCells('AH2:AH3');
        $sheet->mergeCells('AI2:AI3');
        $sheet->mergeCells('AJ2:AJ3');
        $sheet->mergeCells('AK2:AK3');
        $sheet->mergeCells('AL2:AL3');
        $sheet->mergeCells('AI1:AJ1');
        $sheet->mergeCells('AK1:AL1');
        $sheet->mergeCells('AM1:AM3');
        $sheet->mergeCells('AM1:AM3');
        
    }
}
