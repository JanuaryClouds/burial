<?php

namespace App\Services;

use App\Models\Interview;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class InterviewService
{
    public function index(?string $userId = null)
    {
        return Interview::with(['client', 'client.user'])
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('client.user', function ($q) use ($userId) {
                    $q->where('id', $userId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($interview) {
                return [
                    'id' => $interview->id,
                    'client' => $interview->client->fullname(),
                    'schedule' => $interview->schedule ? Carbon::parse($interview->schedule)->format('F j, Y g:i A') : null,
                    'status' => $interview->status,
                    'remarks' => $interview->remarks,
                ];
            });
    }

    /**
     * Summary of store
     */
    public function store(array $data, string $clientUuid): Interview
    {
        $data['client_uuid'] = $clientUuid;

        $interview = Interview::create($data);

        activity()
            ->withProperties([
                'client_uuid' => $clientUuid,
                'interview_uuid' => $interview->uuid,
                'schedule' => $interview->schedule,
                'ip' => request()->ip(),
                'browser' => request()->userAgent(),
            ])
            ->causedBy(Auth::user()->id)
            ->log('Scheduled an interview with the client.');

        return $interview;
    }

    public function done($id)
    {
        try {
            $interview = Interview::findOrFail($id);
            $interview->update(['status' => 'done']);

            return $interview;
        } catch (Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
