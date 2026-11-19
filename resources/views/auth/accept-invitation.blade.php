@extends('layouts.guest', ['recoveryPage' => true, 'guestPageTitle' => 'Complete your staff account'])
@section('content')
<main class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="card w-100 mw-500px"><div class="card-body">
        <h1>Complete your staff account</h1>
        <p>Email: {{ $invitation->email }}</p>
        <form method="POST" action="{{ route('invitation.accept', ['token' => $token]) }}" data-immediate-mail>
            @csrf
            @foreach(['first_name' => 'First name', 'middle_name' => 'Middle name (optional)', 'last_name' => 'Last name', 'suffix' => 'Suffix (optional)', 'contact_number' => 'Contact number'] as $field => $label)
                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                <input class="form-control mb-3" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}"
                    type="{{ $field === 'contact_number' ? 'tel' : 'text' }}" maxlength="{{ $field === 'contact_number' ? 32 : ($field === 'suffix' ? 64 : 255) }}"
                    @required(in_array($field, ['first_name', 'last_name', 'contact_number']))>
            @endforeach
            <p>Use at least 12 characters with uppercase and lowercase letters, a number, and a symbol. Maximum 72 bytes; spaces are preserved.</p>
            <x-form.password name="password" label="Password" />
            <x-form.password name="password_confirmation" label="Confirm password" />
            <button class="btn btn-primary" type="submit" data-sending-label="Creating account…">Complete onboarding</button>
            <p role="status" aria-live="polite" data-mail-status></p>
            @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
        </form>
    </div></div>
</main>
@endsection
