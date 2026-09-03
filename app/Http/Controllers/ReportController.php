<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Claimant;
use App\Services\BeneficiaryService;
use App\Services\ClientService;
use App\Services\DatatableService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportServices,
        protected DatatableService $datatableServices,
        protected ClientService $clientServices,
        protected BeneficiaryService $beneficiaryServices,
    ) {}

    public function clients(Request $request)
    {
        $model = 'clients';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $data = $this->clientServices->reportIndex($startDate, $endDate);
        $columns = $this->datatableServices->getColumns($data, []);

        $clientsPerBarangay = $this->reportServices->clientsPerBarangay($startDate, $endDate);
        $clientsPerAssistance = $this->reportServices->clientsPerAssistance($startDate, $endDate);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'clientsPerBarangay',
            'clientsPerAssistance',
            'startDate',
            'endDate'
        ));
    }

    public function beneficiaries(Request $request)
    {
        $model = 'beneficiaries';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $data = $this->beneficiaryServices->reportIndex($startDate, $endDate);
        $columns = $this->datatableServices->getColumns($data, []);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'startDate',
            'endDate'
        ));
    }

    public function cheques(Request $request)
    {
        $model = 'checks';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $data = Cheque::with([
            'burialAssistance.claimant.client',
        ])
            ->select(
                'id',
                'burial_assistance_id',
                'claimant_id',
                'obr_number',
                'cheque_number',
                'dv_number',
                'amount',
                'date_issued',
                'date_claimed',
                'status',
                'created_at'
            )
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->get()
            ->map(function ($cheque) {
                return [
                    'tracking_no' => $cheque->burialAssistance?->originalClaimant()?->client?->tracking_no,
                    'claimant' => $cheque->claimant?->fullname(),
                    'obr_number' => $cheque->obr_number,
                    'cheque_number' => $cheque->cheque_number,
                    'dv_number' => $cheque->dv_number,
                    'amount' => $cheque->amount,
                    'date_issued' => $cheque->date_issued,
                    'date_claimed' => $cheque->date_claimed,
                    'status' => $cheque->status,
                    'created_at' => $cheque->created_at->format('M d, Y'),
                ];
            });

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        $columns = $this->datatableServices->getColumns($data, []);
        $chequesPerStatus = $this->reportServices->chequesPerStatus($startDate, $endDate);

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'chequesPerStatus',
            'startDate',
            'endDate',
        ));
    }
}
