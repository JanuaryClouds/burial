<?php

namespace App\Http\Controllers;

use App\Models\FuneralAssistanceType;
use App\Models\Sex;
use App\Services\RecordExcelExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function download(Request $request, RecordExcelExport $export, string $report): BinaryFileResponse|RedirectResponse|JsonResponse
    {
        $this->authorize('create-reports');
        $applications = $report === 'applications';
        $beneficiaries = $report === 'beneficiaries';
        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'until' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('from'), 'after_or_equal:from')],
            'status' => ['nullable', Rule::in(RecordExcelExport::STATUSES)],
            'barangay' => ['nullable', 'string', 'max:150'],
            'assistance' => [Rule::prohibitedIf(! $applications), 'nullable', 'uuid', Rule::exists(FuneralAssistanceType::class, 'uuid')],
            'sex' => [Rule::prohibitedIf(! $beneficiaries), 'nullable', 'integer', Rule::exists(Sex::class, 'id')],
            'pwd' => [Rule::prohibitedIf(! $beneficiaries), 'nullable', 'boolean'],
            'workflow_history' => [Rule::prohibitedIf(! $applications), 'sometimes', 'boolean'],
            'claimant_changes' => [Rule::prohibitedIf(! $applications), 'sometimes', 'boolean'],
        ]);
        $path = $export->generate($report, array_filter($filters, fn ($value) => $value !== null), $request->user());
        if ($path === null) {
            $message = 'No records match these export filters.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 404)
                : back()->with('export_message', $message);
        }

        return response()->download($path, $report.'-'.now()->format('Ymd-His').'.xlsx', ['Cache-Control' => 'private, no-store'])->deleteFileAfterSend();
    }
}
