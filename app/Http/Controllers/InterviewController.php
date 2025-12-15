<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewRequest;
use App\Models\Client;
use App\Services\InterviewService;
use Exception;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    protected $interviewService;

    public function __construct(InterviewService $interviewService)
    {
        $this->interviewService = $interviewService;
    }

    public function store(InterviewRequest $request, $id)
    {
        try {
            $interview = $this->interviewService->store($request->validated(), $id);
            if ($interview) {
                // TODO: send SMS message

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

            $interview = $this->interviewService->done($interview->id);
            if ($interview) {
                return redirect()->back()->with('success', 'Interview marked as done.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('info', $e->getMessage());
        }
    }
}
