@extends('layouts.app')
@section('content')
<x-card>
    <x-slot:header>Failed Emails</x-slot:header>
    <p>Sent means accepted by SMTP; later bounces are not tracked. Interrupted attempts may already have delivered an email. Manual retries may send duplicates.</p>
    <a href="{{ route('system.index') }}">Back to System</a>
    <div class="table-responsive mt-4">
        <table class="table table-striped">
            <thead><tr><th>Message / recipient</th><th>Created / last attempt</th><th>Outcome</th><th>Attempts</th><th>Retry</th></tr></thead>
            <tbody>
            @forelse($deliveries as $delivery)
                @php($uncertain = in_array($delivery->status, ['pending', 'sending', 'uncertain'], true))
                <tr>
                    <td>{{ $delivery->type === 'password_reset' ? 'Password reset' : $delivery->type }}<br>{{ $delivery->recipient }}</td>
                    <td>{{ $delivery->created_at }}<br>{{ $delivery->claimed_at }}</td>
                    <td>{{ $uncertain ? 'Uncertain: this email may already have been delivered.' : $delivery->failureDescription() }}</td>
                    <td>{{ $delivery->attempt_count }}
                        <details><summary>Attempt history</summary>
                            @foreach($delivery->attempts as $attempt)
                                <p>{{ $attempt->started_at }} — {{ $attempt->status }}{{ $attempt->actor_id ? ' — Administrator #'.$attempt->actor_id : ' — Initial request' }}</p>
                            @endforeach
                        </details>
                    </td>
                    <td>
                        @if(! in_array($delivery->failure_code, ['ineligible', 'suppressed', 'invalid_recipient'], true))
                        <form method="POST" action="{{ route('system.failed-emails.retry', $delivery) }}" data-immediate-mail data-uncertain="{{ $uncertain ? 'true' : 'false' }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Retry email</button>
                            <p role="status" aria-live="polite" data-mail-status></p>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No failed or interrupted emails.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $deliveries->links() }}
</x-card>
<script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/immediate-mail.js') }}" defer></script>
@endsection
