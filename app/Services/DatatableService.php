<?php

namespace App\Services;

class DatatableService
{
    /**
     * Summary of getColumns
     * @param mixed $data Collected Data
     * @param array $reject Columns not to show
     * @return \Illuminate\Support\Collection<int, array{data: TValue>|\Illuminate\Support\Collection<TKey, TValue>}
     */
    public function getColumns($data, array $reject)
    {
        if ($data->isEmpty()) {
            return collect();
        }

        return collect(array_keys($data->first()))
            ->reject(fn ($key) => in_array($key, $reject))
            ->map(fn ($key) => [
                'data'  => $key,
            ])
            ->values();
    }
}