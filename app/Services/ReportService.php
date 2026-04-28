<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\BurialAssistance;
use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\ClientRecommendation;
use App\Models\FuneralAssistance;
use App\Models\User;
use App\Models\WorkflowStep;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    protected $processLogService;

    private function getLog($claimant, $order, $startDate = null, $endDate = null)
    {
        return $claimant->processLogs()
            ->whereHasMorph(
                'loggable',
                [WorkflowStep::class],
                // fn($q) => $q->where('order_no', $order)
                function ($q) use ($order, $startDate, $endDate) {
                    $q->where('order_no', $order);
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                }
            )
            ->with('loggable')
            ->first();
    }

    public function clientsPerBarangay($startDate, $endDate)
    {
        return Client::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->groupBy('barangay_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function clientsPerAssistance($startDate, $endDate)
    {
        return ClientRecommendation::selectRaw('type, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->type,
                    'count' => $item->total,
                ];
            });
    }

    public function burialAssistanceReport($startDate, $endDate)
    {
        $templatePath = storage_path('app/templates/burial-assistances-template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $row = 4;

        $query = BurialAssistance::with([
            'claimant.relationship',
            'claimant.barangay',
            'claimant.client',
            'claimant.client.beneficiary',
            'deceased',
            'deceased.gender',
            'claimant',
            'processLogs',
            'claimantChanges',
            'claimantChanges.oldClaimant',
            'claimantChanges.newClaimant',
        ]);

        $query->orderBy('created_at', 'asc');

        $burialAssistances = $query->get();

        foreach ($burialAssistances as $ba) {
            $beneficiary = $ba->beneficiary();
            $dobRaw = $beneficiary?->date_of_birth;
            $dodRaw = $beneficiary?->date_of_death;
            $dob = $dobRaw ? Carbon::parse($dobRaw) : null;
            $dod = $dodRaw ? Carbon::parse($dodRaw) : null;
            $encoder = User::find($ba->encoder);
            $initialChecker = User::find($ba->initial_checker);
            $age = ($dob && $dod) ? $dob->diffInYears($dod) : null;
            $approvedChange = $ba->claimantChanges->firstwhere('status', 'approved');
            if ($approvedChange) {
                $newClaimant = $approvedChange?->newClaimant;
                $firstClaimant = $approvedChange?->oldClaimant;
            } else {
                $firstClaimant = $ba->originalClaimant();

                if (! $firstClaimant) {
                    $row++;

                    continue;
                }
            }

            $sheet->setCellValue("A{$row}", $ba->originalClaimant()?->client?->tracking_no);
            $sheet->setCellValue("B{$row}", $ba->application_date);
            $sheet->setCellValue("C{$row}", $ba->swa);
            $sheet->setCellValue("D{$row}", $encoder ? $encoder->first_name.' '.$encoder->last_name : '');
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
            $sheet->setCellValue("V{$row}", $firstClaimant->contact_number);
            $sheet->setCellValue("W{$row}", $this->getLog($firstClaimant, 1, $startDate, $endDate)?->date_out);
            $sheet->setCellValue("X{$row}", $this->getLog($firstClaimant, 1, $startDate, $endDate)?->date_in);
            $sheet->setCellValue("Y{$row}", $this->getLog($firstClaimant, 1, $startDate, $endDate)?->comments);
            $sheet->setCellValue("Z{$row}", $this->getLog($firstClaimant, 2, $startDate, $endDate)?->date_in);
            $sheet->setCellValue("AA{$row}", $this->getLog($firstClaimant, 3, $startDate, $endDate)?->extra_data['compiled_documents'] ?? '');
            $sheet->setCellValue("AB{$row}", $this->getLog($firstClaimant, 3, $startDate, $endDate)?->date_out ?? '');
            $sheet->setCellValue("AC{$row}", $this->getLog($firstClaimant, 3, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AD{$row}", $this->getLog($firstClaimant, 4, $startDate, $endDate)?->date_out ?? '');
            $sheet->setCellValue("AE{$row}", $this->getLog($firstClaimant, 4, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AF{$row}", $this->getLog($firstClaimant, 4, $startDate, $endDate)?->comments ?? '');
            $sheet->setCellValue("AG{$row}", $this->getLog($firstClaimant, 5, $startDate, $endDate)?->date_out ?? '');
            $sheet->setCellValue("AH{$row}", $this->getLog($firstClaimant, 5, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AI{$row}", $this->getLog($firstClaimant, 6, $startDate, $endDate)?->date_out ?? '');
            $sheet->setCellValue("AJ{$row}", $this->getLog($firstClaimant, 6, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AK{$row}", $this->getLog($firstClaimant, 7, $startDate, $endDate)?->date_out ?? '');
            $sheet->setCellValue("AL{$row}", $this->getLog($firstClaimant, 7, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AM{$row}", $this->getLog($firstClaimant, 8, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AN{$row}", $this->getLog($firstClaimant, 9, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AO{$row}", $this->getLog($firstClaimant, 9, $startDate, $endDate)?->extra_data['OBR']['oBR_number'] ?? '');
            $sheet->setCellValue("AP{$row}", $this->getLog($firstClaimant, 9, $startDate, $endDate)?->extra_data['OBR']['date'] ?? '');
            $sheet->setCellValue("AQ{$row}", $this->getLog($firstClaimant, 10, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AR{$row}", $this->getLog($firstClaimant, 11, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AS{$row}", $this->getLog($firstClaimant, 11, $startDate, $endDate)?->extra_data['cheque_number'] ?? '');
            $sheet->setCellValue("AT{$row}", $this->getLog($firstClaimant, 12, $startDate, $endDate)?->extra_data['date_issued'] ?? '');
            $sheet->setCellValue("AU{$row}", $this->getLog($firstClaimant, 13, $startDate, $endDate)?->date_in ?? '');
            // Change of Claimants start here if it exists
            if ($approvedChange) {
                $sheet->setCellValue("AV{$row}", $newClaimant->last_name);
                $sheet->setCellValue("AW{$row}", $newClaimant->first_name);
                $sheet->setCellValue("AX{$row}", $newClaimant?->middle_name);
                $sheet->setCellValue("AY{$row}", $newClaimant?->suffix);
                $sheet->setCellValue("AZ{$row}", $approvedChange->reason_for_change);
                $sheet->setCellValue("BA{$row}", $this->getLog($newClaimant, 4, $startDate, $endDate)?->date_out ?? '');
                $sheet->setCellValue("BB{$row}", $this->getLog($newClaimant, 4, $startDate, $endDate)?->date_in ?? '');
                $sheet->setCellValue("BC{$row}", $this->getLog($newClaimant, 4, $startDate, $endDate)?->comments ?? '');
                $sheet->setCellValue("BD{$row}", $this->getLog($newClaimant, 5, $startDate, $endDate)?->date_out ?? '');
                $sheet->setCellValue("BE{$row}", $this->getLog($newClaimant, 5, $startDate, $endDate)?->date_in ?? '');
                $sheet->setCellValue("BF{$row}", $this->getLog($newClaimant, 6, $startDate, $endDate)?->date_out ?? '');
                $sheet->setCellValue("BG{$row}", $this->getLog($newClaimant, 6, $startDate, $endDate)?->date_in ?? '');
                $sheet->setCellValue("BH{$row}", $this->getLog($newClaimant, 7, $startDate, $endDate)?->date_out ?? '');
                $sheet->setCellValue("BI{$row}", $this->getLog($newClaimant, 7, $startDate, $endDate)?->date_in ?? '');
                $sheet->setCellValue("BJ{$row}",
                    ($this->getLog($newClaimant, 9, $startDate, $endDate)?->date_in ?? '').' / '.
                    ($this->getLog($newClaimant, 8, $startDate, $endDate)?->date_in ?? '').' / '.
                    ($this->getLog($newClaimant, 10, $startDate, $endDate)?->date_in ?? '')
                );
            }
            $sheet->setCellValue("BK{$row}", $ba?->status ?? '');
            $sheet->setCellValue("BL{$row}", $ba?->remarks ?? '');
            $sheet->setCellValue("BM{$row}", $initialChecker ? $initialChecker->first_name.' '.$initialChecker->last_name : '');
            $row++;
        }

        $filename = 'burial-assistances-database-export-'.now()->format('YmdHis').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function deceasedPerBarangay($startDate, $endDate)
    {
        return Beneficiary::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->whereHas('client.claimant')
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function deceasedPerReligion($startDate, $endDate)
    {
        return Beneficiary::selectRaw('religion_id, COUNT(*) as total')
            ->with('religion')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->whereHas('client.claimant')
            ->groupBy('religion_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->religion->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function claimantPerBarangay($startDate, $endDate)
    {
        return Claimant::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->groupBy('barangay_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function claimantPerRelationship($startDate, $endDate)
    {
        return Claimant::selectRaw('relationship_to_deceased, COUNT(*) as total')
            ->with('relationship')
            ->groupBy('relationship_to_deceased')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->relationship->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function chequesPerStatus($startDate, $endDate)
    {
        return Cheque::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->status,
                    'count' => $item->total,
                ];
            });
    }

    public function funeralsPerStatus($startDate, $endDate)
    {
        return FuneralAssistance::selectRaw("
            CASE
                WHEN approved_at IS NOT NULL THEN 'Approved'
                WHEN forwarded_at IS NOT NULL THEN 'Forwarded'
                ELSE 'Pending'
            END AS status_group,
            COUNT(*) as total
        ")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status_group')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->status_group,
                    'count' => $item->total,
                ];
            });
    }
}
