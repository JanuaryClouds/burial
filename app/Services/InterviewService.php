<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Interview;
use Exception;
use Str;

class InterviewService
{
    public function index(?int $user_id = null)
    {
        return Interview::with(['client', 'client.user'])
            ->when($user_id, function ($query) use ($user_id) {
                $query->whereHas('client', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            })
            ->orderBy('created_at', 'desc')    
            ->get()
            ->map(function ($interview) {
                return [
                    'id' => $interview->id,
                    'client' => $interview->client->fullname(),
                    'schedule' => $interview->schedule,
                    'status' => $interview->status
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
