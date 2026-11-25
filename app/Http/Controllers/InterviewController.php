<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterviewRequest;
use App\Models\Client;
use App\Models\Interview;
use App\Services\DataTableService;
use App\Services\InterviewService;
use App\Services\NotificationService;
use App\Services\SmsService;
use Exception;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct(
        protected InterviewService $interviewServices,
        protected DataTableService $dataTableServices,
        protected NotificationService $notificationServices,
        protected SmsService $smsServices
    ) {}

    public function index()
    {
        $pageTitle = 'Appointment Interviews';

        $personalData = $this->interviewServices->index(auth()->user()->id);
        $personalDataColumns = collect([['data' => 'schedule'], ['data' => 'status'], ['data' => 'remarks']]);

        $allData = [];
        $allDataColumns = [];

        if (auth()->user()->hasOfficeAccess()) {
            $allData = $this->interviewServices->index(null);
            $allDataColumns = collect([['data' => 'client'], ['data' => 'schedule'], ['data' => 'status'], ['data' => 'remarks']]);
        }

        if (request()->ajax()) {
            return response()->json([
                'personalData' => $personalData ? $personalData->values() : [],
                'allData' => $allData ? $allData->values() : [],
            ]);
        }

        return view('interview.index', compact(
            'pageTitle',
            'personalDataColumns',
            'personalData',
            'allDataColumns',
            'allData',
        ));
    }

    public function store(StoreInterviewRequest $request, Client $client)
    {
        try {
            $this->authorize('create', [Interview::class, $client]);

            $interview = $this->interviewServices->store($request->validated(), $client->uuid, $request->input('expected_assignment_uuid'));
            if ($interview) {
                return redirect()->back()->with('success', 'Interview created successfully.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('info', $e->getMessage());
        }
    }

    public function done(Request $request, $id)
    {
        try {
            $client = Client::find($id);
            $interview = $client->interviews->first();

            $interview = $this->interviewServices->done($interview->uuid);
            if ($interview) {
                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser, 'interview' => $interview->id])
                    ->log('Marked an interview as done');

                return redirect()->back()->with('success', 'Interview marked as done.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('info', $e->getMessage());
        }
    }
}
