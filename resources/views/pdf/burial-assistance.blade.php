<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Burial Assistances Report</title>
    <style>
        body {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .text-center {
            text-align: center;
        }

        .title {
            font-weight: bold;
            font-size: 24px;
            text-transform: uppercase;
            font-family: serif;
        }

        .subtitle {
            font-size: 16px;
            text-transform: uppercase;
            font-family: serif;
        }

        .logo {
            width: 70%;
            height: auto;
        }

        .no-border {
            border: none !important;
        }

        th,
        td {
            border: 1px solid #000000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-muted {
            color: #6c757d !important;
            font-size: 8px;
        }
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
                <p class="subtitle text-center" style="font-weight: bold;">Funeral Assistance</p>
                <h2 class="text-center" style="font-family: serif; text-transform: uppercase;">Burial Assistances Report
                </h2>
                <p class="text-center" style="font-family: serif;">
                    {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to
                    {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
            </td>
            <td style="width: 30%; text-align: center;" class="no-border">
                <img src="./images/city_logo.webp" alt="" class="logo">
            </td>
        </tr>
    </table>
    <h2>Charts</h2>
    @if (!empty($charts))
        <table class="no-border" style="width: 100%;">
            @foreach (array_chunk($charts, 2, true) as $chunk)
                <tr>
                    @foreach ($chunk as $id => $chartImage)
                        <td class="no-border" style="width: 50%; text-align: center;">
                            <p>{{ Str::title(Str::replace('-', ' ', $id)) }}</p>
                            <div class="chart">
                                <img src="{{ $chartImage }}" alt="{{ $id }}"
                                    style="max-width:100%; height:auto;" class="text-center">
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
                <th>Tracking Number</th>
                <th>Application Date</th>
                <th>Deceased</th>
                <th>Claimant</th>
                <th>Amount</th>
                <th>Funeraria</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($burialAssistance as $ba)
                <tr>
                    <td>{{ $ba->tracking_no }}</td>
                    <td>{{ \Carbon\Carbon::parse($ba->application_date)->format('F d, Y') }}</td>
                    <td>{{ $ba->deceased->first_name }} {{ Str::limit($ba->deceased->middle_name, 1, '.') }}
                        {{ $ba->deceased->last_name }} {{ $ba?->deceased->suffix }}</td>
                    <td>
                        @if ($ba?->claimantChanges->isNotEmpty())
                            @foreach ($ba->claimantChanges as $cc)
                                @if ($cc->status === 'approved')
                                    {{ $cc->newClaimant->first_name }}
                                    {{ Str::limit($cc->newClaimant->middle_name, 1, '.') }}
                                    {{ $cc->newClaimant->last_name }} {{ $cc?->newClaimant->suffix }}
                                @endif
                            @endforeach
                        @else
                            {{ $ba->claimant->first_name }} {{ Str::limit($ba->claimant->middle_name, 1, '.') }}
                            {{ $ba->claimant->last_name }} {{ $ba?->claimant->suffix }}
                        @endif
                    </td>
                    <td>{{ $ba->amount }}</td>
                    <td>{{ $ba->funeraria }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong> {{ $burialAssistance->count() }}
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Barangay</th>
                <th>Count of Assistance Claimants</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deceasedPerBarangay as $barangayName => $count)
                <tr>
                    <td>{{ $barangayName }}</td>
                    <td>{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong> {{ count($deceasedPerBarangay) }}
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Religion</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deceasedPerReligion as $religionName => $count)
                <tr>
                    <td>{{ $religionName }}</td>
                    <td>{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong>{{ count($deceasedPerReligion) }}
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
