@extends('mail.layout')
@section('mail-content')
<h1 style="font-size:24px;line-height:1.3;margin:0 0 16px;color:#173b70;">Reset your password</h1>
<p>A password reset was requested for your {{ $systemName }} internal account.</p>
<p style="margin:28px 0;"><a href="{{ $resetUrl }}" style="display:inline-block;background:#24549b;color:#ffffff;padding:13px 22px;border-radius:6px;text-decoration:none;font-weight:bold;">Reset password</a></p>
<p>This link expires {{ $expiresIn }} minutes after it was created and can only be used once.</p>
<p style="font-size:13px;color:#5a687a;">If the button does not work, copy this link into your browser:</p>
<p style="font-size:13px;word-break:break-all;"><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
<p>If you did not request this, you can ignore this email.</p>
@endsection
