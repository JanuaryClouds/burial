<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\FuneralAssistance;
use App\Models\SystemSetting;
use App\Services\DatatableService;
use App\Services\FuneralAssistanceService;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FuneralAssistanceController extends Controller
{
    protected FuneralAssistanceService $funeralAssistanceServices;

    protected DatatableService $datatableServices;

    protected NotificationService $notificationServices;

    public function __construct(FuneralAssistanceService $funeralAssistanceService, DatatableService $datatableService, NotificationService $notificationService)
    {
        $this->funeralAssistanceServices = $funeralAssistanceService;
        $this->datatableServices = $datatableService;
        $this->notificationServices = $notificationService;
    }

    public function index()
    {
        $page_title = 'Libreng Libing Applications';
        $personalData = $this->funeralAssistanceServices->index(auth()->user()->id);
        $personalDataColumns = $this->datatableServices->getColumns($personalData);

        $allData = [];
        $allDataColumns = [];
        $cardData = [];
        if (auth()->user()->hasRole('staff')) {
            $allData = $this->funeralAssistanceServices->index();
            $allDataColumns = $this->datatableServices->getColumns($allData);
            $cardData = [
                [
                    'model' => 'App\Models\FuneralAssistance',
                    'label' => 'Total Applications',
                    'scope' => 'Total',
                    'iconName' => 'some-files',
                    'iconPathsCount' => 2,
                ],
                [
                    'model' => 'App\Models\FuneralAssistance',
                    'label' => 'Approved Applications',
                    'scope' => 'Approved',
                    'iconName' => 'file-added',
                    'iconPathsCount' => 2,
                ],
                [
                    'model' => 'App\Models\FuneralAssistance',
                    'label' => 'Forwarded Applications',
                    'scope' => 'Forwarded',
                    'iconName' => 'file-right',
                    'iconPathsCount' => 2,
                ],
            ];
        }

        if (request()->expectsJson()) {
            return response()->json([
                'personalData' => $personalData ? $personalData->values() : [],
                'allData' => $allData ? $allData->values() : [],
            ]);
        }

        return view('funeral.index', compact(
            'page_title',
            'personalData',
            'personalDataColumns',
            'allData',
            'allDataColumns',
            'cardData'
        ));
    }

    public function show($id)
    {
        try {
            $data = FuneralAssistance::findOrFail($id);
            $this->authorize('view', $data);
            $client = $data->client;
            if (! $client) {
                return redirect()->back()->with('error', 'Client not found for this application.');
            }
            $page_title = $client->tracking_no;
            $page_subtitle = $client->fullname()."'s Funeral Assistance Application";
            $readonly = ! auth()->user()->hasRole('superadmin') || $data?->forwarded_at != null;

            return view('funeral.view', compact(
                'data',
                'client',
                'page_title',
                'page_subtitle',
                'readonly',
            ));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(ClientRequest $request, $id)
    {
        try {
            $funeralAssistance = FuneralAssistance::findOrFail($id);
            $this->authorize('update', $funeralAssistance);
            $funeralAssistance = $this->funeralAssistanceServices->update($request->all(), $funeralAssistance);

            return redirect()->back()->with('success', 'Successfully updated Libreng Libing Application.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $data = FuneralAssistance::findOrFail($id);
            $this->authorize('update', $data);
            $data->approved_at = now();
            $data->save();

            return redirect()->back()->with('success', 'Successfully approved Libreng Libing Application.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function forward($id)
    {
        try {
            $data = FuneralAssistance::find($id);
            $this->authorize('update', $data);
            $data->forwarded_at = now();
            $data->save();

            $citizenUuid = $data->client?->user?->citizen_uuid;
            if ($citizenUuid) {
                $this->notificationServices->send(
                    $citizenUuid,
                    'libreng_libing',
                    'Libreng Libing Forwarded to Cemetery Staff',
                    'Your application for Libreng Libing has been forwarded to Cemetery Staff.'
                );
            }

            return redirect()->back()->with('success', 'Application for Libreng Libing has been forwarded to Cemetery Staff.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function certificate($id)
    {
        $funeralAssistance = FuneralAssistance::findOrFail($id);
        $client = $funeralAssistance->client;
        $title = Str::title($client->fullname()).'\'s Certification';
        $systemSetting = SystemSetting::first();
        $social_welfare_officer = Str::upper($systemSetting?->social_welfare_officer);
        $dept_head = Str::upper($systemSetting?->dept_head);

        $pdf = Pdf::loadView('pdf.certification',
            compact(
                'client',
                'title',
                'social_welfare_officer',
                'dept_head',
            ))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("certification-{$client->id}.pdf");
    }

    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $funeralAssistances = $this->funeralAssistanceServices->reportIndex($startDate, $endDate);

            $charts = $request->input('charts', []);
            $pdf = Pdf::loadView('pdf.funeral-assistance', compact(
                'funeralAssistances',
                'startDate',
                'endDate',
                'charts',
            ))
                ->setPaper('letter', 'portrait');

            return $pdf->stream("funeral-assistance-report-{$startDate}-{$endDate}.pdf");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
