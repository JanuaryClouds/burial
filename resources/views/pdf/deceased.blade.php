<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deceased Report</title>
    <style>
        body {font-size: 12px;}
        table {width: 100%; border-collapse: collapse; margin-top: 20px;}
        .text-center {text-align: center;}
        .title {font-weight: bold; font-size: 24px; text-transform: uppercase; font-family: serif;}
        .subtitle {font-size: 16px; text-transform: uppercase; font-family: serif;}
        .logo {width: 70%; height: auto;}
        .no-border {border: none !important;}
        th, td {border: 1px solid #000000; padding: 6px; text-align: left;}
        th {background-color: #f2f2f2;}
        .text-muted {color: #6c757d !important; font-size: 8px;}
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 30%; text-align: center;" class="no-border">
                <img src="./images/CSWDO.webp" alt="" class="logo">
            </td>
            <td class="no-border">
                <h1 class="title text-center">Taguig City CSWDO</h1>
                <p class="subtitle text-center" style="font-weight: bold;">Burial Assistance</p>
                <h2 class="text-center" style="font-family: serif; text-transform: uppercase;">Deceased Persons Report</h2>
                <p class="text-center" style="font-family: serif;">{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
            </td>
            <td style="width: 30%; text-align: center;" class="no-border">
                <img src="./images/city_logo.webp" alt="" class="logo">
            </td>
        </tr>
    </table>
    <h2>Charts</h2>
    @if(!empty($charts))
        <table class="no-border" style="width: 100%;">
            @foreach (array_chunk($charts, 2, true) as $chunk)
                <tr>
                    @foreach($chunk as $id => $chartImage)
                        <td class="no-border" style="width: 50%; text-align: center;">
                            <p>{{ Str::title(Str::replace('-', ' ', $id)) }}</p>
                            <div class="chart">
                                <img src="{{ $chartImage }}" alt="{{ $id }}" style="max-width:100%; height:auto;" class="text-center">
                            </div>
                        </td>
                    @endforeach
                    @if (count($chunk) < 2)
                        <td class="no-border" style="width: 50%;"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endif
    <h2>Details</h2>
    <table>
        <thead>
            <tr>
                <th>Assistance Tracking Number</th>
                <th>Full Name (First Name MI. Last Name, Suffix)</th>
                <th>Gender</th>
                <th>Religion</th>
                <th>Date of Birth</th>
                <th>Date of Death</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deceased as $d)
                <tr>
                    <td>
                        {{ $d->burialAssistance->tracking_no }}
                    </td>
                    <td>{{ $d->first_name }} {{ $d->middle_name == null ? '' : Str::limit($d?->middle_name, 1, '.') }} {{ $d->last_name }} {{ $d?->suffix }}</td>
                    <td>{{ $d->gender == 1 ? 'Male' : 'Female' }}</td>
                    <td>{{ $d->religion->name }}</td>
                    <td>{{ $d->date_of_birth }}</td>
                    <td>{{ $d->date_of_death }}</td>
                    <td>{{ $d->address }}, {{ $d->barangay->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong>{{ $deceased->count() }}
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Barangay</th>
                <th>Count of Deceased Persons</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barangays as $b)
                <tr>
                    <td>{{ $b->name }}</td>
                    <td>{{ $b->deceased->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong> {{ $barangays->count() }}
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Religion</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($religions as $r)
                <tr>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->deceased->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong>{{ $religions->count() }}
    <table>
        <tbody>
            <tr>
                <td class="no-border text-muted">
                    Report Generated at {{ \Carbon\Carbon::now()->toISOString() }}
                </td>
                <td class="no-border text-muted" style="text-align: right;">
                    Generated by 
                    {{ auth()->user()->first_name }} 
                    {{ auth()->user()->last_name }}
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>