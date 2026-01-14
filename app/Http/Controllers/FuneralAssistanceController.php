<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\FuneralAssistance;
use App\Services\FuneralAssistanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Storage;
use Str;

class FuneralAssistanceController extends Controller
{
    protected $funeralAssistanceService;

    public function __construct(FuneralAssistanceService $funeralAssistanceService)
    {
        $this->funeralAssistanceService = $funeralAssistanceService;
    }

    public function index()
    {
        $page_title = 'Funeral Assistances';
        $resource = 'funeral-assistances';
        $renderColumns = ['client_id', 'action'];
        $data = FuneralAssistance::select('id', 'client_id', 'approved_at', 'forwarded_at')
            ->with('client')
            ->get();

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

        return view('funeral.index', compact('data', 'page_title', 'resource', 'renderColumns', 'cardData'));
    }

    public function show($id)
    {
        try {
            $data = FuneralAssistance::find($id);
            $client = $data->client;
            $page_title = Str::title($client->first_name).' '.Str::title($client->last_name);
            $page_subtitle = $client->tracking_no.' - '.$client->id;
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
            $funeralAssistance = FuneralAssistance::find($id);
            $funeralAssistance = $this->funeralAssistanceService->update($request->all(), $funeralAssistance);

            return redirect()->back()->with('success', 'Successfully updated Funeral Assistance.');
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

            return redirect()->back()->with('success', 'Successfully approved Funeral Assistance.');
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

            return redirect()->back()->with('success', 'Application for Funeral Assistance has been forwarded to Cemetery Staff.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function certificate($id)
    {
        $funeralAssistance = FuneralAssistance::find($id);
        $client = $funeralAssistance->client;
        $title = Str::title($client->first_name).' '.Str::title($client->last_name).'\'s Certification';

        $pdf = Pdf::loadView('pdf.certification',
            compact('client', 'title'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("certification-{$client->id}.pdf");
    }

    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $funeralAssistances = FuneralAssistance::select(
                'id',
                'client_id',
                'approved_at',
                'forwarded_at',
            )
                ->with('client')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

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
