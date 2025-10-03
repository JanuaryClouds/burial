<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Claimants Report</title>
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
                <h2 class="text-center" style="font-family: serif; text-transform: uppercase;">Assistance Claimants Report</h2>
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
                <th>Full Name (First Name MI. Last Name, Suffix)</th>
                <th>Burial Assistance Tracking Number</th>
                <th>Relationship of Deceased to Claimant</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($claimants as $c)
                <tr>
                    <td>
                        {{ $c->first_name }} 
                        {{ $c->middle_name == null ? '' : Str::limit($c?->middle_name, 1, '.') }} 
                        {{ $c->last_name }} 
                        {{ $c?->suffix }} 
                    </td>
                    <td>
                        @if ($c->oldClaimantChanges->isNotEmpty() || $c->newClaimantChanges->isNotEmpty())
                            @foreach ($c->newClaimantChanges as $ncc)
                                @if ($ncc->status === 'approved')
                                    @if ($ncc->newClaimant->id == $c->id)
                                        New claimant of {{ $ncc->burialAssistance?->tracking_no }}
                                    @endif
                                @endif
                            @endforeach
                            @foreach ($c->oldClaimantChanges as $occ)
                                @if ($occ->status === 'approved')
                                    @if ($occ->oldClaimant->id == $c->id)
                                        Old claimant of {{ $occ->burialAssistance?->tracking_no }}
                                    @endif
                                @endif
                            @endforeach
                        @else
                            @foreach ($c->burialAssistance as $ba)
                                {{ $ba->tracking_no }}
                            @endforeach
                        @endif

                    </td>
                    <td>{{ $c->relationship->name }}</td>
                    <td>{{ $c->address }}, {{ $c->barangay->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong>{{ $claimants->count() }}
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Barangay</th>
                <th>Count of Assistance Claimants</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barangays as $b)
                <tr>
                    <td>{{ $b->name }}</td>
                    <td>{{ $b->claimant->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong> {{ $barangays->count() }}
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Relationship</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($relationships as $r)
                <tr>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->claimant->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Total: </strong>{{ $relationships->count() }}
</body>
</html>