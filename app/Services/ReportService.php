<?php

namespace App\Services;
use App\Models\Cheque;
use Carbon\Carbon;
use App\Models\BurialAssistance;
use App\Models\Deceased;
use App\Models\Claimant;
use App\Models\ProcessLog;
use App\Models\WorkflowStep;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Services\ProcessLogService;
use App\Models\User;

class ReportService 
{
    protected $processLogService;

    private function getLog($claimant, $order, $startDate = null, $endDate = null) {
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

    public function burialAssistanceReport($startDate, $endDate) {
        $templatePath = storage_path('app/templates/burial-assistances-template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $row = 4;

        $query = BurialAssistance::with([
            'claimant.relationship',
            'claimant.barangay',
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
            $sheet->setCellValue("W{$row}", $this->getLog($firstClaimant, 1, $startDate, $endDate)?->date_out);
            $sheet->setCellValue("X{$row}", $this->getLog($firstClaimant, 1, $startDate, $endDate)?->date_in);
            $sheet->setCellValue("Y{$row}", $this->getLog($firstClaimant, 1, $startDate, $endDate)?->comments);
            $sheet->setCellValue("Z{$row}", $this->getLog($firstClaimant, 2, $startDate, $endDate)?->date_in);
            $sheet->setCellValue("AA{$row}", $this->getLog($firstClaimant, 3, $startDate, $endDate)?->extra_data['compiled_docs'] ?? '');
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
            $sheet->setCellValue("AP{$row}", $this->getLog($firstClaimant, 12, $startDate, $endDate)?->extra_data['date_issued'] ?? '');
            $sheet->setCellValue("AQ{$row}", $this->getLog($firstClaimant, 10, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AR{$row}", $this->getLog($firstClaimant, 11, $startDate, $endDate)?->date_in ?? '');
            $sheet->setCellValue("AS{$row}", $this->getLog($firstClaimant, 12, $startDate, $endDate)?->extra_data['cheque_number'] ?? '');
            $sheet->setCellValue("AT{$row}", $this->getLog($firstClaimant, 12, $startDate, $endDate)?->extra_data['date'] ?? '');
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
                    ($this->getLog($newClaimant, 9, $startDate, $endDate)?->date_in ?? '') . ' / ' .
                    ($this->getLog($newClaimant, 8, $startDate, $endDate)?->date_in ?? '') . ' / ' .
                    ($this->getLog($newClaimant, 10, $startDate, $endDate)?->date_in ?? '')
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

    public function deceasedReport($startDate, $endDate) {
        $templatePath = storage_path('app/templates/deceased-persons-template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $row = 4;
        $deceased = Deceased::with([
            'gender',
            'religion',
        ])
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date_of_death', [$startDate, $endDate]);
        })->get();

        foreach ($deceased as $d) {
            $dob = Carbon::parse($d->date_of_birth);
            $dod = Carbon::parse($d->date_of_death);
            $age = $dob->diffInYears($dod);

            $sheet->setCellValue("A{$row}", $d->last_name);
            $sheet->setCellValue("B{$row}", $d->first_name);
            $sheet->setCellValue("C{$row}", $d->middle_name ?? '');
            $sheet->setCellValue("D{$row}", $d->suffix ?? '');
            $sheet->setCellValue("E{$row}", $d->date_of_birth);
            $sheet->setCellValue("F{$row}", $age);
            $sheet->setCellValue("G{$row}", $d->gender == 1 ? 'M' : 'F');
            $sheet->setCellValue("H{$row}", $d->religion->name);
            $sheet->setCellValue("I{$row}", $d->date_of_death);
            $row++;
        }

        $filename = 'deceased-persons-database-export-' . now()->format('YmdHis') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function deceasedPerMonth($startDate, $endDate) {
        return Deceased::selectRaw('YEAR(date_of_death) as year, MONTH(date_of_death) as month, COUNT(*) as total')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::create($item->year, $item->month)->format('F Y'),
                    'count'  => $item->total,
                ];
            });
    }
    
    public function deceasedPerWeek($startDate, $endDate) {
        return Deceased::selectRaw('YEAR(date_of_death) as year, WEEK(date_of_death, 1) as week, COUNT(*) as total')
        ->whereBetween('date_of_death', [$startDate, $endDate])
        ->groupBy('year', 'week')
        ->orderBy('year')
        ->orderBy('week')
        ->get()
        ->map(function ($item) {
            $startOfWeek = Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
            $endOfWeek = (clone $startOfWeek)->endOfWeek();

            return [
                'period' => $startOfWeek->format('M d, Y') . ' - ' . $endOfWeek->format('M d, Y'),
                'count'  => $item->total,
            ];
        });
    }

    public function deceasedPerDay($startDate, $endDate) {
        return Deceased::selectRaw('DATE(date_of_death) as day, COUNT(*) as total')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::parse($item->day)->format('M d, Y'),
                    'count'  => $item->total,
                ];
        });
    }

    public function deceasedPerBarangay($startDate, $endDate) {
        return Deceased::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->groupBy('barangay_id')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function deceasedPerReligion($startDate, $endDate) {
        return Deceased::selectRaw('religion_id, COUNT(*) as total')
            ->with('religion')
            ->groupBy('religion_id')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->religion->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function deceasedPerGender($startDate, $endDate) {
        return Deceased::selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->gender == 1 ? 'Male' : 'Female',
                    'count' => $item->total,
                ];
            });
    }

    public function claimantPerBarangay($startDate, $endDate) {
        return Claimant::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->groupBy('barangay_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function claimantPerRelationship($startDate, $endDate) {
        return Claimant::selectRaw('relationship_to_deceased, COUNT(*) as total')
            ->with('relationship')
            ->groupBy('relationship_to_deceased')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->relationship->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
    }

    public function chequesPerStatus($startDate, $endDate) {
        return Cheque::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->status,
                    'count' => $item->total,
                ];
            });
    }
}