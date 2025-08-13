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
            >Burial Service Request Form</h3>
            <p class="text-lg text-gray-600" style="grid-row">CSWDO Burial Assistance</p>
        </div>
    </div>
    <hr>
    <h3>Details of Deceased</h3>
    <div style="display: table; font-size: small; column-count: 2; column-gap: 5rem; margin-bottom: 2em;">
        <span style="">
            <b style="color: darkgray; font-weight: normal;">Last Name:</b> {{ $assistanceRequest->deceased_lastname }}
        </span>
        <span style="">
            <b style="color: darkgray; font-weight: normal;">First Name:</b> {{ $assistanceRequest->deceased_firstname }}
        </span>
    </div>
    <h3>Representative</h3>
    <div style="display: table; font-size: small; column-gap: 5rem; margin-bottom: 2em;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Name:</b> {{ $assistanceRequest->representative }}
        </span>
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Contact:</b> {{ $assistanceRequest->representative_contact }}
        </span>
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Relationship:</b> {{ $relationships->firstWhere('id', $assistanceRequest->rep_relationship)->name ?? 'Unknown' }} 
        </span>
    </div>
    <h3>Burial Service Details</h3>
    <div style="display: table; font-size: small;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Address:</b> {{ $assistanceRequest->burial_address }} {{ $barangays->firstWhere('id', $assistanceRequest->barangay_id)->name }}
        </span>
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Duration of Burial:</b> {{ Str::limit($assistanceRequest->start_of_burial,10) }} {{ Str::limit($assistanceRequest->end_of_burial, 10) }}
        </span>
    </div>
    <h3>Death Certificate</h3>
    @foreach ($requestImages as $image)
        <img src="data:image/jpeg;base64,{{ base64_encode($image['content']) }}" class="rounded" 
        style="max-width: 100%; height: auto;">
    @endforeach
    <h3>Remarks</h3>
    <p style="font-size: small;">{{ $assistanceRequest->remarks }}</p>
</body>
</html>