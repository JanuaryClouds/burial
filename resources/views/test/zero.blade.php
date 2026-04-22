@extends('layouts.metronic.guest')
@section('content')
    @php
        if (!app()->environment('local', 'testing')) {
            abort(404);
        }
    @endphp
    <title>Ground Zero</title>
    <div class="container-xxl min-vh-100">
        <div class="d-flex flex-column gap-4 my-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ground Zero Form</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">This page and this form is created for testing purposes. The intent was to set the
                        TLC
                        Portal link to be a form containing hidden inputs for a key and the citizen's uuid</p>
                    <div class="row g-1">
                        @php
                            $citizens = App\Models\User::whereNotNull('citizen_uuid')
                                ->inRandomOrder()
                                ->limit(10)
                                ->get();
                        @endphp
                        @foreach ($citizens as $citizen)
                            @php
                                $payload = [
                                    'citizen_uuid' => $citizen->citizen_uuid,
                                    'time' => time(),
                                    'nonce' => Str::uuid()->toString(),
                                ];

                                $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
                                $signature = hash_hmac('sha256', $encoded, config('services.portal.sso.secret'));

                                $url = url('/sso/callback') . "?payload={$encoded}&signature={$signature}";
                            @endphp
                            <div class="col-4 d-flex">
                                <a href="{{ $url }}" class="btn btn-secondary w-100">
                                    {{ $citizen->citizen_uuid }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
