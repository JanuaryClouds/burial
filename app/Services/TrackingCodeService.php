<?php

namespace App\Services;

use App\Models\BurialAssistance;
use App\Models\Client;
use App\Models\TrackingCode;
use Illuminate\Support\Str;

class TrackingCodeService
{
    public function create(array $data)
    {
        return TrackingCode::create($data);
    }

    /**
     * Summary of match
     *
     * @param  string  $code  tracking code generated per assistance
     * @param  string  $tracking_no  tracking number of the client related to the assistance
     * @return bool if the code matches the tracking no
     */
    public function match(string $code, string $tracking_no)
    {
        $client = Client::where('tracking_no', $tracking_no)->first();
        if ($client === null) {
            return false;
        }

        $parsed_code = Str::upper(Str::replace('-', '', $code));
        $tracking_code = TrackingCode::where('code', $parsed_code)->first();
        if ($tracking_code === null) {
            return false;
        }

        $assistance = $tracking_code->trackable()->first();
        if ($assistance === null) {
            return false;
        }

        if ($client->claimant !== null) {
            $burial_assistance = $client->claimant->burialAssistance;
            if ($burial_assistance !== null && $assistance instanceof BurialAssistance && $burial_assistance->id === $assistance->id) {
                return true;
            }
        }

        $funeral_assistance = $client->funeralAssistance;
        if ($funeral_assistance !== null && $funeral_assistance->id === $assistance->id) {
            return true;
        }

        return false;
    }
}
