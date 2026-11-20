        @foreach($invitations as $invitation)
        <tr data-invitation-id="{{ $invitation->id }}" data-invitation-version="{{ $invitation->managementVersion() }}" data-invitation-pending="{{ $invitation->isPending() ? 'true' : 'false' }}">
            <td>{{ $invitation->email }}</td>
            <td>{{ $invitation->status() }}<br>{{ $invitation->expires_at->timezone('Asia/Manila')->format('Y-m-d H:i') }} PHT</td>
            <td>
                @if($invitation->isPending())
                <form method="POST" action="{{ route('user.invitations.manage', $invitation) }}" data-immediate-mail data-refresh-invitations>
                    @csrf<input type="hidden" name="action" value="roles">
                    <label class="form-label" for="invitation-roles-{{ $invitation->id }}">Operational roles</label>
                    <select class="form-select" id="invitation-roles-{{ $invitation->id }}" name="roles[]" multiple
                        data-control="select2" data-placeholder="Staff only" data-width="100%" aria-label="Roles for {{ $invitation->email }}">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(in_array($role, $invitation->roles, true))>{{ $role }}</option>
                    @endforeach
                    </select>
                    <button class="btn btn-sm btn-light-primary mt-3" type="submit" data-sending-label="Saving…">Save roles</button>
                    <p role="status" aria-live="polite" data-mail-status></p>
                </form>
                @else {{ implode(', ', $invitation->roles) ?: 'Staff only' }} @endif
            </td>
            <td>
                @if($invitation->isPending())
                @foreach(['renew' => 'Renew 24 hours (no email)', 'resend' => 'Resend email', 'revoke' => 'Revoke'] as $action => $label)
                <form class="mb-2" method="POST" action="{{ route('user.invitations.manage', $invitation) }}" data-immediate-mail data-refresh-invitations>
                    @csrf<input type="hidden" name="action" value="{{ $action }}">
                    <button class="btn btn-sm btn-light-primary" type="submit">{{ $label }}</button>
                    <p role="status" aria-live="polite" data-mail-status></p>
                </form>
                @endforeach
                @endif
            </td>
        </tr>
        @endforeach
