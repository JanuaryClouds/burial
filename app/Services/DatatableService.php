<?php

namespace App\Services;

class DatatableService
{
    /**
     * Summary of getColumns
     * @param mixed $data Collected Data
     * @param array $reject Columns not to show
     * @return \Illuminate\Support\Collection<int, array{data: string}>     */
    public function getColumns($data, array $reject)
    {
        if ($data->isEmpty()) {
            return collect();
        }

        $keys = is_array($data->first()) ? array_keys($data->first()) : array_keys($data->first()->toArray());

        return collect($keys)
            ->reject(fn ($key) => in_array($key, $reject))
            ->map(fn ($key) => [
                'data'  => $key,
            ])
            ->values();
    }
}