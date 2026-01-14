<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Interview;
use Exception;
use Str;

class InterviewService
{
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
