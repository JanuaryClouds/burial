<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service->deceased_firstname }} {{ $service->deceased_lastname }} Burial Service Form</title>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="min-vh-100 row row-gap-0 vw-100 g-0">
    <div style="display: table;">
        <img src="./images/CSWDO.webp" alt="" class="mr-2" style="width: 100px; display: table-cell;">
        <div style="display: table-cell;">
            <h3 class="fw-semibold" 
                style="font-family: sans-serif; grid-row;"
            >Burial Service Form</h3>
            <p class="text-lg text-gray-600" style="grid-row">CSWDO Burial Assistance</p>
        </div>
    </div>
    <div style="display: table; column-count: 2;">
        <span style="">
            Print Date: {{ now()->format('F d, Y') }}
        </span>
    </div>
    <hr>
    <h3>Details of Deceased</h3>
    <div style="display: table; font-size: small; column-count: 2; column-gap: 5rem; margin-bottom: 2em;">
        <span style="">
            <b style="color: darkgray; font-weight: normal;">Last Name:</b> {{ $service->deceased_lastname }}
        </span>
        <span style="">
            <b style="color: darkgray; font-weight: normal;">First Name:</b> {{ $service->deceased_firstname }}
        </span>
    </div>
    <h3>Representative</h3>
    <div style="display: table; font-size: small; column-gap: 5rem; margin-bottom: 2em;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Name:</b> {{ $service->representative }}
        </span>
        <span style="margin-left: 2rem;">
            <b style="color: darkgray; font-weight: normal;">Phone:</b> {{ $assistanceRequest->representative_phone }}
        </span>
        <span style="margin-left: 2rem;">
            <b style="color: darkgray; font-weight: normal;">Email:</b> {{ $assistanceRequest?->representative_email ?? 'N/A' }}
        </span>
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Relationship:</b> {{ $relationships->firstWhere('id', $service->representative_relationship)->name ?? 'Unknown' }} 
        </span>
    </div>
    <h3>Burial Service Details</h3>
    <div style="display: table; font-size: small;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Address:</b> {{ $service->burial_address }} {{ $barangays->firstWhere('id', $service->barangay_id)->name }}
        </span>
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Duration of Burial:</b> {{ Str::limit($service->start_of_burial,10) }} {{ Str::limit($service->end_of_burial, 10) }}
        </span>
    </div>
    <div style="display: table; font-size: small; margin-top: 1em;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Burial Service Provider:</b> {{ $providers->firstWhere('id', $service->burial_service_provider)->name }}
        </span>
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Collected Funds:</b> Php {{ $service->collected_funds }}
        </span>
    </div>
    <div style="display: table; font-size: small; margin-top: 1em; margin-bottom: 2em;">
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Address of Burial Service Provider:</b> {{ $service ? $service->provider->address : '' }}, {{ $service ? $service->provider->barangay->name : '' }}
        </span>
        <span style="table-cell">
            <b style="color: darkgray; font-weight: normal;">Burial Service Provider Contact:</b> {{ $service ? $service->provider->contact_details : '' }}
        </span>
    </div>
    <h3>Images</h3>
    @foreach ($serviceImages as $image)
        <img src="./storage/{{$image}}" class="rounded" 
        style="max-width: 100%; height: auto;">
    @endforeach
    <h3>Remarks</h3>
    <p style="font-size: small;">{{ $service->remarks }}</p>
</body>
</html>