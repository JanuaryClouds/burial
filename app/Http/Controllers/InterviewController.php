<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewRequest;
use App\Models\Client;
use App\Models\Interview;
use App\Services\DatatableService;
use App\Services\InterviewService;
use Exception;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    protected $interviewServices;

    protected $datatableServices;

    public function __construct(InterviewService $interviewService, DatatableService $datatableService)
    {
        $this->interviewServices = $interviewService;
        $this->datatableServices = $datatableService;
    }

    public function index()
    {
        if (auth()->user()->roles()->count() == 0) {
            $user_id = auth()->user()->id;
        }
        $data = $this->interviewServices->index($user_id ?? null);

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
                // TODO send SMS message
                // Unavialable

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
                return redirect()->back()->with('success', 'Interview marked as done.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('info', $e->getMessage());
        }
    }
}
