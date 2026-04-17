<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $client->fullname() }} General Intake Form</title>
    <style>
        @page {
            margin: 0px 0px 0px 0px;
        }

        body {
            font-family: serif;
            font-size: 11px;
            margin: 0px 0px 0px 0px;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000000;
            padding: 1px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .field-section {
            width: fit-content;
            padding: 0.25rem 1rem 0.25rem 1rem;
        }

        .field-row td {
            width: fit-content;
            padding: 0.1rem 0.5rem 0.1rem 0rem;
        }

        .field-input {
            width: fit-content;
            text-wrap: nowrap;
            text-decoration: underline;
            font-weight: bold;
            padding-right: 2rem;
        }

        .divider {
            width: 100%;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
            background: rgb(217, 217, 217);
            padding: 0.25rem 1rem 0.25rem 1rem;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .logo {
            width: 50%;
            height: auto;
        }

        .border-none td {
            border: none !important;
        }
    </style>
</head>

<body>
    <table style="margin-top: 1em; width: 100%;">
        <tr class="border-none">
            <td style="width: 20%; text-align: center;">
                <img src="./images/city_logo.webp" alt="Taguig City Logo" class="logo">
            </td>
            <td>
                <p class="text-center text-bold text-uppercase">
                    City Government of Taguig
                </p>
                <p class="text-center text-uppercase">
                    Gen. Luna St. Tuktukan, Taguig City
                </p>
                <p class="text-center text-bold text-uppercase" style="font-size: 9px;">
                    City Social Welfare and Development Office
                </p>
                <p class="text-center text-bold text-uppercase" style="font-size: 14px;">
                    General Intake Sheet
                </p>
            </td>
            <td style="width: 20%; text-align: center;">
                <img src="./images/CSWDO.webp" alt="CSWDO Logo" class="logo">
            </td>
        </tr>
    </table>
    <div class="divider">
        I. client's identifying information
    </div>
    <table class="field-section">
        @foreach ($data['client'] as $row)
            <tr class="border-none field-row">
                @foreach ($row as $field => $value)
                    <td>{{ $field }}:</td>
                    <td class="field-input">{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>

    <div class="divider">
        II. beneficiary's identifying information
    </div>
    <table class="field-section">
        @foreach ($data['beneficiary'] as $row)
            <tr class="border-none field-row">
                @foreach ($row as $field => $value)
                    <td>{{ $field }}:</td>
                    <td class="field-input">{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
    <div class="divider">
        III. beneficiary's family composition
    </div>
    <table style="width: 100%; padding: 0rem 1rem 0rem 1rem;">
        <tr>
            <td class="text-center" style="padding: 0.5rem 0rem 0.5rem 0rem;">Name</td>
            <td class="text-center" style="padding: 0.5rem 0rem 0.5rem 0rem;">Sex</td>
            <td class="text-center" style="padding: 0.5rem 0rem 0.5rem 0rem;">Age</td>
            <td class="text-center" style="padding: 0.5rem 0rem 0.5rem 0rem;">Civil Status</td>
            <td class="text-center" style="padding: 0.5rem 0rem 0.5rem 0rem;">Relationship</td>
            <td class="text-center" style="padding: 0.5rem 0rem 0.5rem 0rem;">Occupation</td>
            <td class="text-center" style="padding: 0.5rem 0rem 0.5rem 0rem;">Income</td>
        </tr>
        @foreach ($family as $member)
            <tr>
                <td style="font-weight: bold;">{{ $member->name }}</td>
                <td style="font-weight: bold;">{{ $member->sex?->name ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $member->age }}</td>
                <td style="font-weight: bold;">{{ $member->civil?->name ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $member->relationship?->name ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $member->occupation ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $member->income ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </table>
    <div class="divider">
        IV. assessment
    </div>
    <table style="width: 100%; padding: 0rem 1rem 0rem 1rem;">
        <tr>
            <td style="border: none;">
                <p style="font-weight: bold">a. Problem Presented</p>
                <p>{{ $data['assessment']['problem_presented'] }}</p>
            </td>
            <td style="border: none; border-left: 1px solid #000000; padding-left: 1rem;">
                <p style="font-weight: bold">b. Social Worker's Assessment</p>
                <p>{{ $data['assessment']['swa'] }}</p>
            </td>
        </tr>
    </table>
    <div class="divider">
        V. recommended services and assistance
    </div>
    <table style="width: 100%; padding: 0rem 1rem 0rem 1rem;">
        <tr class="border-none">
            <td style="font-weight: bold;">
                Nature of Service/Assistance
            </td>
        </tr>
        <tr class="border-none">
            <td>
                @foreach ($assistances as $assistance)
                    <table>
                        <tr class="">
                            <td style="text-decoration: underline;">
                                @if ($assistance == 'Burial')
                                    &nbsp;
                                    /
                                    &nbsp;
                                @else
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                @endif
                            </td>
                            <td>
                                {{ $assistance }}
                            </td>
                        </tr>
                    </table>
                @endforeach
                <table>
                    <tr>
                        <td style="text-decoration: underline;">
                            @if (isset($referral))
                                &nbsp;
                                /
                                &nbsp;
                            @else
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            Referral:
                        </td>
                        <td style="text-decoration: underline">
                            @if (isset($referral))
                                {{ $referral->referral_to }}
                            @else
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                &nbsp;
                            @endif
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td style="text-decoration: underline;">
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                        </td>
                        <td>Others, Specify:</td>
                        <td style="text-decoration: underline;">
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <p style="margin-top: 0.25rem;">Amount of Assistance to be Extended</p>
                <table>
                    <tr>
                        <td>Php</td>
                        <td class="field-input">
                            {{ $recommendation->amount ?? 'N/A' }}
                        </td>
                    </tr>
                </table>
                <p style="margin-top: 2rem;">Mode of Financial Assistance</p>
                <table>
                    <tr>
                        @foreach ($moa as $mode)
                            <td style="text-decoration: underline;">
                                @if (isset($recommendation))
                                    @if ($recommendation->moa == $mode->id)
                                        &nbsp;
                                        /
                                        &nbsp;
                                    @else
                                        &nbsp;
                                        &nbsp;
                                        &nbsp;
                                        &nbsp;
                                    @endif
                                @else
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                @endif
                            </td>
                            <td>{{ Str::ucfirst($mode->name) }}</td>
                        @endforeach
                    </tr>
                </table>
                <table style="width: 20%; margin: 1rem 0rem 1rem 0rem;">
                    <tr style="border: 1px solid #000000;">
                        <td style="width: 100px; height: 100px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-center">
                                (Thumb Mark)
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="width: 100%; margin-top: 1rem;">
        <tr>
            <td style="width: 30%; border: none;"></td>
            <td style="border: none;border-top: 1px solid #000000; width: 40%;">
                <p class="text-center">
                    Client's Signature
                </p>
            </td>
            <td style="width: 30%; border: none;"></td>
        </tr>
    </table>
    <table style="width: 100%; margin-top: 1rem; padding: 0rem 1rem 0rem 1rem;">
        <tr>
            <td style="border: none;">
                <table>
                    <tr class="border-none">
                        <td>Name of Client:</td>
                        <td class="field-input">{{ $client->fullname() }}</td>
                    </tr>
                    <tr>
                        <td style="border: none;">Address:</td>
                        <td style="border: none; border-bottom: 1px solid #000000; width: 300px;">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="border: none;">
                <table>
                    <tr class="border-none">
                        <td>Interviewed By:</td>
                        <td>{{ $social_welfare_officer }}</td>
                    </tr>
                    <tr class="border-none">
                        <td></td>
                        <td>Name and Signature of Social Worker</td>
                    </tr>
                </table>
                <table style="margin-top: 2rem;">
                    <tr class="border-none">
                        <td>Reviewed & Approved By:</td>
                        <td>{{ $dept_head }}</td>
                    </tr>
                    <tr class="border-none">
                        <td></td>
                        <td>Name and Signature of Department Head</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
