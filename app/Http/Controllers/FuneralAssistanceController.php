<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use App\Models\FuneralAssistance;
use App\Models\Client;
use App\Models\Sex;
use App\Models\Relationship;
use App\Models\CivilStatus;
use App\Models\Religion;
use App\Models\Nationality;
use App\Models\Education;
use App\Models\Assistance;
use App\Models\ModeOfAssistance;
use App\Models\Barangay;
use App\Models\District;
use Str;
use Crypt;
use Storage;

class FuneralAssistanceController extends Controller
{
    public function index() {
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

    public function view($id) {
        try {
            $data = FuneralAssistance::find($id);
            $client = $data->client;
            $page_title = Str::title($client->first_name) . ' ' . Str::title($client->last_name);
            $page_subtitle = $client->tracking_no . ' - ' . $client->id;
            $genders = Sex::select('id', 'name')->get()->pluck('name', 'id');
            $relationships = Relationship::select('id', 'name')->get()->pluck('name', 'id');
            $civilStatus = CivilStatus::select('id', 'name')->get()->pluck('name', 'id');
            $religions = Religion::select('id', 'name')->get()->pluck('name', 'id');
            $nationalities = Nationality::select('id', 'name')->get()->pluck('name', 'id');
            $educations = Education::select('id', 'name')->get()->pluck('name', 'id');
            $assistances = Assistance::select('id', 'name')->get()->pluck('name', 'id');
            $modes = ModeOfAssistance::select('id', 'name')->get()->pluck('name', 'id');
            $barangays = Barangay::select('id', 'name')->get()->pluck('name', 'id');
            $districts = District::select('id', 'name')->get()->pluck('name', 'id');
            $path = "clients/{$client->tracking_no}";
            $storedFiles = Storage::disk('local')->files($path);
            $files = [];

            foreach ($storedFiles as $storedFile) {
                // TODO: Use API to store images
                $encryptedFile = Storage::disk('local')->get($storedFile);
                $decryptedFile = Crypt::decrypt($encryptedFile);
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->buffer($decryptedFile);
                $files[] = [
                    'name' => basename($storedFile, '.enc'),
                    'path' => $storedFile,
                    'content' => $decryptedFile,
                    'mime' => $mime,
                ];
            }

            return view('funeral.view', compact(
                'data',
                'client',
                'page_title',
                'genders',
                'relationships',
                'civilStatus',
                'religions',
                'nationalities',
                'educations',
                'assistances',
                'modes',
                'barangays',
                'districts',
                'files'
            ));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve($id) {
        try {
            $data = FuneralAssistance::find($id);
            $data->approved_at = now();
            $data->save();
            return redirect()->back()->with('success', 'Successfully approved Funeral Assistance.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function forward($id) {
        try {
            $data = FuneralAssistance::find($id);
            $data->forwarded_at = now();
            $data->save();
            return redirect()->back()->with('success', 'Application for Funeral Assistance has been forwarded to Cemetery Staff.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function certificate($id) {
        $funeralAssistance = FuneralAssistance::find($id);
        $client = $funeralAssistance->client;
        $title = Str::title($client->first_name) . ' ' . Str::title($client->last_name) . '\'s Certification';
    
        $pdf = Pdf::loadView('pdf.certification', 
        compact('client', 'title'))
        ->setPaper('letter', 'portrait');
    
        return $pdf->stream("certification-{$client->id}.pdf");
    }

    public function generatePdfReport(Request $request, $startDate, $endDate) {
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
