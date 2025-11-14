<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assistanceRequest->deceased_firstname }} {{ $assistanceRequest->deceased_lastname }} Burial Assistance Request</title>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body>
    <div style="display: table;">
        <img src="./images/CSWDO.webp" alt="" class="mr-2" style="width: 100px; display: table-cell;">
        <div style="display: table-cell; margin-left: 4em;">
            <h3 class="fw-semibold" 
                style="font-family: sans-serif; grid-row;"
            >Burial Service Request Form</h3>
            <p>CSWDO Burial Assistance</p>
        </div>
    </div>
    <div style="display: table; column-count: 2; font-size: small;">
        <span style="">
            Status: {{ Str::ucfirst($assistanceRequest->status) }}
        </span>
        <span style="margin-left: 2rem; color: darkgray;">
            Date Submitted: {{ $assistanceRequest->created_at->format('F d, Y') }}
        </span>
        <span style="margin-left: 2rem; color: darkgray;">
            Print Date: {{ now()->format('F d, Y') }}
        </span>
    </div>
    <hr>
    <h3>Details of Deceased</h3>
    <div style="display: table; font-size: small; column-count: 2; margin-bottom: 2em;">
        <span style="">
            <b style="color: darkgray; font-weight: normal;">Last Name:</b> {{ $assistanceRequest->deceased_lastname }}
        </span>
        <span style="margin-left: 2rem;">
            <b style="color: darkgray; font-weight: normal;">First Name:</b> {{ $assistanceRequest->deceased_firstname }}
        </span>
    </div>
    <h3>Representative</h3>
    <div style="display: table; font-size: small; column-gap: 5rem; margin-bottom: 2em;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Name:</b> {{ $assistanceRequest->representative }}
        </span>
        <span style="margin-left: 2rem;">
            <b style="color: darkgray; font-weight: normal;">Phone:</b> {{ $assistanceRequest->representative_phone }}
        </span>
        <span style="margin-left: 2rem;">
            <b style="color: darkgray; font-weight: normal;">Email:</b> {{ $assistanceRequest?->representative_email ?? 'N/A' }}
        </span>
        <span style="margin-left: 2rem;">
            <b style="color: darkgray; font-weight: normal;">Relationship:</b> {{ $relationships->firstWhere('id', $assistanceRequest->representative_relationship)->name ?? 'Unknown' }} 
        </span>
    </div>
    <h3>Burial Service Details</h3>
    <div style="display: table; font-size: small;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Address:</b> {{ $assistanceRequest->burial_address }} {{ $barangays->firstWhere('id', $assistanceRequest->barangay_id)->name }}
        </span>
        <span style="margin-left: 2rem;">
            <b style="color: darkgray; font-weight: normal;">Duration of Burial:</b> {{ Str::limit($assistanceRequest->start_of_burial,10) }} {{ Str::limit($assistanceRequest->end_of_burial, 10) }}
        </span>
    </div>
    <h3>Death Certificate</h3>
    @if ($requestImages)
        @foreach ($requestImages as $image)
            <img src="data:image/jpeg;base64,{{ base64_encode($image['content']) }}" class="rounded" 
            style="max-width: 100%; height: auto;">
        @endforeach
    @else
        <p>No images found.</p>
    @endif
    <h3>Remarks</h3>
    <p style="font-size: small;">{{ $assistanceRequest->remarks }}</p>
</body>
</html>