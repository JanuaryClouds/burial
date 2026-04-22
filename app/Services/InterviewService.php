<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Interview;
use Carbon\Carbon;
use Exception;
use Str;

class InterviewService
{
    public function index(?int $userId = null)
    {
        return Interview::with(['client', 'client.user'])
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('client', function ($q) use ($userId) {
                    $q->where('userId', $userId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($interview) {
                return [
                    'id' => $interview->id,
                    'client' => $interview->client->fullname(),
                    'schedule' => $interview->scjhedule ? Carbon::parse($interview->schedule)->format('F j, Y g:i A') : null,
                    'status' => $interview->status,
                    'remarks' => $interview->remarks,
                ];
            });
    }

    public function store(array $data, $id)
    {
        try {
            $client = Client::findOrFail($id);
            $data['client_id'] = $client->id;
            $data['id'] = Str::uuid();

            return Interview::create($data);
        } catch (Exception $e) {
            return null;
        }
    }

    public function done($id)
    {
        try {
            $interview = Interview::findOrFail($id);
            $interview->update(['status' => 'done']);

            return $interview;
        } catch (Exception $e) {
            return redirect()->back()->with('info', $e->getMessage());
        }
    }
}
