<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Assistance;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\ModeOfAssistance;
use App\Models\SystemSetting;
use Barcode\Facades\Barcode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApplicationService
{
    public function index(
        ?string $userId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ) {
        return Application::index(
            $userId,
            $startDate,
            $endDate,
        )
            ->orderBy('tracking_no', 'desc')
            ->get()
            ->map(function (Application $application) {
                $client = $application->client;
                $beneficiary = $application->beneficiary;

                $status = $application->currentStatus();

                return [
                    'uuid' => $application->uuid,
                    'tracking_no' => $application->tracking_no,
                    'client' => $client->fullname(),
                    'beneficiary' => $beneficiary->fullname(),
                    'application_date' => $application->created_at->format('F d, Y'),
                    'status' => $status,
                    'show_route' => route('application.show', $application),
                ];
            });
    }

    /**
     * Summary of groupByStatus
     */
    public function groupByStatus(Collection $applications): Collection
    {
        return $applications
            ->map(function (Application $application) {
                return [
                    'uuid' => $application->uuid,
                    'status' => $application->currentSTatus()['label'],
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
     * Summary of groupByRelationship
     */
    public function groupByRelationship(Collection $applications): Collection
    {
        return $applications
            ->map(function (Application $application) {
                return [
                    'uuid' => $application->uuid,
                    'relationship' => $application->relationship->name,
                ];
            })
            ->groupBy('relationship')
            ->map(function ($collection, $key) {
                return [
                    'name' => ucfirst($key),
                    'count' => $collection->count(),
                ];
            })
            ->values();
    }

    /**
     * Summary of print
     */
    public function print(Application $application)
    {
        $client = $application->client;
        $beneficiary = $application->beneficiary;

        $client = Client::with([
            'user',
            'demographic',
            'demographic.sex',
            'demographic.religion',
            'demographic.nationality',
            'socialInfo',
            'socialInfo.civil',
            'socialInfo.education',
            'application.relationship',
            'barangay',
        ])->find($client->uuid);
        $family = $beneficiary->family()->with(['sex', 'relationship', 'civil'])->get() ?? [];
        $assessment = $application->assessment ?? null;
        $recommendation = $application->recommendations->first() ?? null;
        $referral = $application->referral ?? null;

        $data = [
            'client' => [
                [
                    '1. Client\'s Name' => $client->fullname(),
                    '2. Age' => $client->age(),
                    '3. Sex' => $client->demographic?->sex?->name ?? 'N/A',
                ],
                [
                    '4. Date of Birth' => Carbon::parse($client->date_of_birth)->format('F d, Y'),
                    '5. Present Address' => $client->fullAddress(),
                ],
                [
                    '6. Relationship to Beneficiary' => $client->application?->relationship?->name ?? 'N/A',
                    '7. Civil Status' => $client->socialInfo?->civil?->name ?? 'N/A',
                ],
                [
                    '8. Religion' => $client->demographic?->religion?->name ?? 'N/A',
                    '9. Nationality' => $client->demographic?->nationality?->name ?? 'N/A',
                    '10. Educational Attainment' => $client->socialInfo?->education?->name ?? 'N/A',
                ],
                [
                    '11. Skills/Occupation' => $client->socialInfo?->skill ?? 'N/A',
                    '12. Estimated Monthly Income' => $client->socialInfo?->income ?? 'N/A',
                ],
                [
                    '13. PhilHealth Number' => $client->socialInfo?->philhealth ?? 'N/A',
                    '14. Contact Number' => $client->contact_number ?? 'N/A',
                ],
            ],
            'beneficiary' => [
                [
                    '1. Beneficiary\'s Name' => $beneficiary?->fullname(),
                    '2. Sex' => $beneficiary?->sex?->name ?? 'N/A',
                ],
                [
                    '3. Date of Birth' => $beneficiary?->date_of_birth ? Carbon::parse($beneficiary->date_of_birth)->format('F d, Y') : 'N/A',
                    '4. Place of Birth' => $beneficiary?->fullAddress() ?? 'N/A',
                ],
            ],
            'assessment' => [
                'problem_presented' => $assessment?->problem_presented ?? 'N/A',
                'swa' => $assessment?->swa ?? 'N/A',
            ],
        ];

        $systemSetting = SystemSetting::first();
        $social_welfare_officer = Str::upper(Str::replace('_', ' ', $systemSetting?->social_welfare_officer ?? ''));
        $dept_head = Str::upper(Str::replace('_', ' ', $systemSetting?->dept_head ?? ''));

        $pdf = Pdf::loadView('pdf.gis-form', [
            'data' => $data,
            'client' => $client,
            'family' => $family,
            'assistances' => Assistance::pluck('name', 'id')->toArray(),
            'recommendation' => $recommendation,
            'moa' => ModeOfAssistance::all(),
            'referral' => $referral,
            'social_welfare_officer' => $social_welfare_officer,
            'dept_head' => $dept_head,
        ])
            ->setOption('margin-top', '0')
            ->setPaper('A4', 'portrait');

        return $pdf->stream("gis-form-{$application->tracking_no}.pdf");
    }

    public function getQrCodeUri(
        string $format,
        string $contents,
        int $size = 100,
        int $margin = 2,
        string $errorCorrection = 'H'
    ) {
        $qrCode = QrCode::format($format)
            ->size($size)
            ->margin($margin)
            ->errorCorrection($errorCorrection)
            ->generate($contents);

        return 'data:image/svg+xml;base64,'.base64_encode($qrCode);
    }

    public function getBarcodeUri(string $contents)
    {
        return Barcode::generateSvgBase64($contents);
    }

    public function codes(Application $application)
    {
        $pdf = Pdf::loadView('pdf.codes', [
            'application' => $application,
            'qrCode' => $this->getQrCodeUri(
                'svg',
                $application->qr_code,
                200
            ),
            'barcode' => $this->getBarcodeUri($application->qr_code),
        ])
            ->setOption('isRemoteEnabled', true)
            ->setPaper([0, 0, 250, 400], 'portrait');

        return $pdf->stream("codes-{$application->tracking_no}.pdf");
    }

    public function certificate(Application $application)
    {
        $pdf = Pdf::loadView('pdf.certificate', [
            'title' => $application->client->fullname()."'s Certificate",
            'client' => $application->client,
            'beneficiary' => $application->beneficiary,
            'deptHead' => SystemSetting::first()->dept_head,
            'socialWelfareOfficer' => SystemSetting::first()->social_welfare_officer,
        ])
            ->setOption('margin-top', '0')
            ->setPaper('A4', 'portrait');

        return $pdf->stream($application->tracking_no.'-certificate.pdf');
    }

    public function store(Client $client, Beneficiary $beneficiary)
    {
        return Application::create([
            'client_uuid' => $client->uuid,
            'beneficiary_uuid' => $beneficiary->uuid,
        ]);
    }
}
