<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $page_title ?? config('app.name') }}</title>
<script nonce="{{ $nonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.20/dist/sweetalert2.all.min.js"
    integrity="sha256-AdsOUa1T5MFNHdura152IyIfRU9R4LYlXbqyGXv2z+g=" crossorigin="anonymous"></script>
<script nonce="{{ $nonce ?? '' }}" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script nonce="{{ $nonce ?? '' }}" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
    integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script nonce="{{ $nonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
    integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.bootstrap5.min.css">

<link rel="stylesheet" href="{{ asset('metronic/plugins/global/plugins.bundle.css') }}">
<link rel="stylesheet" href="{{ asset('metronic/css/style.bundle.css') }}">

<script nonce="{{ $nonce ?? '' }}" defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.15.8/dist/cdn.min.js"
    integrity="sha256-532TLFLOYWwqXF3EVTCgIhkRoxqrSKF56GgJMtTTqkc=" crossorigin="anonymous"></script>
<script nonce="{{ $nonce ?? '' }}" defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"
    integrity="sha256-iZhCeCp/0W/MLXp8h3/57BWTlARMh7FYsu8TJ4ZgaTI=" crossorigin="anonymous"></script>
<link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

<link rel="preload" href="{{ asset('assets/fonts/poppins.ttf') }}" as="font" type="font/ttf" crossorigin>
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
