<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Crypt;
use Storage;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(
        ImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

    public function get(string $filename)
    {
        return $this->imageService->get($filename);
    }

    public function stream($tracking_no, $filename)
    {
        $path = "clients/{$tracking_no}/{$filename}";
        abort_unless(Storage::disk('local')->exists($path), 404);
        $encrypted = Storage::disk('local')->get($path);
        $decrypted = Crypt::decrypt($encrypted);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($decrypted);

        return response($decrypted)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }
}
