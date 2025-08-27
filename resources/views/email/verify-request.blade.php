<head>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
@php
    $time = \Carbon\Carbon::now()->format('m/d/y h:i:s a');
    $period = \Carbon\Carbon::now()->format('H:i:s');
    if ($period <= 12) {
        $greeting = "Good morning";
    } elseif ($period >= 12 && $period < 18) {
        $greeting = "Good afternoon";
    } else {
        $greeting = "Good evening";
    }
@endphp
<div>
    <h1>{{ $greeting }}</h1>
    <p>Your email address have been recently set to contact by the Taguig City CSWDO and BAO for a burial assistance request.</p>
    <p>Below is the six-digit verification code to verify the request</p>
    <strong>{{ $code }}</strong>
    <p>Do not share this verification code to any person</p>
    <p>If you believe this is a mistake, please contact the Taguig City CSWDO and BAO. Or ignore this email.</p>
    <hr>
    <small>This email is sent via CSWDO Burial Assistance System on {{ $time }}</small>
</div>
