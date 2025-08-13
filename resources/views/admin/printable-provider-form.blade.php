<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div style="display: table;">
        <img src="./images/CSWDO.webp" alt="" class="mr-2" style="width: 100px; display: table-cell;">
        <div style="display: table-cell;">
            <h3 class="fw-semibold" 
                style="font-family: sans-serif; grid-row;"
            >Burial Service Provider Form</h3>
            <p class="text-lg text-gray-600" style="grid-row">CSWDO Burial Assistance</p>
        </div>
    </div>
    <hr>
    <h3>Details of Burial Service Provider</h3>
    <div style="display: table; font-size: small; column-count: 2; column-gap: 5rem; margin-bottom: 1em;">
        <span style="">
            <b style="color: darkgray; font-weight: normal;">Company Name:</b> {{ $provider->name }}
        </span>
    </div>
    <div style="display: table; font-size: small; column-count: 2; column-gap: 5rem; margin-bottom: 1em;">
        <span style="">
            <b style="color: darkgray; font-weight: normal;">Company Address:</b> {{ $provider->address }} {{ $barangays->firstWhere('id', $provider->barangay_id)->name }}
        </span>
    </div>
    <div style="display: table; font-size: small; column-count: 2; column-gap: 5rem; margin-bottom: 1em;">
        <span style="">
            <b style="color: darkgray; font-weight: normal;">Contact Details:</b> {{ $provider->contact_details }}
        </span>
    </div>
    <h3>Remarks</h3>
    <p style="font-size: small;">{{ $provider->remarks }}</p>
</body>
</html>