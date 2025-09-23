<?php

namespace App\Http\Controllers;

use App\Models\ClaimantChange;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use App\Models\BurialAssistance;
use App\Models\User;
use App\Exports\BurialAssistancesExport;
use App\Exports\BurialAssistancesExportTemplate;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function applications() {
        $templatePath = storage_path('app/templates/burial-assistances-template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);

        $sheet = $spreadsheet->getActiveSheet();
        $row = 4;

        function getLog($claimant, $order) {
            return $claimant->processLogs()
                ->whereHasMorph('loggable', [WorkflowStep::class], fn($q) => $q->where('order_no', $order))
                ->with('loggable')
                ->first();
        };

        foreach (BurialAssistance::with([
            'claimant.relationship',
            'claimant.barangay',
            'deceased',
            'deceased.gender',
            'claimant',
            'processLogs',
            'claimantChanges',
            'claimantChanges.oldClaimant',
            'claimantChanges.newClaimant',
        ])->get() as $ba) {
            $dob = Carbon::parse($ba->deceased->date_of_birth);
            $dod = Carbon::parse($ba->deceased->date_of_death);
            $encoder = User::find($ba->encoder);
            $initialChecker = User::find($ba->initial_checker);
            $age = $dob->diffInYears($dod);
            $approvedChange = $ba->claimantChanges->firstwhere('status', 'approved');
            if ($approvedChange) {
                $newClaimant = $approvedChange?->newClaimant;
                $firstClaimant = $approvedChange?->oldClaimant;
            } else {
                $firstClaimant = $ba->claimant;
            }

            $sheet->setCellValue("A{$row}", $ba->tracking_no);
            $sheet->setCellValue("B{$row}", $ba->application_date);
            $sheet->setCellValue("C{$row}", $ba->swa);
            $sheet->setCellValue("D{$row}", $encoder ? $encoder->first_name . ' ' . $encoder->last_name : '');
            $sheet->setCellValue("E{$row}", $firstClaimant->last_name);
            $sheet->setCellValue("F{$row}", $firstClaimant->first_name);
            $sheet->setCellValue("G{$row}", $firstClaimant?->middle_name);
            $sheet->setCellValue("H{$row}", $firstClaimant?->suffix);
            $sheet->setCellValue("I{$row}", $ba->deceased->last_name);
            $sheet->setCellValue("J{$row}", $ba->deceased->first_name);
            $sheet->setCellValue("K{$row}", $ba->deceased?->middle_name);
            $sheet->setCellValue("L{$row}", $ba->deceased?->suffix);
            $sheet->setCellValue("M{$row}", $firstClaimant->relationship->name);
            $sheet->setCellValue("N{$row}", $ba->deceased->date_of_birth);
            $sheet->setCellValue("O{$row}", $age);
            $sheet->setCellValue("P{$row}", $ba->deceased->gender == 1 ? 'M' : 'F');
            $sheet->setCellValue("Q{$row}", $firstClaimant->barangay->name);
            $sheet->setCellValue("R{$row}", $firstClaimant->address);
            $sheet->setCellValue("S{$row}", $ba->funeraria);
            $sheet->setCellValue("T{$row}", $ba->amount);
            $sheet->setCellValue("U{$row}", $ba->deceased->date_of_death);
            $sheet->setCellValue("V{$row}", $firstClaimant->mobile_number);
            $sheet->setCellValue("W{$row}", getLog($firstClaimant, 1)?->date_out ?? '');
            $sheet->setCellValue("X{$row}", getLog($firstClaimant, 1)?->date_in ?? '');
            $sheet->setCellValue("Y{$row}", getLog($firstClaimant, 1)?->comments ?? '');
            $sheet->setCellValue("Z{$row}", getLog($firstClaimant, 2)?->date_in ?? '');
            $sheet->setCellValue("AA{$row}", getLog($firstClaimant, 3)?->extra_data['compiled_docs'] ?? '');
            $sheet->setCellValue("AB{$row}", getLog($firstClaimant, 3)?->date_out ?? '');
            $sheet->setCellValue("AC{$row}", getLog($firstClaimant, 3)?->date_in ?? '');
            $sheet->setCellValue("AD{$row}", getLog($firstClaimant, 4)?->date_out ?? '');
            $sheet->setCellValue("AE{$row}", getLog($firstClaimant, 4)?->date_in ?? '');
            $sheet->setCellValue("AF{$row}", getLog($firstClaimant, 4)?->comments ?? '');
            $sheet->setCellValue("AG{$row}", getLog($firstClaimant, 5)?->date_out ?? '');
            $sheet->setCellValue("AH{$row}", getLog($firstClaimant, 5)?->date_in ?? '');
            $sheet->setCellValue("AI{$row}", getLog($firstClaimant, 6)?->date_out ?? '');
            $sheet->setCellValue("AJ{$row}", getLog($firstClaimant, 6)?->date_in ?? '');
            $sheet->setCellValue("AK{$row}", getLog($firstClaimant, 7)?->date_out ?? '');
            $sheet->setCellValue("AL{$row}", getLog($firstClaimant, 7)?->date_in ?? '');
            $sheet->setCellValue("AM{$row}", getLog($firstClaimant, 8)?->date_in ?? '');
            $sheet->setCellValue("AN{$row}", getLog($firstClaimant, 9)?->date_in ?? '');
            $sheet->setCellValue("AO{$row}", getLog($firstClaimant, 9)?->extra_data['OBR']['obr_number'] ?? '');
            $sheet->setCellValue("AP{$row}", getLog($firstClaimant, 9)?->extra_data['OBR']['date'] ?? '');
            $sheet->setCellValue("AQ{$row}", getLog($firstClaimant, 10)?->date_in ?? '');
            $sheet->setCellValue("AR{$row}", getLog($firstClaimant, 11)?->date_in ?? '');
            $sheet->setCellValue("AS{$row}", getLog($firstClaimant, 12)?->extra_data['cheque_number'] ?? '');
            $sheet->setCellValue("AT{$row}", getLog($firstClaimant, 12)?->extra_data['date'] ?? '');
            $sheet->setCellValue("AU{$row}", getLog($firstClaimant, 13)?->date_in ?? '');
            // Change of Claimants start here if it exists
            if ($approvedChange) {
                $sheet->setCellValue("AV{$row}", $newClaimant->last_name);
                $sheet->setCellValue("AW{$row}", $newClaimant->first_name);
                $sheet->setCellValue("AX{$row}", $newClaimant?->middle_name);
                $sheet->setCellValue("AY{$row}", $newClaimant?->suffix);
                $sheet->setCellValue("AZ{$row}", $approvedChange->reason_for_change);
                $sheet->setCellValue("BA{$row}", getLog($newClaimant, 4)?->date_out ?? '');
                $sheet->setCellValue("BB{$row}", getLog($newClaimant, 4)?->date_in ?? '');
                $sheet->setCellValue("BC{$row}", getLog($newClaimant, 4)?->comments ?? '');
                $sheet->setCellValue("BD{$row}", getLog($newClaimant, 5)?->date_out ?? '');
                $sheet->setCellValue("BE{$row}", getLog($newClaimant, 5)?->date_in ?? '');
                $sheet->setCellValue("BF{$row}", getLog($newClaimant, 6)?->date_out ?? '');
                $sheet->setCellValue("BG{$row}", getLog($newClaimant, 6)?->date_in ?? '');
                $sheet->setCellValue("BH{$row}", getLog($newClaimant, 7)?->date_out ?? '');
                $sheet->setCellValue("BI{$row}", getLog($newClaimant, 7)?->date_in ?? '');
                $sheet->setCellValue("BJ{$row}",
                    (getLog($newClaimant, 8)?->date_in ?? '') . ' / ' .
                    (getLog($newClaimant, 9)?->date_in ?? '') . ' / ' .
                    (getLog($newClaimant, 9)?->extra_data['OBR']['obr_number'] ?? '') . ' - ' .
                    (getLog($newClaimant, 9)?->extra_data['OBR']['date'] ?? '')
                );
            }
            $sheet->setCellValue("BK{$row}", $ba?->status ?? '');
            $sheet->setCellValue("BL{$row}", $ba?->remarks ?? '');
            $sheet->setCellValue("BM{$row}", $initialChecker ? $initialChecker->first_name . ' ' . $initialChecker->last_name : '');
            $row++;
        };

        $filename = 'burial-assistances-database-export-' . now()->format('YmdHis') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
