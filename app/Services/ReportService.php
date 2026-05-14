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
                    'count' => (int) $item->getAttribute('total'),
                ];
            });
    }

    public function clientsPerAssistance($startDate, $endDate)
    {
        return ClientRecommendation::with('client')
            ->selectRaw('type, COUNT(*) as total')
            ->whereIn('type', ['libreng_libing', 'burial'])
            ->whereHas('client', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                $type = match ($item->type) {
                    'libreng_libing' => 'Libreng Libing',
                    'burial' => 'Burial Assistance',
                    default => 'Unknown',
                };

                return [
                    'name' => $type,
                    'count' => (int) $item->getAttribute('total'),
                ];
            });
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
                    'count' => (int) $item->getAttribute('total'),
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
                    'count' => (int) $item->getAttribute('total'),
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
                    'count' => (int) $item->getAttribute('total'),
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
                    'count' => (int) $item->getAttribute('total'),
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
                    'count' => (int) $item->getAttribute('total'),
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
                    'name' => (string) $item->getAttribute('status_group'),
                    'count' => (int) $item->getAttribute('total'),
                ];
            });
    }
}
