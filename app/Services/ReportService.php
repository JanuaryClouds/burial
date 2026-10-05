<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\ClientRecommendation;
use App\Models\FuneralAssistance;
use Illuminate\Support\Collection;

class ReportService
{
    public function print(?string $startDate, ?string $endDate)
    {
        
    }

    /**
     * Summary of indexApplications
     * @param mixed $userId
     * @param mixed $startDate
     * @param mixed $endDate
     * @return Collection<int, array{PWD: string, age: string, beneficiary: string, client: string, "relationship_with_beneficiary": string, status: mixed, "tracking_number": string>|\Illuminate\Database\Eloquent\Collection<int, array{PWD: string, age: string, beneficiary: string, client: string, "relationship_with_beneficiary": string, status: mixed, "tracking_number": string}>}
     */
    public function indexApplications(
        ?int $userId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return Application::index($userId, $startDate, $endDate)
            ->orderBy('tracking_no', 'asc')
            ->get()
            ->map(function (Application $application) {
                return [
                    'tracking_number' => $application->tracking_no,
                    'client' => $application->client->fullname(),
                    'relationship_with_beneficiary' => $application->relationship->name,
                    'beneficiary' => $application->beneficiary->fullname(),
                    'age' => $application->beneficiary->age().' years old',
                    'PWD' => $application->beneficiary->pwd == 1 ? 'Yes' : 'No',
                    'status' => $application->currentStatus()['label'],
                ];
            });
    }

    /**
     * Summary of perStatus
     * @param mixed $startDate
     * @param mixed $endDate
     * @return Collection<int, array{count: int, name: string>|\Illuminate\Database\Eloquent\Collection<int, array{count: int, name: string}>}
     */
    public function perStatus(
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return Application::perStatus($startDate, $endDate)
            ->get()
            ->map(function (Application $application) {
                return [
                    'status' => $application->currentStatus()['label'],
                    'uuid' => $application->uuid,
                ];
            })
            ->groupBy('status')
            ->map(function ($collection, $key) {
                return [
                    'name' => ucfirst($key),
                    'count' => $collection->count(),
                ];
            })
            ->values();
    }

    /**
     * Summary of beneficiariesTotal
     * @param mixed $userId
     * @param mixed $startDate
     * @param mixed $endDate
     * @return int
     */
    public function beneficiariesTotal(
        ?int $userId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        return Beneficiary::index($userId)
            ->whereHas('application', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get()
            ->count();
    }

    /**
     * Summary of beneficiariesNatality
     * @param mixed $startDate
     * @param mixed $endDate
     * @return Collection<int, array{count: int, group: string>|\Illuminate\Database\Eloquent\Collection<int, array{count: int, group: string}>}
     */
    public function beneficiariesNatality(
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return Beneficiary::perNatality($startDate, $endDate)
            ->get()
            ->map(function ($item) {
                return [
                    'group' => (string) $item->natality_group,
                    'count' => (int) $item->total
                ];
            });
    }

    /**
     * Summary of beneficiaryAgeGroups
     * @param mixed $startDate
     * @param mixed $endDate
     * @return Collection<int, array{count: int, name: string>|\Illuminate\Database\Eloquent\Collection<int, array{count: int, name: string}>}
     */
    public function beneficiaryAgeGroups(
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return Beneficiary::perAgeGroup($startDate, $endDate)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => (string) $item->age_group,
                    'count' => (int) $item->total,
                ];
            });
    }

    /**
     * Summary of clientsPerRegion
     * @param mixed $startDate
     * @param mixed $endDate
     * @return Collection<int, array{count: mixed, "region_name": string>|\Illuminate\Database\Eloquent\Collection<int, array{count: mixed, "region_name": string}>}
     */
    public function clientsPerRegion(
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        return Client::perRegion($startDate, $endDate)
            ->get()
            ->map(function ($item) {
                return [
                    'region_name' => ucfirst($item->region_name),
                    'count' => $item->total,
                ];
            });
    }

    // public function clientsPerBarangay(string $startDate, string $endDate)
    // {
    //     return Client::selectRaw('barangay_id, COUNT(*) as total')
    //         ->with('barangay')
    //         ->groupBy('barangay_id')
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'name' => (string) $item->barangay->name,
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }

    // public function clientsPerAssistance(string $startDate, string $endDate)
    // {
    //     return ClientRecommendation::with('client')
    //         ->selectRaw('type, COUNT(*) as total')
    //         ->whereIn('type', ['libreng_libing', 'burial'])
    //         ->whereHas('client', function ($q) use ($startDate, $endDate) {
    //             $q->whereBetween('created_at', [$startDate, $endDate]);
    //         })
    //         ->groupBy('type')
    //         ->get()
    //         ->map(function ($item) {
    //             $type = match ($item->type) {
    //                 'libreng_libing' => 'Libreng Libing',
    //                 'burial' => 'Burial Assistance',
    //                 default => null,
    //             };

    //             return [
    //                 'name' => $type,
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }

    // public function deceasedPerBarangay(string $startDate, string $endDate)
    // {
    //     return Beneficiary::selectRaw('barangay_id, COUNT(*) as total')
    //         ->with('barangay')
    //         ->whereBetween('date_of_death', [$startDate, $endDate])
    //         ->whereHas('client.claimant')
    //         ->groupBy('barangay_id')
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'name' => (string) $item->barangay->name,
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }

    // public function deceasedPerReligion(string $startDate, string $endDate)
    // {
    //     return Beneficiary::selectRaw('religion_id, COUNT(*) as total')
    //         ->with('religion')
    //         ->whereBetween('date_of_death', [$startDate, $endDate])
    //         ->whereHas('client.claimant')
    //         ->groupBy('religion_id')
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'name' => (string) $item->religion->name,
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }

    // public function claimantPerBarangay(string $startDate, string $endDate)
    // {
    //     return Claimant::selectRaw('barangay_id, COUNT(*) as total')
    //         ->with('barangay')
    //         ->groupBy('barangay_id')
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'name' => (string) $item->barangay->name,
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }

    // public function claimantPerRelationship(string $startDate, string $endDate)
    // {
    //     return Claimant::selectRaw('relationship_to_deceased, COUNT(*) as total')
    //         ->with('relationship')
    //         ->groupBy('relationship_to_deceased')
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'name' => (string) $item->relationship->name,
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }

    // public function chequesPerStatus(string $startDate, string $endDate)
    // {
    //     return Cheque::selectRaw('status, COUNT(*) as total')
    //         ->groupBy('status')
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'name' => (string) $item->getAttribute('status'),
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }

    // public function funeralsPerStatus(string $startDate, string $endDate)
    // {
    //     return FuneralAssistance::selectRaw("
    //         CASE
    //             WHEN approved_at IS NOT NULL THEN 'Approved'
    //             WHEN forwarded_at IS NOT NULL THEN 'Forwarded'
    //             ELSE 'Pending'
    //         END AS status_group,
    //         COUNT(*) as total
    //     ")
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->groupBy('status_group')
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'name' => (string) $item->getAttribute('status_group'),
    //                 'count' => (int) $item->getAttribute('total'),
    //             ];
    //         });
    // }
}
