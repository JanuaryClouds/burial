<?php

namespace App\Services;

use Illuminate\Support\Collection;

class DatatableService
{
    /**
     * Summary of getColumns
     *
     * @param Collection $data Collected Data
     * @param array $reject Columns not to show
     * @return Collection<int, array{data: int|string}>     */
    public function getColumns(Collection $data, array $reject = [])
    {
        if ($data->isEmpty()) {
            return collect();
        }

        $reject = array_merge($reject, ['id', 'status', 'show_route']);

        $keys = is_array($data->first()) ? array_keys($data->first()) : array_keys($data->first()->toArray());

        return collect($keys)
            ->reject(fn ($key) => in_array($key, $reject))
            ->map(fn ($key) => [
                'data' => $key,
            ])
            ->values();
    }
}
