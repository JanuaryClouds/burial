<?php

namespace App\Exports;

use App\Models\BurialAssistance;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Sheet;
use Carbon\Carbon;

class BurialAssistancesExportTemplate implements WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // Load the template file
                $templatePath = storage_path('app/templates/burial-assistances-template.xlsx');
                $spreadsheet = IOFactory::load($templatePath);

                // Write your data
                $sheet = $spreadsheet->getActiveSheet();
                $row = 4; // starting row

                foreach (BurialAssistance::with([
                    'claimant.relationship',
                    'claimant.barangay',
                    'deceased',
                    'deceased.gender',
                    'claimant',
                    'processLogs',
                    'claimantChanges',
                ])->get() as $ba) {
                    $sheet->setCellValue("A{$row}", $ba->tracking_no);
                    $sheet->setCellValue("B{$row}", $ba->application_date);
                    $sheet->setCellValue("C{$row}", $ba->swa);
                    $sheet->setCellValue("D{$row}", $ba->encoder);
                    $sheet->setCellValue("E{$row}", $ba->claimant->last_name);
                    $sheet->setCellValue("F{$row}", $ba->claimant->first_name);
                    $sheet->setCellValue("G{$row}", $ba->claimant?->middle_name);
                    $sheet->setCellValue("H{$row}", $ba->claimant?->suffix);
                    $sheet->setCellValue("I{$row}", $ba->deceased->last_name);
                    $sheet->setCellValue("J{$row}", $ba->deceased->first_name);
                    $sheet->setCellValue("K{$row}", $ba->deceased?->middle_name);
                    $sheet->setCellValue("L{$row}", $ba->deceased?->suffix);
                };

            },
        ];
    }
}
