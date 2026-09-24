<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function get(string $filename)
    {
        return $this->imageService->get($filename);
    }

    public function webView(Application $application, string $filename)
    {
        $src = route('application.image.get', [$application->uuid, $filename]);

        if (! Auth::user()->can('view', $application)) {
            abort(403);
        }

        return view('application.image.get', [
            'src' => $src,
            'alt' => Str::title(Str::replace('_', ' ', $filename)),
            'pageTitle' => $application->tracking_no.' | '.Str::title(Str::replace('_', ' ', $filename)),
        ]);
    }
}
