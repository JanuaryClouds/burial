@extends('mail.layout')
@section('mail-content')
<h1 style="font-size:24px;line-height:1.3;margin:0 0 16px;color:#173b70;">{{ $systemName }} account security</h1>
<p>{{ $securityEvent }} at {{ $occurredAt }}.</p>
<p>If you did not make this change, contact your system administrator immediately. Never share authenticator or recovery codes.</p>
@endsection
