<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\DocumentRequirement;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function get(Application $application, string $filename)
    {
        abort_unless(auth()->user()?->can('view', $application), 403);
        $document = DocumentRequirement::query()->get()->first(fn ($document) => str_replace(' ', '_', strtolower($document->name)) === $filename);
        abort_unless($document, 404);
        $name = $application->tracking_no.'-'.$document->name;

        try {
            $jpeg = $this->imageService->get(Str::slug($name).'.jpg.enc');
        } catch (\Exception) {
            // Do not log requests or payloads containing credentials/documents.
            abort(502, 'Image is temporarily unavailable.');
        }
        abort_if($jpeg === null, 404);

        return response($jpeg, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function webView(Application $application, string $filename)
    {
        $src = route('application.image.get', [$application->uuid, $filename]);

        if (! Auth::user()?->can('view', $application)) {
            abort(403);
        }
        abort_unless(DocumentRequirement::query()->get()->contains(fn ($document) => str_replace(' ', '_', strtolower($document->name)) === $filename), 404);

        return view('application.image', [
            'src' => $src,
            'alt' => Str::title(Str::replace('_', ' ', $filename)),
            'pageTitle' => $application->tracking_no.' | '.Str::title(Str::replace('_', ' ', $filename)),
        ]);
    }
}
