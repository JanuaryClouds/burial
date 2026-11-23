<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Walk-in Acknowledgment</title><link rel="stylesheet" href="{{ asset('css/walk-in-receipt.css') }}"></head>
<body>
    <main>
        <h1>CSWDO Walk-in Acknowledgment</h1>
        <p><strong>Applicant:</strong> {{ data_get($intake->data, 'identity.first_name') }} {{ data_get($intake->data, 'identity.last_name') }}</p>
        <p><strong>Application Tracking Number:</strong> {{ $intake->application->tracking_no }}</p>
        <p>Submitted {{ $intake->application->created_at->format('d M Y H:i') }}</p>
        <h2>Private Claim Code</h2>
        <p class="code">{{ $code }}</p>
        <p>Valid until {{ $intake->claim_expires_at->format('d M Y H:i') }}. Single use.</p>
        <ol><li>Sign in through the TLC Citizens Portal and open the Funeral Assistance System.</li><li>Select <strong>Link My Walk-in Records</strong>.</li><li>Enter this private code and confirm the account shown is yours.</li></ol>
        <p><strong>Keep this code private.</strong> Anyone with this code and a citizen account could link these records. It is different from your public tracking number.</p>
        <p>Print or securely save this acknowledgment now. The system cannot display this code again. If it is lost or expired, contact CSWDO for verification and a replacement.</p>
        <nav><button type="button" id="print-receipt">Print Acknowledgment</button> <a href="{{ route('application.show', $intake->application) }}">Open Application</a> <a href="{{ route('walk-in.index') }}">Back to Intakes</a></nav>
    </main>
    <script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/walk-in-receipt.js') }}" defer></script>
</body>
</html>
