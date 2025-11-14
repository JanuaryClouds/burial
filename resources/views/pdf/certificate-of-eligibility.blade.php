<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {font-size: 12px; font-family: 'Book Antiqua';}
        .bold {font-weight: bold;}
        .underline {text-decoration: underline;}
        .subtitle {font-size: 16px; text-transform: uppercase; font-family: serif;}
        .logo {width: 70%; height: auto;}
        .no-border {border: none !important;}
        .title {font-weight: bold; font-size: 24px; text-transform: uppercase; font-family: serif;}
        .text-center {text-align: center;}
        .text-muted {color: #6c757d !important; font-size: 8px;}

        th {background-color: #f2f2f2;}
        table {width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td {border: 1px solid #000000; padding: 6px; text-align: left;}
        p {font-family: 'serif'; font-size: 14px;}
    </style>
</head>
<body>
    <table class="bold">
        <tr>
            <td style="width: 30%; text-align: center;" class="no-border">
                <img src="./images/CSWDO.webp" alt="" class="logo">
            </td>
            <td class="no-border text-center">
                <p>Republika ng Pilipinas</p>
                <p>PAMAHALAANG LUNGSOD NG TAGUIG</p>
                <p>TANGGAPAN NG PANGLUNGSOD SA KAGALINGANG PANLIPUNAN AT PAGPAPAUNLAD</p>
                <p>(City Social Welfare and Development Office)</p>
            </td>
            <td style="width: 30%; text-align: center;" class="no-border">
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
    <p style="text-align: justify; margin: 0px 10px 0px 10px; line-height: 1.5">
        Pinatutunayan nito na si <strong class="bold underline">{{ $claimant->first_name . ' ' . $claimant?->middle_name . ' ' . $claimant->last_name . ' ' . $claimant->suffix }}</strong>, 
        edad <strong class="bold underline">{{ $claimant->client->age }}</strong>, at kasalukuyang may pahatirang-sulat sa 
        <strong class="bold underline">{{ $claimant->address }}, {{ $claimant->barangay->name }}</strong>,
        Taguig City. Matapos ang isinagawang ebalwasyon ay kwalipikadong makatanggap ng benepisyo sa ilalim ng programang <strong>FUNERAL ASSISTANCE PROGRAM (FAP)</strong> bilang <strong>Namatayan</strong>.
        <br><br>
        Ang rekord ng kaso at ang General Intake Sheet ay nasa pangangalagang kompidensyal ng tanggapang ito.
        <br><br>
        Ang Benepisyaryo ay nirerekomendang bigyan ng tulong-pinansiyal sa halagang <strong>Dalawampung Libong Piso (Php20,000.00)</strong> na <strong>manggagaling sa General Fund</strong>.
        <br><br>
        Nilagdaan ngayong ika-
        <strong class="underline">{{ \Carbon\Carbon::parse($claimant->created_at)->format('d') }}</strong> ng 
        <strong class="underline">{{ \Carbon\Carbon::parse($claimant->created_at)->format('F') }}</strong>, 2025 dito sa 
        <strong>Tanggapang Panglungsod sa Kagalingang Panlipunan at Pagpapaunlad, Lungsod ng Taguig</strong>.
        <br><br><br><br>
        ____________________________________________________
        <br>
        <strong>Lagda sa itaas ng Pangalan ng Benepisyaryo</strong>
        <br><br><br><br>
        Nagsagawa ng Ebalwasyon
        <br><br>
        ______________________________
        <br>
        <strong>EMMA B. REANZARES, RSW</strong>
        <br>
        Social Welfare Officer III
        <br><br><br><br>
        Nirepaso at pinahuntulutan ni:
        <br>
        _______________________________
        <br>
        <strong>NIKKI ROSE H. OPERARIO, RSW</strong>
        <br>
        City Gov't Dept. Head II, CSWDO
    </p>
</body>
</html>