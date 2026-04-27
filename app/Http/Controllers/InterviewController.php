<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewRequest;
use App\Models\Client;
use App\Models\SystemSetting;
use App\Services\DatatableService;
use App\Services\InterviewService;
use App\Services\NotificationService;
use App\Services\SmsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InterviewController extends Controller
{
    protected $interviewServices;

    protected $datatableServices;

    protected $notificationServices;

    protected $smsServices;

    public function __construct(InterviewService $interviewService, DatatableService $datatableService, NotificationService $notificationServices, SmsService $smsServices)
    {
        $this->interviewServices = $interviewService;
        $this->datatableServices = $datatableService;
        $this->notificationServices = $notificationServices;
        $this->smsServices = $smsServices;
    }

    public function index()
    {
        $userId = auth()->user()->roles()->count() == 0 ? auth()->user()->id : null;

        $data = $this->interviewServices->index($userId);
        $columns = $this->datatableServices->getColumns($data, ['client']);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('interview.index', [
            'data' => $data,
            'columns' => $columns ?? [],
            'page_title' => 'Interviews',
        ]);
    }

    public function store(InterviewRequest $request, $id)
    {
        try {
            $interview = $this->interviewServices->store($request->validated(), $id);
            if ($interview) {
                $client = $interview->client;
                $citizen_uuid = $client->user?->citizen_uuid;
                if ($citizen_uuid) {
                    $this->notificationServices->send(
                        $citizen_uuid,
                        'interview',
                        'Interview Scheduled',
                        'You have an interview scheduled for '.Carbon::parse($interview->schedule)->format('F j, Y g:i A').
                        ' at the CSWDO Office in the Taguig City Hall at Gen. Luna St., Tuktukan, Taguig City. 
                        Please arrive earlier than scheduled time and bring hard copies of the required documents.'
                    );

                }

                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
                    ->performedOn($interview->client)
                    ->log('Created an interview');

                $department_email = SystemSetting::first()?->department_email;
                $date = Carbon::parse($interview->schedule)->format('F j, Y');
                $time = Carbon::parse($interview->schedule)->format('g:i A');

                $this->smsServices->send(
                    $client->contact_number,
                    'Magandang araw! '.$client->fullname().', Ikaw ay inaanyayahan para sa panayam kaungay ng inyong aplikasyon sa funeral assistance sa CSWDO ng Pamahalaang Lungsod ng Taguig.
                        Petsa: '.$date.', Oras: '.$time.', Lugar: CSWDO Office, Taguig City Hall. Sa address na: General Luna St., Tuktukan, Taguig City. 
                        Dalhin ang mga hard copy ng kinakailangan dokumento at dumating kayo bago sa itinakdang oras. Para sa karagdagang detalye, maaaring makipag-ugnayan sa '.$department_email.'. Maraming salamat po.'
                );

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

            $interview = $this->interviewServices->done($interview->id);
            if ($interview) {
                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
                    ->performedOn($interview)
                    ->log('Marked an interview as done');

                return redirect()->back()->with('success', 'Interview marked as done.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('info', $e->getMessage());
        }
    }
}
