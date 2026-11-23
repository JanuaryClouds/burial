@extends('layouts.app')
@section('content')
    <x-card>
        <x-slot:header>Link My Walk-in Records</x-slot:header>
        <p>Enter the private code on your CSWDO walk-in acknowledgment. Your tracking number is not a claim code.</p>
        <p>The records will be linked to <strong>{{ auth()->user()->fullname() }}</strong> ({{ auth()->user()->email }}).</p>
        @can('claim-walk-in')
        <form method="POST" action="{{ route('walk-in.claim.store') }}" autocomplete="off">
            @csrf
            <label for="claim-code" class="form-label">Private Claim Code</label>
            <input id="claim-code" class="form-control mb-4" name="claim_code" type="password" autocomplete="off" spellcheck="false" required maxlength="128">
            <label class="d-block mb-4"><input type="checkbox" name="confirmed" value="1" required> These are my walk-in records, and I confirm linking them to this TLC account.</label>
            <button class="btn btn-primary" type="submit">Link My Records</button>
        </form>
        @else
            <p class="alert alert-info">Please sign out and sign in again through the TLC Citizens Portal before linking your walk-in records.</p>
        @endcan
        <p class="mt-5">If your code is lost or expired, contact CSWDO for identity verification and a replacement. A code can only be used once.</p>
    </x-card>
@endsection
