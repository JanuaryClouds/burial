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
use Storage;
use Str;

class FuneralAssistanceController extends Controller
{
    protected $funeralAssistanceServices;

    protected $datatableServices;

    protected $notificationServices;

    public function __construct(FuneralAssistanceService $funeralAssistanceService, DatatableService $datatableService, NotificationService $notificationService)
    {
        $this->funeralAssistanceServices = $funeralAssistanceService;
        $this->datatableServices = $datatableService;
        $this->notificationServices = $notificationService;
    }

    public function index()
    {
        $page_title = 'Libreng Libing Applications';

        if (auth()->user()->roles()->count() == 0) {
            $data = $this->funeralAssistanceServices->index(auth()->user()->id);
        } elseif (auth()->user()->roles()->count() > 0) {
            $data = $this->funeralAssistanceServices->index();
            $approvedApplications = FuneralAssistance::where('approved_at', '!=', null)->count();
            $forwardedApplications = FuneralAssistance::where('forwarded_at', '!=', null)->count();

            $cardData = [
                [
                    'label' => 'Total Applications',
                    'icon' => 'ki-document',
                    'pathsCount' => 2,
                    'count' => $data->count(),
                ],
                [
                    'label' => 'Approved Applications',
                    'icon' => 'ki-file-added',
                    'pathsCount' => 2,
                    'count' => $approvedApplications,
                ],
                [
                    'label' => 'Forwarded Applications',
                    'icon' => 'ki-file-right',
                    'pathsCount' => 2,
                    'count' => $forwardedApplications,
                ],
            ];
        }
        $columns = $this->datatableServices->getColumns($data);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('funeral.index', [
            'page_title' => $page_title,
            'data' => $data,
            'columns' => $columns,
            'cardData' => $cardData ?? null,
        ]);
    }

    public function show($id)
    {
        try {
            $data = FuneralAssistance::findOrFail($id);
            $client = $data->client;
            if (! $client) {
                return redirect()->back()->with('error', 'Client not found for this application.');
            }
            $page_title = $client->tracking_no;
            $page_subtitle = $client->fullname()."'s Funeral Assistance Application";
            $readonly = auth()->user()->cannot('manage-content') || $data?->forwarded_at != null;
            $path = "clients/{$client->tracking_no}";
            $storedFiles = Storage::disk('local')->files($path);
            $files = collect($storedFiles)->map(function ($file) {
                return [
                    'name' => basename($file),
                    'path' => $file,
                ];
            });

            return view('funeral.view', compact(
                'data',
                'client',
                'page_title',
                'page_subtitle',
                'readonly',
                'files'
            ));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(ClientRequest $request, $id)
    {
        try {
            $funeralAssistance = FuneralAssistance::findOrFail($id);
            $funeralAssistance = $this->funeralAssistanceServices->update($request->all(), $funeralAssistance);

            return redirect()->back()->with('success', 'Successfully updated Libreng Libing Application.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $data = FuneralAssistance::find($id);
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
        $title = Str::title($client->first_name).' '.Str::title($client->last_name).'\'s Certification';
        $systemSetting = SystemSetting::first();
        $social_welfare_officer = Str::upper(Str::replace('_', ' ', $systemSetting?->social_welfare_officer));
        $dept_head = Str::upper(Str::replace('_', ' ', $systemSetting?->dept_head));

        $pdf = Pdf::loadView('pdf.certification',
            compact([
                'client',
                'title',
                'social_welfare_officer',
                'dept_head',
            ]))
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
