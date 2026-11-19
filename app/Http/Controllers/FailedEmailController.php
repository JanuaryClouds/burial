<?php

namespace App\Http\Controllers;

use App\Models\MailDelivery;
use App\Services\Auth\AccountAccess;
use App\Services\Mail\ImmediateMailService;
use Illuminate\Http\Request;

class FailedEmailController extends Controller
{
    private function authorizeAdmin(): void
    {
        $actor = app(AccountAccess::class)->assertWritable();
        abort_unless($actor->isSystemAdministrator(), 403);
    }

    public function index(Request $request, ImmediateMailService $mail)
    {
        $this->authorizeAdmin();
        $filters = $request->validate(['status' => 'nullable|in:pending,sending,sent,failed,uncertain',
            'type' => 'nullable|in:password_reset,user_invitation,mfa_security']);
        $deliveries = MailDelivery::with(['attempts' => fn ($q) => $q->latest('id'), 'invitation'])
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->latest('id')->get();

        return response()->view('system.failed-emails', compact('deliveries'))->header('Cache-Control', 'no-store');
    }

    public function retry(Request $request, MailDelivery $delivery, ImmediateMailService $mail)
    {
        $this->authorizeAdmin();
        $result = $mail->retry($delivery, $request->user());
        $message = $result->status === 'sent' ? 'Email accepted by the mail service.' : $result->failureDescription();

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'sent' => $result->status === 'sent',
                'retryable' => $result->status === 'failed' && ! in_array($result->failure_code, ['ineligible', 'suppressed', 'invalid_recipient'], true)])
            : back()->with($result->status === 'sent' ? 'success' : 'error', $message);
    }
}
