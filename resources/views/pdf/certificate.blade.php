<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-size: 12px;
            font-family: Calibri;
        }

        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
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

        .title {
            font-weight: bold;
            font-size: 24px;
            text-transform: uppercase;
            font-family: serif;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d !important;
            font-size: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000000;
            padding: 6px;
            text-align: left;
        }

        p {
            font-family: 'sans-serif';
            font-size: 14px;
        }
    </style>
</head>

<body>
    <table style="margin-top: -2rem;">
        <tr>
            <td style="width: 15%; text-align: center;" class="no-border">
                <img src="./images/CSWDO.webp" alt="" class="logo">
            </td>
            <td class="no-border text-center">
                <p>Republika ng Pilipinas<br />
                    Lungsod ng Taguig<br />
                    Tanggapang Panlungsod sa Kagalingang Panlipunan at Pagpapaunlad</p>
            </td>
            <td style="width: 15%; text-align: center;" class="no-border">
                <img src="./images/city_logo.webp" alt="" class="logo">
            </td>
        </tr>
    </table>
    <hr>
    <p class="text-center bold">
        KATIBAYAN NG PAGIGING KWALIPIKADO
        <br><br>
        (Certificate of Eligibility)
    </p>
    <p style="text-align: justify; margin: 0px 10px 0px 10px; line-height: 2.0">
        Pinatutunayan nito na si <strong class="bold underline">{{ $client->fullname() }}</strong>,
        edad <strong class="bold underline">{{ $client->age() }}</strong>, at kasalukuyang may
        pahatirang-sulat
        sa
        <strong class="bold underline">{{ $client->house_no }} {{ $client->street }},
            {{ $client->barangay->name }}</strong>,
        Taguig City. Matapos ang isinagawang ebalwasyon ay kwalipikadong makatanggap ng benepisyo sa ilalim ng
        programang <strong>FUNERAL ASSISTANCE PROGRAM (FAP)</strong> bilang <strong>Namatayan</strong>.
        <br><br>
        Ang rekord ng kaso at ang General Intake Sheet ay nasa pangangalagang kompidensyal ng tanggapang ito.
        <br><br>
        Ang Benepisyaryo ay nirerekomendang bigyan ng tulong-pinansiyal sa halagang <strong>Dalawampung Libong Piso
            (Php20,000.00)</strong> na <strong>manggagaling sa General Fund</strong>.
        <br><br>
        Nilagdaan ngayong ika-
        <strong class="underline">{{ \Carbon\Carbon::parse($client->created_at)->format('d') }}</strong> ng
        <strong class="underline">{{ \Carbon\Carbon::parse($client->created_at)->format('F') }}</strong>,
        <strong class="underline">{{ \Carbon\Carbon::parse($client->created_at)->format('Y') }}</strong> dito sa
        <strong>Tanggapang Panglungsod sa Kagalingang Panlipunan at Pagpapaunlad, Lungsod ng Taguig</strong>.
    </p>
    <hr />
    <p class="text-center bold" style="letter-spacing: 0.5em;">
        SERTIPIKASYON
    </p>
    <div style="text-align: justify; margin: 0px 10px 0px 10px; line-height: 2.0;">
        <p><strong>Sa Kinauukulan:</strong></p>
        <p>
            PINATUTUNAYAN nito na si <strong class="underline">{{ $beneficiary->fullname() }}</strong>
            (<i>namatay</i>) ay tunay na residente ng Lungsod Taguig at maralita o nangangailangan, at kung ganoon ay
            sakop ng <strong>Programang Benepisyo sa Pagpapalibing</strong>.
        </p>
        <p>
            PINATUTUNAYAN din na si <strong class="underline">{{ $client->fullname() }}</strong>
            (<i>aplikante</i>) ay may angkop na ugnayan sa namatay, at karapat-dapat ng tumanggap ng benepisyo.
        </p>
        <p>
            Ang Sertipikasyon na ito ay batay sa personal na panayam at pagsuri ng mga awtentikong dokumento, na
            isinagawa ng mga kawani ng Tanggapang ito. Ang resulta ng mga panayam at mga dokumentong isinumite o nakalap
            ay nakatago sa Tanggapan at maaaring suriin ng tamang awtoridad.
        </p>
        <p>
            Ipinagkaloob ngayong <strong class="underline">{{ \Carbon\Carbon::now()->format('F d, Y') }}</strong> sa
            Lungsod Taguig.
        </p>
    </div>
    <table>
        <tr>
            <td style="text-align: center;" class="no-border">
                <p class="underline">
                    {{ strtoupper($client->fullname()) }}<br />
                </p>
                APLIKANTE
            </td>
            <td style="text-align: center;" class="no-border">
                <p class="underline">
                    {{ strtoupper($socialWelfareOfficer) }}<br />
                </p>
                SOCIAL WELFARE OFFICER III
            </td>
            <td style="text-align: center;" class="no-border">
                <p class="underline">
                    {{ strtoupper($deptHead) }}<br />
                </p>
                CITY GOVERNMENT DEPARTMENT HEAD II
            </td>
        </tr>
    </table>
</body>

</html>
