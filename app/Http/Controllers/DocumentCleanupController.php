<?php

namespace App\Http\Controllers;

use App\Models\SuperadminMfaCredential;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\SuperadminMfa;
use App\Services\DocumentCleanupService;
use Illuminate\Cache\Lock;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class DocumentCleanupController extends Controller
{
    public function index(Request $request): View
    {
        $actor = $this->actor();
        $token = $request->session()->get('document_cleanup.preview');
        try {
            $preview = is_string($token) ? Cache::get('document-cleanup.preview.'.$token) : null;
            if (! is_array($preview) || ! $this->belongsTo($preview, $request, $actor)) {
                $preview = null;
            }
        } catch (Throwable) {
            $preview = null;
        }

        return view('system.document-cleanup', ['pageTitle' => 'Document Cleanup', 'preview' => $preview, 'token' => $token]);
    }

    public function preview(Request $request, DocumentCleanupService $cleanup): RedirectResponse
    {
        $actor = $this->actor();
        try {
            if ($previous = $request->session()->pull('document_cleanup.preview')) {
                Cache::forget('document-cleanup.preview.'.$previous);
            }
            $preview = $cleanup->preview(24, 100, 10);
            if ($preview['initial_failed']) {
                return $this->back()->with('cleanup_error', 'Unable to read document references or storage. No files were deleted. Please try a new preview later.');
            }
            $token = Str::random(64);
            $preview += ['user_id' => $actor->id, 'session' => hash('sha256', $request->session()->getId()),
                'version' => SuperadminMfaCredential::where('user_id', $actor->id)->value('version'),
                'expires' => now()->addMinutes(15)->timestamp];
            if (! Cache::put('document-cleanup.preview.'.$token, $preview, 900)) {
                throw new \RuntimeException('Unable to store cleanup preview.');
            }
            $request->session()->put('document_cleanup.preview', $token);

            return $this->back();
        } catch (Throwable $exception) {
            Log::warning('Document cleanup preview unavailable.', ['exception_type' => $exception::class]);

            return $this->back()->with('cleanup_error', 'The preview could not be completed. No files were deleted. Please try again later.');
        }
    }

    public function clean(Request $request, DocumentCleanupService $cleanup, SuperadminMfa $mfa): RedirectResponse
    {
        $actor = $this->actor();
        $data = $request->validate(['preview_token' => ['required', 'string', 'regex:/^[A-Za-z0-9]{64}$/D'],
            'confirm_deletion' => ['accepted'], 'mfa_code' => ['required', 'string', 'regex:/^[0-9]{6}$/D']]);
        $lock = null;
        $executionStarted = false;
        try {
            $lock = Cache::lock('document-cleanup.execution', 120);
            if (! $lock instanceof Lock) {
                throw new \RuntimeException('Cleanup requires a cache lock with ownership checks.');
            }
            if (! $lock->get()) {
                return $this->back()->with('cleanup_error', 'Another document cleanup is running. Please wait, then try again.');
            }
            $key = 'document-cleanup.preview.'.$data['preview_token'];
            $preview = Cache::get($key);
            if (! is_array($preview) || ! $this->belongsTo($preview, $request, $actor)
                || $request->session()->get('document_cleanup.preview') !== $data['preview_token']) {
                throw ValidationException::withMessages(['preview_token' => 'This preview is unavailable or expired. Create a new preview before cleaning.']);
            }
            $operation = (string) Str::uuid();
            $mfa->protect($actor, $data['mfa_code'], function () use ($actor, $preview, $operation, $key): void {
                $this->audit($actor, $operation, 'started', ['candidates' => count($preview['files'])]);
                if (! Cache::forget($key)) {
                    throw new \RuntimeException('Unable to consume cleanup preview.');
                }
            });
            $request->session()->forget('document_cleanup.preview');
            $executionStarted = true;
            $result = $cleanup->clean($preview['files'], 24, 10, fn (): bool => $lock->isOwnedByCurrentProcess());
            $counts = ['deleted' => count($result['deleted']), 'skipped' => count($result['skipped']),
                'failed' => count($result['failed']), 'unprocessed' => $result['unprocessed']];
            $status = $result['initial_failed'] ? 'failed' : (($counts['failed'] || $counts['unprocessed']) ? 'partial' : 'completed');
            $this->audit($actor, $operation, $status, $counts);

            return $this->back()->with('cleanup_result', $counts + ['status' => $status, 'operation' => $operation, 'initial_failed' => $result['initial_failed']]);
        } catch (ValidationException|HttpExceptionInterface $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::warning('Document cleanup could not finish.', ['exception_type' => $exception::class]);

            return $this->back()->with('cleanup_error', $executionStarted
                ? 'Cleanup could not finish or its outcome could not be recorded. Some files may have been removed. Create a new preview to inspect what remains.'
                : 'Cleanup could not start. No files were deleted. Please try again later.');
        } finally {
            try {
                $lock?->release();
            } catch (Throwable $exception) {
                Log::warning('Document cleanup lock release failed.', ['exception_type' => $exception::class]);
            }
        }
    }

    private function actor(): User
    {
        $actor = app(AccountAccess::class)->assertWritable();
        abort_unless($actor->hasRole('superadmin'), 403);

        return $actor;
    }

    /** @param array{user_id: int, session: string, version: string, expires: int} $preview */
    private function belongsTo(array $preview, Request $request, User $actor): bool
    {
        return $preview['user_id'] === $actor->id && $preview['expires'] > now()->timestamp
            && hash_equals($preview['session'], hash('sha256', $request->session()->getId()))
            && $preview['version'] === SuperadminMfaCredential::where('user_id', $actor->id)->value('version');
    }

    /** @param array<string, int> $counts */
    private function audit(User $actor, string $operation, string $status, array $counts): void
    {
        $entry = activity('document-cleanup')->causedBy($actor)->withProperties($counts + ['operation' => $operation, 'hours' => 24, 'status' => $status])
            ->log('Document cleanup '.$status);
        if (! $entry instanceof Model || ! $entry->exists) {
            throw new \RuntimeException('Cleanup audit logging is unavailable.');
        }
    }

    private function back(): RedirectResponse
    {
        return redirect()->route('system.document-cleanup.index');
    }
}
