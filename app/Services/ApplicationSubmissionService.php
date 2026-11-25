<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationDocumentFile;
use App\Models\ApplicationSubmissionDraft;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApplicationSubmissionService
{
    public function prepare(string $clientUuid, string $beneficiaryUuid, int $relationshipId, ?int $expectedVersion = null): ApplicationSubmissionDraft
    {
        $actor = app(AccountAccess::class)->assertWritable();
        Gate::authorize('create', Application::class);
        Validator::make(['relationship_id' => $relationshipId], ['relationship_id' => ['required', 'integer', 'exists:relationships,id']])->validate();

        return DB::transaction(function () use ($actor, $clientUuid, $beneficiaryUuid, $relationshipId, $expectedVersion) {
            User::whereKey($actor->id)->lockForUpdate()->firstOrFail();
            $client = Client::whereKey($clientUuid)->where('user_id', $actor->id)->firstOrFail();
            Beneficiary::whereKey($beneficiaryUuid)->where('created_by', $actor->id)->firstOrFail();
            $draft = ApplicationSubmissionDraft::firstOrCreate(
                ['user_id' => $actor->id, 'client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiaryUuid],
                ['relationship_id' => $relationshipId, 'documents' => []],
            );
            $draft->refresh();
            if ($expectedVersion !== null && $draft->version !== $expectedVersion) {
                throw ValidationException::withMessages(['images' => 'This draft changed in another request. Reload before continuing.']);
            }
            if (! $draft->application_uuid && $draft->relationship_id != $relationshipId) {
                $draft->update(['relationship_id' => $relationshipId, 'version' => $draft->version + 1]);
            }

            return $draft;
        });
    }

    public function documents(ApplicationSubmissionDraft $draft): Collection
    {
        $beneficiary = Beneficiary::with('religion')->findOrFail($draft->beneficiary_uuid);

        return DocumentRequirement::index()->where('is_active', true)->get()
            ->filter(fn ($document) => ! $document->for_muslim || $beneficiary->religion?->name === 'Islam');
    }

    private function lock(string $uuid, int $version): ApplicationSubmissionDraft
    {
        app(AccountAccess::class)->assertWritable();
        Gate::authorize('create', Application::class);
        $draft = ApplicationSubmissionDraft::whereKey($uuid)->lockForUpdate()->firstOrFail();
        abort_unless($draft->user_id === auth()->id(), 403);
        if ($draft->version !== $version || $draft->application_uuid) {
            throw ValidationException::withMessages(['images' => 'This draft changed or was submitted. Reload before continuing.']);
        }
        abort_unless(Client::whereKey($draft->client_uuid)->where('user_id', auth()->id())->exists()
            && Beneficiary::whereKey($draft->beneficiary_uuid)->where('created_by', auth()->id())->exists(), 403);

        return $draft;
    }

    public function submit(string $uuid, int $version, array $images): Application
    {
        $draft = DB::transaction(fn () => $this->lock($uuid, $version));
        $policy = app(FileserverPolicy::class);
        if ($policy->skip()) {
            $images = [];
        }
        $documents = $this->documents($draft);
        $this->validateDocuments($draft, $documents, $images);
        if ($warning = $policy->warning($images !== [] || (! $policy->skip() && $documents->contains('is_required', true)))) {
            throw ValidationException::withMessages(['images' => $warning]);
        }
        foreach ($images as $key => $file) {
            if ($file === null) {
                continue;
            }
            DB::transaction(function () use ($uuid, $version, $key, $file) {
                $draft = $this->lock($uuid, $version);
                $document = $this->documents($draft)->first(fn ($item) => $item->uploadKey() === $key);
                abort_unless($document, 422);
                $stored = $draft->documents ?? [];
                if (isset($stored[$document->id])) {
                    throw ValidationException::withMessages(['images' => 'This document is already saved. Remove the selection to reuse it.']);
                }
                $stored[$document->id] = app(ImageService::class)->stageDocument($document, $file);
                $draft->update(['documents' => $stored]);
            });
        }

        return DB::transaction(function () use ($uuid, $version) {
            $draft = $this->lock($uuid, $version);
            $documents = $this->documents($draft);
            $this->validateDocuments($draft, $documents, []);
            $application = app(ApplicationService::class)->store(Client::findOrFail($draft->client_uuid), Beneficiary::findOrFail($draft->beneficiary_uuid), $draft->relationship_id);
            if (! app(FileserverPolicy::class)->skip()) {
                foreach ($documents as $document) {
                    if ($reference = ($draft->documents ?? [])[$document->id] ?? null) {
                        ApplicationDocumentFile::create([...$reference, 'application_uuid' => $application->uuid, 'document_requirement_id' => $document->id]);
                    }
                }
            }
            $draft->update(['application_uuid' => $application->uuid, 'version' => $version + 1]);

            return $application;
        });
    }

    private function validateDocuments(ApplicationSubmissionDraft $draft, Collection $documents, array $images): void
    {
        $policy = app(FileserverPolicy::class);
        if ($policy->skip()) {
            return;
        }
        $stored = $draft->documents ?? [];
        foreach ($documents as $document) {
            if (isset($stored[$document->id]) && $stored[$document->id]['storage'] !== app(ImageService::class)->destination()) {
                throw ValidationException::withMessages(['images' => 'Saved documents belong to another storage environment.']);
            }
            if ($document->is_required && ! isset($stored[$document->id]) && ! isset($images[$document->uploadKey()])) {
                throw ValidationException::withMessages(['images' => 'Upload '.$document->name.' before submitting.']);
            }
        }
        foreach ($images as $key => $file) {
            if (! $documents->contains(fn ($document) => $document->uploadKey() === $key)) {
                throw ValidationException::withMessages(['images' => 'An uploaded document is no longer applicable.']);
            }
            if ($file !== null) {
                app(ImageUploadValidator::class)->validate($file, 'images.'.$key);
            }
        }
    }
}
