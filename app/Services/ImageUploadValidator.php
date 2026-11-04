<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImageUploadValidator
{
    public function validate(mixed $file, string $field = 'image'): void
    {
        $validator = Validator::make(['upload' => $file], ['upload' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp']]);
        if ($validator->fails()) {
            throw ValidationException::withMessages([$field => $validator->errors()->first('upload')]);
        }
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            throw ValidationException::withMessages([$field => 'Please upload a valid image.']);
        }
        // Read only the header before allocating memory for decoded pixels.
        $size = @getimagesize($file->getRealPath());
        $width = (int) config('security.images.max_width');
        $height = (int) config('security.images.max_height');
        $pixels = (int) config('security.images.max_pixels');
        if (! $size || $size[0] < 1 || $size[1] < 1 || $size[0] > $width || $size[1] > $height || $size[0] > intdiv($pixels, $size[1])) {
            throw ValidationException::withMessages([$field => "Upload a readable image no larger than {$width} × {$height} pixels and {$pixels} total pixels."]);
        }
    }
}
