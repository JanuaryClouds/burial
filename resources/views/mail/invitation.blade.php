@extends('mail.layout')
@section('mail-content')
<h1 style="font-size:24px;line-height:1.3;margin:0 0 16px;color:#173b70;">You're invited to join</h1>
<p>A superadmin has invited you to create a staff account for {{ $systemName }}.</p>
<p>Complete your name and contact details, then choose your own password.</p>
<p style="margin:28px 0;"><a href="{{ $invitationUrl }}" style="display:inline-block;background:#24549b;color:#ffffff;padding:13px 22px;border-radius:6px;text-decoration:none;font-weight:bold;">Complete your account</a></p>
<p>This single-use link expires at {{ \Illuminate\Support\Carbon::parse($expiresAt)->timezone('Asia/Manila')->format('F j, Y, g:i A') }} PHT (24 hours after creation or renewal).</p>
<p>If it expires, ask your superadmin to renew it. You can then reopen this same email link.</p>
<p style="font-size:13px;color:#5a687a;">If the button does not work, copy this link into your browser:</p>
<p style="font-size:13px;word-break:break-all;"><a href="{{ $invitationUrl }}">{{ $invitationUrl }}</a></p>
<p>If you were not expecting this invitation, ignore this email.</p>
@endsection
