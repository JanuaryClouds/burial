<head>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
@php
    $time = \Carbon\Carbon::now()->format('m/d/y h:i:s a');
    $period = \Carbon\Carbon::now()->format('H:m:s');
    if ($period <= 12) {
        $greeting = "Good morning";
    } elseif ($period >= 12 && $period < 18) {
        $greeting = "Good afternoon";
    } else {
        $greeting = "Good evening";
    }
@endphp
<h1>{{ $subjectLine }}</h1>
<p>{{ $greeting }} {{ $provider->name }},</p>
<p>{!! nl2br(e($messageBody)) !!}</p>
<hr>
<small>This email is sent via CSWDO Burial Assistance System on {{ $time }}</small>