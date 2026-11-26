                @foreach ($intakes as $intake)
                    <tr>
                        <td>{{ data_get($intake->data, 'identity.first_name') }} {{ data_get($intake->data, 'identity.last_name') }}</td>
                        <td>{{ $intake->encoder->fullname() }}</td><td>{{ $intake->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $intake->application_uuid ? ($intake->claimed_at ? 'Linked to citizen account' : 'Submitted; account not linked') : 'Draft' }}</td>
                        <td>
                            @if (! $intake->application_uuid)
                                <a href="{{ route('walk-in.edit', $intake) }}">Resume Draft</a>
                            @else
                                <a wire:navigate href="{{ route('application.show', $intake->application) }}">{{ $intake->application->tracking_no }}</a>
                                @if (! $intake->claimed_at)
                                    <details class="mt-3"><summary>Replace lost or expired claim code</summary>
                                        <form method="POST" action="{{ route('walk-in.code.replace', $intake) }}" class="mt-3">
                                            @csrf
                                            <label class="form-label" for="reason-{{ $intake->uuid }}">Reason</label>
                                            <textarea id="reason-{{ $intake->uuid }}" class="form-control mb-3" name="reason" required maxlength="2000"></textarea>
                                            <label class="d-block mb-3"><input type="checkbox" name="identity_verified" value="1" required> I verified the applicant's identity.</label>
                                            <button class="btn btn-sm btn-warning" type="submit">Issue Replacement Code</button>
                                        </form>
                                    </details>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
