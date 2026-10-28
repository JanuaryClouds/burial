<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\FuneralAssistanceType;
use App\Services\Auth\AccountAccess;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentTemplateService
{
    public function save(FuneralAssistanceType $type, string $name, UploadedFile $upload, ?string $templateUuid = null): DocumentTemplate
    {
        app(AccountAccess::class)->assertWritable();
        $this->authorize();
        Validator::make(compact('name', 'upload'), [
            'name' => ['required', 'string', 'max:255'],
            'upload' => ['required', 'file', 'extensions:docx', 'mimes:docx', 'max:5120'],
        ])->validate();
        if ($templateUuid) {
            $type->documentTemplates()->findOrFail($templateUuid);
        }
        app(DocumentTemplateProcessor::class)->inspect($upload->getRealPath());
        $disk = Storage::disk('document_templates');
        $path = 'templates/'.Str::uuid().'.docx';
        $oldPath = null;
        try {
            $stream = fopen($upload->getRealPath(), 'rb');
            try {
                if (! $disk->put($path, $stream)) {
                    throw new \RuntimeException('Unable to store document template.');
                }
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
            $template = DB::transaction(function () use ($type, $name, $upload, $templateUuid, $path, &$oldPath) {
                $template = $templateUuid ? $type->documentTemplates()->lockForUpdate()->findOrFail($templateUuid) : $type->documentTemplates()->make();
                $oldPath = $template->path;
                $template->fill(['name' => trim($name), 'path' => $path, 'original_filename' => basename($upload->getClientOriginalName())])->save();

                return $template;
            });
        } catch (\Throwable $exception) {
            $this->cleanup($path);
            throw $exception;
        }
        if ($oldPath) {
            $this->cleanup($oldPath);
        }

        return $template;
    }

    public function remove(FuneralAssistanceType $type, string $templateUuid): void
    {
        app(AccountAccess::class)->assertWritable();
        $this->authorize();
        $path = DB::transaction(function () use ($type, $templateUuid) {
            $template = $type->documentTemplates()->lockForUpdate()->findOrFail($templateUuid);
            $template->delete();

            return $template->path;
        });
        $this->cleanup($path);
    }

    private function authorize(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    private function cleanup(string $path): void
    {
        try {
            if (! Storage::disk('document_templates')->delete($path)) {
                throw new \RuntimeException('Template cleanup failed.');
            }
        } catch (\Throwable) {
            Log::warning('Document template file cleanup failed.', ['path' => $path]);
        }
    }
}
