<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\InterviewRequest;
use App\Services\InterviewService;

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

                return redirect()->back()->with('alertSuccess', 'Interview created successfully.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('alertInfo', $e->getMessage());
        }
    }

    public function done(Request $request, $id)
    {
        try {
            $client = Client::find($id);
            $interview = $client->interviews->first();

            $interview = $this->interviewService->done($interview->id);
            if ($interview) {
                return redirect()->back()->with('alertSuccess', 'Interview marked as done.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('alertInfo', $e->getMessage());
        }
    }
}
