<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $systemName }}</title></head>
<body style="margin:0;padding:0;background:#f2f5f9;color:#243247;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;table-layout:fixed;background:#f2f5f9;"><tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;table-layout:fixed;background:#ffffff;border:1px solid #dce3ec;border-radius:12px;">
<tr><td align="center" style="padding:28px 24px 22px;border-bottom:4px solid #24549b;">
<img src="{{ $message->embed($logoPath) }}" alt="CSWDO" width="80" height="80" style="display:block;width:80px;height:80px;margin:0 auto 12px;">
<p style="margin:0;font-size:20px;font-weight:bold;color:#173b70;">{{ $systemName }}</p>
<p style="margin:4px 0 0;font-size:13px;color:#5a687a;">City Social Welfare and Development Office</p>
</td></tr>
<tr><td style="padding:28px 24px;text-align:left;font-size:16px;overflow-wrap:anywhere;word-wrap:break-word;">@yield('mail-content')</td></tr>
<tr><td style="padding:20px 24px;background:#f8fafc;border-top:1px solid #dce3ec;font-size:12px;color:#5a687a;text-align:center;">
This is an automated message from {{ $systemName }}. Never share your password or account access links.
</td></tr></table></td></tr></table>
</body></html>
