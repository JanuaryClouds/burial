<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Services\DocumentTemplateProcessor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentTemplateController extends Controller
{
    public function original(FuneralAssistanceType $funeralAssistanceType, string $template)
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        $document = $funeralAssistanceType->documentTemplates()->findOrFail($template);
        $disk = Storage::disk('document_templates');
        abort_unless($disk->exists($document->path), 404);

        return response()->download($disk->path($document->path), $this->filename($document->name), $this->headers());
    }

    public function download(Application $application, string $template, DocumentTemplateProcessor $processor)
    {
        $this->authorize('view', $application);
        abort_unless(auth()->user()?->hasOfficeAccess(), 403);
        $application->load('activeRecommendation.funeralAssistanceType');
        $type = $application->activeRecommendation?->funeralAssistanceType;
        abort_unless($type !== null, 404);
        $document = $type->documentTemplates()->findOrFail($template);
        $disk = Storage::disk('document_templates');
        abort_unless($disk->exists($document->path), 404);
        $path = 'generated/'.Str::uuid().'.docx';
        try {
            $disk->makeDirectory('generated');
            $processor->generate($disk->path($document->path), $disk->path($path), $application);

            return response()->download($disk->path($path), $this->filename($document->name.'-'.$application->tracking_no), $this->headers())->deleteFileAfterSend(true);
        } catch (\Throwable $exception) {
            try {
                $disk->delete($path);
            } catch (\Throwable) {
                Log::warning('Generated document cleanup failed.', ['path' => $path]);
            }
            Log::error('Accessory document generation failed.', ['template_uuid' => $document->uuid, 'exception_type' => $exception::class]);
            abort(503, 'The document could not be generated. Please try again or ask a superadmin to check the template.');
        }
    }

    private function filename(string $name): string
    {
        return (Str::slug(Str::limit($name, 150, '')) ?: 'document').'.docx';
    }

    private function headers(): array
    {
        return ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff'];
    }
}
