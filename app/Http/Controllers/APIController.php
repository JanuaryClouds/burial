<?php

namespace App\Http\Controllers;

use App\Models\BurialAssistance;
use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\ProcessLog;
use Str;

class APIController extends Controller
{
    public function getBurialAssistances()
    {
        $data = BurialAssistance::with([
            'claimant',
            'claimant.client',
            'claimant.client.beneficiary',
        ])
            ->get()
            ->map(function ($burialAssistance) {
                return [
                    'tracking_no' => $burialAssistance->claimant?->client?->tracking_no,
                    'client' => $burialAssistance->claimant?->client?->fullname(),
                    'beneficiary' => $burialAssistance->claimant?->client?->beneficiary?->fullname(),
                    'address' => $burialAssistance->claimant?->client?->address(),
                    'funeraria' => $burialAssistance->funeraria,
                    'amount' => $burialAssistance->amount,
                    'status' => Str::title($burialAssistance->status)
                ];
            })
            ->sortBy('tracking_no')
            ->values();

        return response()->json($data);
    }

    public function claimants()
    {
        $data = Claimant::select(
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'suffix',
            'address',
            'barangay_id',
            'mobile_number',
            'relationship_to_deceased',
        )
            ->with('barangay', 'relationship', 'cheque')
            ->get()
            ->map(function ($claimant) {
                return [
                    'id' => $claimant->id,
                    'firstName' => $claimant->first_name,
                    'middleName' => $claimant->middle_name ?? null,
                    'lastName' => $claimant->last_name,
                    'suffix' => $claimant->suffix ?? null,
                    'address' => $claimant->address,
                    'barangay' => $claimant->barangay,
                    'mobileNumber' => $claimant->mobile_number,
                    'relationshipOfDeceasedToClaimant' => $claimant->relationship,
                    'cheque' => $claimant->cheque,
                ];
            });

        return response()->json($data);
    }

    public function cheques()
    {
        $data = Cheque::select(
            'id',
            'burial_assistance_id',
            'claimant_id',
            'obr_number',
            'cheque_number',
            'dv_number',
            'amount',
            'date_issued',
            'date_claimed',
        )
            ->with('burialAssistance', 'claimant')
            ->get()
            ->map(function ($cheque) {
                return [
                    'id' => $cheque->id,
                    'burialAssistance' => $cheque->burialAssistance,
                    'claimant' => $cheque->claimant,
                    'obrNumber' => $cheque->obr_number,
                    'chequeNumber' => $cheque->cheque_number,
                    'dvNumber' => $cheque->dv_number,
                    'amount' => $cheque->amount,
                    'dateIssued' => $cheque->date_issued,
                    'dateClaimed' => $cheque->date_claimed,
                ];
            });

        return response()->json($data);
    }

    // ! Unused
    public function processLogs()
    {
        $data = ProcessLog::select(
            'id',
            'burial_assistance_id',
            // 'claimant_id',
            'loggable_id',
            'loggable_type',
            'date_in',
            'date_out',
            'comments',
            'extra_data',
        )
            ->with('burialAssistance', 'loggable')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'burialAssistance' => $log->burialAssistance,
                    'loggable' => $log->loggable,
                    'dateIn' => $log->date_in,
                    'dateOut' => $log->date_out,
                    'comments' => $log->comments,
                    'extraData' => $log->extra_data,
                ];
            });

        return response()->json($data);
    }
}
