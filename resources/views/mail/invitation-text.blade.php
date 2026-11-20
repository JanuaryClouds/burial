{{ $systemName }} — CSWDO

You have been invited to create a staff account. Enter your own name, contact number, and password:
{{ $invitationUrl }}

This single-use link expires at {{ \Illuminate\Support\Carbon::parse($expiresAt)->timezone('Asia/Manila')->format('F j, Y, g:i A') }} PHT.
If it expires, ask your superadmin to renew it, then reopen this same link.
If you were not expecting this invitation, ignore this email. Never share your password or access link.
