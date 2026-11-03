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

    public function index(ImmediateMailService $mail)
    {
        $this->authorizeAdmin();
        $deliveries = MailDelivery::with(['attempts' => fn ($q) => $q->latest('id')])
            ->where(function ($query) use ($mail) {
                $query->whereIn('status', ['failed', 'uncertain'])
                    ->orWhere(fn ($q) => $q->where('status', 'pending')->where('created_at', '<', $mail->interruptedBefore()))
                    ->orWhere(fn ($q) => $q->where('status', 'sending')->where('claimed_at', '<', $mail->interruptedBefore()));
            })->latest('id')->paginate(20);

        return response()->view('system.failed-emails', compact('deliveries'))->header('Cache-Control', 'no-store');
    }

    public function retry(Request $request, MailDelivery $delivery, ImmediateMailService $mail)
    {
        $this->authorizeAdmin();
        $result = $mail->retry($delivery, $request->user());
        $message = $result->status === 'sent' ? 'Email accepted by the mail service.' : $result->failureDescription();

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'sent' => $result->status === 'sent'])
            : back()->with($result->status === 'sent' ? 'success' : 'error', $message);
    }
}
