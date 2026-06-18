<?php

namespace App\Http\Controllers;

use App\Services\DatatableService;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct(
        protected DatatableService $datatableServices
    ) {}

    public function index()
    {
        $data = Activity::with('causer')
            ->select('id', 'description', 'causer_type', 'causer_id', 'properties', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'caused_by' => $log->causer?->first_name ?? '',
                    'properties' => collect($log->properties->toArray())
                        ->map(function ($value, $key) {
                            if (is_array($value)) {
                                $value = json_encode($value);
                            }

                            return "{$key}: {$value}";
                        })
                        ->implode(', '),
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });
        $columns = $this->datatableServices->getColumns($data, ['id']);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('logs.index', compact('data', 'columns'));
    }
}
