@extends('layouts.app')
@section('content')
<x-card>
    <x-slot:header>Emails</x-slot:header>
    <p>Sent means accepted by SMTP; later bounces are not tracked. Interrupted attempts may already have delivered an email. Manual retries may send duplicates.</p>
    <form method="GET" action="{{ route('system.failed-emails.index') }}" class="row g-4 align-items-end mb-5">
        <div class="col-12 col-md-4 col-lg-3">
            <label for="email-status-filter" class="form-label">Status</label>
            <select id="email-status-filter" name="status" class="form-select"><option value="">All statuses</option>
                @foreach(['pending', 'sending', 'sent', 'failed', 'uncertain'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-4 col-lg-3">
            <label for="email-type-filter" class="form-label">Message</label>
            <select id="email-type-filter" name="type" class="form-select"><option value="">All messages</option>
                @foreach(['password_reset' => 'Password reset', 'user_invitation' => 'Staff invitation', 'mfa_security' => 'Account security'] as $type => $label)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-auto">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
        </div>
    </form>
    @include('partials.dataTable.index', [
        'dataTableId' => 'email-deliveries',
        'columns' => collect([
            ['data' => 'recipient', 'title' => 'Message / recipient'],
            ['data' => 'created', 'title' => 'Created / last attempt'],
            ['data' => 'outcome', 'title' => 'Outcome'],
            ['data' => 'attempts', 'title' => 'Attempts', 'orderable' => false],
            ['data' => 'retry', 'title' => 'Retry', 'orderable' => false, 'searchable' => false],
        ]),
        'bodyView' => 'system.partials.email-rows',
        'emptyMessage' => 'No emails match these filters.',
    ])
</x-card>
<script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/immediate-mail.js') }}" defer></script>
@include('partials.dataTable.script', ['dataTableId' => 'email-deliveries'])
@endsection
