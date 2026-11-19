            @foreach($deliveries as $delivery)
                @php($uncertain = in_array($delivery->status, ['pending', 'sending', 'uncertain'], true))
                <tr>
                    <td>{{ match($delivery->type) { 'password_reset' => 'Password reset', 'user_invitation' => 'Staff invitation', 'mfa_security' => 'Account security', default => $delivery->type } }}<br>{{ $delivery->recipient }}</td>
                    <td>{{ $delivery->created_at }}<br>{{ $delivery->claimed_at }}</td>
                    <td>{{ ucfirst($delivery->status) }}<br>
                        @if($delivery->status === 'sent') Accepted by SMTP.
                        @elseif($delivery->canRetry() && $uncertain) Uncertain: this email may already have been delivered.
                        @elseif($delivery->status === 'failed') {{ $delivery->failureDescription() }}
                        @else Awaiting completion. @endif
                    </td>
                    <td>{{ $delivery->attempt_count }}
                        <details><summary>Attempt history</summary>
                            @foreach($delivery->attempts as $attempt)
                                <p>{{ $attempt->started_at }} — {{ $attempt->status }}{{ $attempt->actor_id ? ' — Administrator #'.$attempt->actor_id : ' — Initial request' }}</p>
                            @endforeach
                        </details>
                    </td>
                    <td>
                        @if($delivery->canRetry())
                        <form method="POST" action="{{ route('system.failed-emails.retry', $delivery) }}" data-immediate-mail data-uncertain="{{ $uncertain ? 'true' : 'false' }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Retry email</button>
                            <p role="status" aria-live="polite" data-mail-status></p>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
