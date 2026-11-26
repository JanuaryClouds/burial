@extends('layouts.app')
@section('content')
<x-card>
    <x-slot:header>Document Cleanup</x-slot:header>
    <p>Preview abandoned Word files before removing them from private document storage. Only unreferenced DOCX templates and generated downloads at least 24 hours old are eligible. Referenced templates, including those belonging to deleted assistance types, are preserved.</p>
    <p>Application records and uploaded application images are not affected. File deletion is permanent.</p>

    @if(session('cleanup_error'))
        <div class="alert alert-warning text-gray-900" role="alert">{{ session('cleanup_error') }}</div>
    @endif
    @if($result = session('cleanup_result'))
        <div class="alert {{ $result['status'] === 'completed' ? 'alert-success' : 'alert-warning' }} text-gray-900" role="status">
            <p class="fw-bold mb-2">{{ $result['status'] === 'completed' ? 'Cleanup completed.' : 'Cleanup stopped or completed with issues.' }}</p>
            <p class="mb-2">Deleted: {{ $result['deleted'] }}. Skipped: {{ $result['skipped'] }}. Failed: {{ $result['failed'] }}. Unprocessed: {{ $result['unprocessed'] }}.</p>
            @if($result['initial_failed'])
                <p class="mb-2">Document references or storage could not be inspected. No files were deleted.</p>
            @endif
            <p class="mb-0">Skipped files were missing, changed, or no longer eligible. Failed and unprocessed files may remain. Create a new preview to inspect what remains; deletion is never retried automatically.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('system.document-cleanup.preview') }}" data-cleanup-form class="mb-5">
        @csrf
        <button class="btn btn-primary w-100 w-sm-auto" type="submit">
            <span data-cleanup-idle>{{ $preview ? 'Create new preview' : 'Preview eligible files' }}</span>
            <span data-cleanup-busy hidden><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Inspecting files…</span>
        </button>
    </form>

    @if($preview)
        <h2 class="fs-4">Preview batch</h2>
        <p>{{ count($preview['files']) }} eligible file(s), {{ number_format(array_sum(array_column($preview['files'], 'size'))) }} bytes in this batch. No files have been deleted by this preview.</p>
        <p class="text-gray-700">This preview expires at {{ \Illuminate\Support\Carbon::createFromTimestamp($preview['expires'])->timezone(config('app.timezone'))->format('F j, Y g:i A T') }}.</p>
        @if($preview['limited'])
            <div class="alert alert-info text-gray-900" role="status">The scan stopped at its 100-file or 10-second limit. This is an incomplete scan; totals describe this batch only. More files may remain. After cleaning, create another preview.</div>
        @endif
        @if(count($preview['failed']))
            <div class="alert alert-warning text-gray-900" role="alert">{{ count($preview['failed']) }} file(s) could not be inspected and are excluded from this batch. Try a new preview later.</div>
        @endif
        @if(count($preview['files']))
            <div class="table-responsive mb-5">
                <table class="table table-row-bordered align-middle">
                    <caption>Eligible files in this preview batch</caption>
                    <thead><tr><th scope="col" class="min-w-250px">Relative filename</th><th scope="col">Category</th><th scope="col">Last modified</th><th scope="col" class="text-end">Size (bytes)</th></tr></thead>
                    <tbody>
                    @foreach($preview['files'] as $file)
                        <tr>
                            <td class="text-break">{{ $file['path'] }}</td>
                            <td>{{ $file['category'] }}</td>
                            <td class="text-nowrap">{{ \Illuminate\Support\Carbon::createFromTimestamp($file['modified'])->timezone(config('app.timezone'))->format('M j, Y g:i A T') }}</td>
                            <td class="text-end">{{ number_format($file['size']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <form method="POST" action="{{ route('system.document-cleanup.clean') }}" data-cleanup-form>
                @csrf
                <input type="hidden" name="preview_token" value="{{ $token }}">
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="confirm-cleanup" name="confirm_deletion" value="1" required>
                    <label class="form-check-label text-gray-800" for="confirm-cleanup">I confirm permanent deletion of the eligible files in this preview batch.</label>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="cleanup-mfa-code">Fresh authenticator code</label>
                    <input class="form-control mw-250px" id="cleanup-mfa-code" name="mfa_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required aria-describedby="cleanup-mfa-help">
                    <p id="cleanup-mfa-help" class="form-text text-gray-700">A code works only once. Wait for the next code if you just used it to sign in or approve a change. Recovery codes cannot approve cleanup.</p>
                </div>
                <button class="btn btn-danger w-100 w-sm-auto" type="submit">
                    <span data-cleanup-idle>Delete previewed files</span>
                    <span data-cleanup-busy hidden><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Cleaning files…</span>
                </button>
                <p class="form-text text-gray-700 mt-3">Eligibility is checked again before deletion. Each request processes only this batch and stops between files after 10 seconds.</p>
            </form>
        @else
            <p>No eligible files were found in this scan.</p>
        @endif
    @elseif($token)
        <p class="text-muted">Your previous preview is unavailable or expired. Create a new preview before cleaning.</p>
    @endif
</x-card>
<script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/document-cleanup.js') }}" defer></script>
@endsection
