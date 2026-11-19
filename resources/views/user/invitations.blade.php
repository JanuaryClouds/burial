@extends('layouts.app')
@section('content')
<x-card>
    <x-slot:header>Staff invitations</x-slot:header>
    <div class="d-flex justify-content-end mb-4">
        <a class="btn btn-sm btn-light-primary" href="{{ route('system.failed-emails.index', ['type' => 'user_invitation']) }}">Invitation Email Delivery Log</a>
    </div>
    @if(session('success'))<p class="alert alert-info" role="status">{{ session('success') }}</p>@endif
    <p>Invitees supply their own name, contact number, and password. All receive staff membership.</p>
    <form method="POST" action="{{ route('user.invitations.store') }}" data-immediate-mail data-refresh-invitations>
        @csrf
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <label for="invite-email" class="form-label">Email address</label>
                <input id="invite-email" name="email" type="email" required maxlength="254" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="col-12 col-md-8">
                <label for="invite-roles" class="form-label">Optional operational roles</label>
                <select id="invite-roles" name="roles[]" class="form-select" multiple data-control="select2" data-width="100%" data-placeholder="Select operational roles (optional)">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(in_array($role, old('roles', []), true))>{{ $role }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Create and send invitation</button>
        <p role="status" aria-live="polite" data-mail-status></p>
    </form>
    @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
    <x-callout class="border-warning bg-light-warning my-5">
        <x-slot:title><strong>Invitation expiry</strong></x-slot:title>
        Links last 24 hours. Renew keeps the same link valid for 24 hours from now without sending email.
        Resend renews expired links and may send a duplicate email.
    </x-callout>
    <p data-invitation-action-status role="status" aria-live="polite"></p>
    <p class="text-muted" data-invitation-refresh-status role="status" aria-live="polite">Invitations refresh automatically every 10 seconds.</p>
    <div data-invitations-live data-refresh-url="{{ route('user.invitations.index') }}">
    @include('partials.dataTable.index', [
        'dataTableId' => 'staff-invitations',
        'columns' => collect([
            ['data' => 'email', 'title' => 'Email'],
            ['data' => 'expiry', 'title' => 'Status / expiry'],
            ['data' => 'roles', 'title' => 'Roles', 'orderable' => false, 'searchable' => false, 'width' => '35%'],
            ['data' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
        ]),
        'bodyView' => 'user.partials.invitation-rows',
        'emptyMessage' => 'No invitations.',
    ])
    </div>
</x-card>
<script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/immediate-mail.js') }}" defer></script>
@include('partials.dataTable.script', ['dataTableId' => 'staff-invitations'])
<script type="module" nonce="{{ $nonce ?? '' }}" src="{{ asset('js/invitations-live.js') }}"></script>
@endsection
