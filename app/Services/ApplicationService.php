<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Assistance;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\ModeOfAssistance;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApplicationService
{
    public function index(?string $userId = null, string $orderBy = 'created_at', string $orderDirection = 'asc')
    {
        return Application::with([
            'client',
            'client.interviews',
            'client.user',
            'beneficiary',
            'assessment',
            'recommendations',
            'referral',
            'workflowStage',
        ])
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('client.user', function ($subQuery) use ($userId) {
                    $subQuery->where('id', $userId);
                });
            })
            ->orderBy($orderBy, $orderDirection)
            ->get()
            ->map(function (Application $application) {
                $client = $application->client;
                $beneficiary = $application->beneficiary;

                $status = $application->status();

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

    public function workflowState(Application $application): array
    {
        // This is the default state of newly submitted applications
        $state = [
            'canInterview' => null,
            'canAssess' => app()->isLocal() ? null : 'The client must be interviewed first',
            'canRefer' => 'This application cannot be referred without an assessment',
            'canRecommend' => 'This application cannot receive a recommendation without an assessment',
        ];

        if ($application->client->interviews->where('scheduled', '>', now())->isNotEmpty()) {
            $state['canInterview'] = 'The client has an upcoming interview.';
            $state['canAssess'] = 'The client must be interviewed first.';
            $state['canRefer'] = 'This application cannot be referred without an assessment';
            $state['canRecommend'] = 'This application cannot receive a recommendation without an assessment';
        } else {
            if ($application->client->interviews->where('status', 'done')->isNotEmpty()) {
                $state['canInterview'] = null;
            }

            $state['canAssess'] = 'The client must be interviewed first';
            $state['canRefer'] = 'This application cannot be referred without an assessment';
            $state['canRecommend'] = 'This application cannot receive a recommendation without an assessment';
        }

        if ($application->assessment) {
            $state['canInterview'] = 'This application already has an assessment';
            $state['canAssess'] = 'This applcation already has an assessment';
            $state['canRefer'] = null;
            $state['canRecommend'] = null;
        }

        if ($application->referral) {
            $state['canInterview'] = 'This application already has a referral';
            $state['canAssess'] = 'This application already has a referral';
            $state['canRefer'] = 'This application already has a referral';
            $state['canRecommend'] = 'This application already has a referral';
        }

        if ($application->recommendations->isNotEmpty()) {
            $state['canInterview'] = 'This application has already received a recommendation';
            $state['canAssess'] = 'This application already has a recommendation';

            if ($application->recommendations->where('status', ['approved', 'canceled'])->isNotEmpty()) {
                $state['canRefer'] = 'This application already has an approved or canceled recommendation';
                $state['canRecommend'] = 'This application already has an approved or canceled recommendation';
            } else {
                $state['canRefer'] = null;
                $state['canRecommend'] = null;
            }
        }

        return $state;
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
            'socialInfo.relationship',
            'socialInfo.civil',
            'socialInfo.education',
            'barangay',
        ])->find($client->uuid);
        $family = $beneficiary->family ?? [];
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
                    '5. Present Address' => $client->address(),
                ],
                [
                    '6. Relationship to Beneficiary' => $client->socialInfo?->relationship?->name ?? 'N/A',
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
                    '14. Contact Number' => $client->user?->contact_number ?? 'N/A',
                ],
            ],
            'beneficiary' => [
                [
                    '1. Beneficiary\'s Name' => $beneficiary?->fullname(),
                    '2. Sex' => $beneficiary?->sex?->name ?? 'N/A',
                ],
                [
                    '3. Date of Birth' => $beneficiary?->date_of_birth ? Carbon::parse($beneficiary->date_of_birth)->format('F d, Y') : 'N/A',
                    '4. Place of Birth' => $beneficiary?->place_of_birth ?? 'N/A',
                ],
            ],
            'assessment' => [
                'problem_presented' => $assessment?->problem_presented ?? 'N/A',
                'swa' => $assessment?->assessment ?? 'N/A',
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
