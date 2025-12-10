<?php

namespace App\Http\Controllers;

use App\Models\BurialAssistance;
use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\Deceased;
use App\Models\ProcessLog;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function getBurialAssistances() {
        $data = BurialAssistance::select(
            'id', 
            'tracking_no',
            'application_date',
            'swa',
            'funeraria',
            'amount',
            'remarks',
        )
            ->with('deceased', 'claimant')
            ->get()
            ->map(function($application) {
                return [
                    'id' => $application->id,
                    'trackingNumber' => $application->tracking_no,
                    'applicationDate' => $application->application_date,
                    'swa' => $application->swa,
                    'funeraria' => $application->funeraria,
                    'deceased' => $application->deceased,
                    'claimant' => $application->claimant,
                    'amount' => $application->amount,
                    'remarks' => $application->remarks
                ];
            });

        return response()->json($data);
    }

    public function deceased() {
        $data = Deceased::select(
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'suffix',
            'date_of_birth',
            'date_of_death',
            'gender',
            'barangay_id',
            'religion_id',
        )
            ->with('barangay', 'gender', 'religion')
            ->get()
            ->map(function ($deceased) {
                return [
                    'id' => $deceased->id,
                    'firstName' => $deceased->first_name,
                    'middleName' => $deceased->middle_name ?? null,
                    'lastName' => $deceased->last_name,
                    'suffix' => $deceased->suffix ?? null,
                    'dateOfBirth' => $deceased->date_of_birth,
                    'dateOfDeath' => $deceased->date_of_death,
                    'gender' => $deceased->gender == 1 ? 'Male' : 'Female',
                    'barangay' => $deceased->barangay,
                    'religion' => $deceased->religion
                ];
            });
        return response()->json($data);
    }

    public function claimants() {
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
                    'cheque' => $claimant->cheque
                ];
            });

        return response()->json($data);
    }

    public function cheques() {
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
                    'dateClaimed' => $cheque->date_claimed
                ];
            });
        return response()->json($data);
    }

    public function processLogs() {
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
                    'extraData' => $log->extra_data
                ];
            });

        return response()->json($data);
    }
}
