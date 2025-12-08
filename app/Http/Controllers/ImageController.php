<?php

namespace App\Http\Controllers;

use Crypt;
use Illuminate\Http\Request;
use App\Models\Client;
use Storage;

class ImageController extends Controller
{
    public function stream($tracking_no, $filename)
    {
        $path = "clients/{$tracking_no}/{$filename}";
        abort_unless(Storage::disk('local')->exists($path), 404);
        $encrypted = Storage::disk('local')->get($path);
        $decrypted = Crypt::decrypt($encrypted);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($decrypted);

        return response($decrypted)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
