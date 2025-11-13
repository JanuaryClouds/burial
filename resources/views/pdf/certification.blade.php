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
                <p>Tanggapang Panlungsod sa Kagalingang Panlipunan at Pagpapaunlad</p>
                <p>(City Social Welfare and Development Office)</p>
            </td>
            <td style="width: 30%; text-align: center;" class="no-border">
                <img src="./images/city_logo.webp" alt="" class="logo">
            </td>
        </tr>
    </table>
    <hr>
    <p class="text-center bold" style="letter-spacing: 0.5em;">
        SERTIPIKASYON
    </p>
    <div style="text-align: justify; margin: 0px 10px 0px 10px; line-height: 2.0;">
        <p><strong>Sa Kinauukulan:</strong></p>
        <p style="text-indent: 3em;">
            PINATUTUNAYAN nito na si <strong class="underline">{{ $client->beneficiary->first_name }} {{ \Str::limit($client->beneficiary?->middle_name, 1, '.') }} {{ $client->beneficiary->last_name }}</strong>
            (<i>namatay</i>) ay tunay na residente ng Lungsod Taguig at maralita o nangangailangan, at kung ganoon ay sakop ng <strong>Programang Benepisyo sa Pagpapalibing</strong>.
        </p>
        <p style="text-indent: 3em">
            PINATUTUNAYAN din na si <strong class="underline">{{ $client->first_name }} {{ \Str::limit($client?->middle_name, 1, '.') }} {{ $client->last_name }}</strong> 
            (<i>aplikante</i>) ay may angkop na ugnayan sa namatay, at karapat-dapat ng tumanggap ng benepisyo. 
        </p>
        <p style="text-indent: 3em;">
            Ang Sertipikasyon na ito ay batay sa personal na panayam at pagsuri ng mga awtentikong dokumento, na isinagawa ng mga kawani ng Tanggapang ito. Ang resulta ng mga panayam at mga dokumentong isinumite o nakalap ay nakatago sa Tanggapan at maaaring suriin ng tamang awtoridad.
        </p>
        <p style="text-indent: 3em;">
            Ipinagkaloob ngayong <strong class="underline">{{ \Carbon\Carbon::now()->format('F d, Y') }}</strong> sa Lungsod Taguig.
        </p>
        <p class="text-center"">
            Nilagdaan:
        </p>
        <p style="margin: 0px 0px 0px 400px;">
            <strong>EMMA B. REANZARES, RSW</strong>
            <br>
            Social Welfare Officer III
            <br>
            Lisensiyang PRC:: 0029548
            <br>
            May Bisa: December 14, 2027 
            <br>
        </p>
        <br>
        <p class="text-center">
            Pinagtibay ni:
        </p>
        <p style="margin: 0px 0px 0px 400px;">
            <strong>NIKKI ROSE H. OPERARIO, RSW</strong>
            <br>
            Department Head II
            <br>
            Tanggapang Panlungsod sa Kagalingang 
            <br>
            Panlipunan at Pagpapaunlad, Lungsod ng Taguig
        </p>
    </div>
</body>
</html>