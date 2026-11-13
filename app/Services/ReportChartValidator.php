<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReportChartValidator
{
    public function validate(mixed $charts): array
    {
        Validator::make(['charts' => $charts], [
            'charts' => ['required', 'array:applications-per-status,beneficiaries-per-age-group'],
            'charts.applications-per-status' => ['required', 'string', 'max:2796226'],
            'charts.beneficiaries-per-age-group' => ['required', 'string', 'max:2796226'],
        ])->validate();

        foreach ($charts as $key => $chart) {
            $bytes = str_starts_with($chart, 'data:image/png;base64,')
                ? base64_decode(substr($chart, 22), true) : false;
            $size = $bytes === false ? false : @getimagesizefromstring($bytes);
            if ($bytes === false || strlen($bytes) > 2097152 || ! $size || $size[2] !== IMAGETYPE_PNG
                || $size[0] < 1 || $size[1] < 1 || $size[0] > 4096 || $size[1] > 4096
                || $size[0] > intdiv(8000000, $size[1])) {
                throw ValidationException::withMessages(['charts.'.$key => 'Provide a PNG chart up to 2 MB and 4096 pixels per side (8 million pixels total).']);
            }
            $image = @imagecreatefromstring($bytes);
            if ($image === false) {
                throw ValidationException::withMessages(['charts.'.$key => 'Provide a readable PNG chart.']);
            }
            // Only server-encoded raster bytes reach the document renderer.
            ob_start();
            try {
                imagesavealpha($image, true);
                imagepng($image);
                $charts[$key] = 'data:image/png;base64,'.base64_encode(ob_get_contents());
            } finally {
                ob_end_clean();
                unset($image);
            }
        }

        return $charts;
    }
}
