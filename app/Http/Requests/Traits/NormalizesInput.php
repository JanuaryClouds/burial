<?php

namespace App\Http\Requests\Traits;

trait NormalizesInput
{
    protected function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return preg_replace('/\s+/u', ' ', $value);
    }

    protected function cleanArray(mixed $value): ?array
    {
        if ($value === null || ! is_array($value)) {
            return null;
        }

        $clean = function ($value) use (&$clean) {
            if (is_array($value)) {
                return array_map($clean, $value);
            }

            if (is_string($value)) {
                return preg_replace('/\s+/u', ' ', trim($value));
            }

            return $value;
        };

        return array_map($clean, $value);
    }

    protected function normalizePhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\D+/', '', $value);
    }
}