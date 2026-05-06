<?php

namespace App\Services;

use App\Models\BurialAssistance;
use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\ProcessLog;
use App\Models\WorkflowStep;
use Carbon\Carbon;
use DB;
use Http;
use Illuminate\Http\Client\RequestException;
use Str;

class ProcessLogService
{
    /**
     * @param  BurialAssistance  $burialAssistance  Application
     * @param  Claimant  $claimant  Assigned claimant of the assistance
     * @param  WorkflowStep  $step  Step indicator
     * @param  array  $data  Data to save
     * @return void
     */
    public function create(BurialAssistance $burialAssistance, Claimant $claimant, WorkflowStep $step, array $data)
    {
        return DB::transaction(function () use ($burialAssistance, $claimant, $step, $data) {
            $application = $burialAssistance;
            $latestLog = $application->processLogs()->latest()->first();
            if ($latestLog && $latestLog->loggable && $latestLog->loggable->order_no == $step->order_no) {
                throw new \RuntimeException('This step already exists. Refreshing the page will update your page.');
            }
            $application->update(['status' => 'processing']);

            if ($step->order_no == 9) {
                if (Cheque::where('obr_number', $data['extra_data']['OBR']['oBR_number'])->exists()) {
                    throw new \RuntimeException('This OBR number already exists.');
                }

                $application->cheque()->create([
                    'id' => Str::uuid(),
                    'burial_assistance_id' => $application->id,
                    'claimant_id' => $claimant->id,
                    'obr_number' => $data['extra_data']['OBR']['oBR_number'],
                ]);
            }

            if ($step->order_no == 10) {
                if (Cheque::where('dv_number', $data['extra_data']['dv_number'])->exists()) {
                    throw new \RuntimeException('This DV number already exists.');
                }

                $application->latestCheque()->update([
                    'dv_number' => $data['extra_data']['dv_number'] ?? null,
                ]);
            }

            if ($step->order_no == 11) {
                if (Cheque::where('cheque_number', $data['extra_data']['cheque_number'])->exists()) {
                    throw new \RuntimeException('This cheque number already exists.');
                }

                DB::transaction(function () use ($application, $data) {
                    $application->latestCheque()->update([
                        'cheque_number' => $data['extra_data']['cheque_number'],
                        'amount' => $data['extra_data']['amount'],
                    ]);
                });

                $claimantName = $claimant->fullname();
                $deceased = $application->beneficiary()?->fullname();
                $beneficiary = $application->beneficiary();
                $dod = $beneficiary?->date_of_death
                    ? Carbon::parse($beneficiary->date_of_death)->format('F d, Y')
                    : null;

                // TODO Disbursement Service here
            }

            if ($step->order_no == 12) {
                $application->latestCheque()->update([
                    'date_issued' => $data['extra_data']['date_issued'],
                ]);
                $application->update(['status' => 'approved']);
            }

            if ($application->initial_checker == null) {
                $application->update(['initial_checker' => auth()->user()->id]);
            }

            $application->processLogs()->create([
                'id' => Str::uuid(),
                'loggable_id' => $step->id,
                'loggable_type' => WorkflowStep::class,
                'is_progress_step' => true,
                'burial_assistance_id' => $application->id,
                'claimant_id' => $claimant->id,
                'date_in' => $data['date_in'],
                'date_out' => $data['date_out'],
                'comments' => $data['comments'],
                'extra_data' => $data['extra_data'] ?? null,
                'added_by' => auth()->user()->id,
            ]);
        });

        if ($claimant && $deceased) {
            $this->createDisbursement($data, $deceased, $claimant, $dod);
        }
    }

    /**
     * @param  array  $data  Process Log Data to save
     * @param  string  $deceased  Deceased Name
     * @param  string  $claimant  Claimant Name
     * @param  string  $dod  Date of Death
     */
    public function createDisbursement(array $data, string $deceased, string $claimant, $dod)
    {
        // TODO API Post to Disbursement System
        if (config('services.disbursement.enable.post')) {
            $response = Http::timeout(10)
                ->retry(3, 1000)
                ->withHeader('X-Secret-Key', config('services.disbursement.key'))
                ->post(config('services.disbursement.endpoint'), [
                    'key' => 'burial',
                    'payee' => $claimant,
                    'particulars' => 'For payment of funeral to the
                    '.$deceased.' who died on '.$dod.' as per Ordinance No.34 series Of 2015',
                    'cheque_number' => $data['extra_data']['cheque_number'],
                    'amount' => $data['extra_data']['amount'],
                    'date' => $data['date_in'],
                ]);

            throw_if($response->failed(), RequestException::class, $response);

            return $response->json();
        }
    }

    /**
     * Summary of delete
     *
     * @param  ProcessLog  $log  Log to delete
     * @param  BurialAssistance  $application  Application to update
     * @return void
     */
    public function delete(ProcessLog $log, BurialAssistance $application)
    {
        if (app()->hasDebugModeEnabled()) {
            if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 9) {
                $application->latestCheque()->delete();
                $application->update(['status' => 'processing']);
            }

            if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 10) {
                $application->latestCheque()->update(['dv_number' => null]);
            }

            if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 11) {
                $application->latestCheque()->update(['cheque_number' => null]);
                $application->latestCheque()->update(['amount' => null]);
            }

            if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 12) {
                $application->latestCheque()->update(['date_issued' => null]);
                $application->update(['status' => 'processing']);
            }

            if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 13) {
                $application->latestCheque()->update([
                    'status' => 'issued',
                    'date_claimed' => null,
                ]);
                $application->update(['status' => 'approved']);
            }
        }

        $log->delete();
    }

    public function timeline(BurialAssistance $application)
    {
        return ProcessLog::where('burial_assistance_id', $application->id)
            ->with(['loggable', 'claimant'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'loggable' => $log->loggable,
                    'step' => $log->loggable?->order_no,
                    'description' => $log->loggable->description ?? null,
                    'extra_data' => $log->extra_data ?? null,
                    'comments' => $log->comments ?? null,
                    'in' => $log->date_in ?? null,
                    'out' => $log->date_out ?? null,
                ];
            })
            ->toArray();
    }

    public function getLog($claimant, $order)
    {
        return $claimant->processLogs()
            ->whereHasMorph('loggable', [WorkflowStep::class], fn ($q) => $q->where('order_no', $order))
            ->with('loggable')
            ->first();
    }

    public function getAvgProcessingTime($application)
    {
        $avgProcessingTime = $application::with(['processLogs'])
            ->get()
            ->map(function ($application) {
                $logs = $application->processLogs->sortBy('created_at')->values();
                if ($logs->count() < 2) {
                    return 0;
                }
                $diffs = [];
                for ($i = 0; $i < $logs->count() - 1; $i++) {
                    $start = Carbon::parse($logs[$i]->created_at);
                    $end = Carbon::parse($logs[$i + 1]->created_at);
                    $diffs[] = $end->diffInMinutes($start);
                }

                return collect($diffs)->avg();
            })
            ->filter()
            ->values();

        return $avgProcessingTime;
    }
}
