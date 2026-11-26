<meta charset="UTF-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1.0">
@include('partials.page.title')

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
	integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
	crossorigin="anonymous"
	referrerpolicy="no-referrer"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.20/dist/sweetalert2.all.min.js"
	integrity="sha256-AdsOUa1T5MFNHdura152IyIfRU9R4LYlXbqyGXv2z+g="
	crossorigin="anonymous"></script>

{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.bootstrap5.min.css"> --}}
<link href="{{ asset('metronic/plugins/custom/datatables/datatables.bundle.css') }}"
	rel="stylesheet"
	type="text/css" />

<link rel="stylesheet"
	href="{{ asset('metronic/plugins/global/plugins.bundle.css') }}">
<link rel="stylesheet"
	href="{{ asset('metronic/css/style.bundle.css') }}">

{{-- <script nonce="{{ $nonce ?? '' }}" defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.15.8/dist/cdn.min.js"
    integrity="sha256-532TLFLOYWwqXF3EVTCgIhkRoxqrSKF56GgJMtTTqkc=" crossorigin="anonymous"></script>
<script nonce="{{ $nonce ?? '' }}" defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"
    integrity="sha256-iZhCeCp/0W/MLXp8h3/57BWTlARMh7FYsu8TJ4ZgaTI=" crossorigin="anonymous"></script> --}}
<link rel="icon"
	type="image/x-icon"
	href="{{ asset('images/favicon.ico') }}">

<link rel="preload"
	href="{{ asset('fonts/poppins.ttf') }}"
	as="font"
	type="font/ttf"
	crossorigin>
<link rel="stylesheet"
	href="{{ asset('css/app.css') }}">
@livewireStyles(['nonce' => $nonce ?? ''])

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
	integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
	crossorigin="anonymous"
	referrerpolicy="no-referrer"></script>

{{-- Metronic includes Bootstrap. Loading a second copy registers duplicate modal handlers. --}}
<script nonce="{{ $nonce ?? '' }}"
	src="{{ asset('metronic/plugins/global/plugins.bundle.js') }}"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"
	integrity="sha256-SERKgtTty1vsDxll+qzd4Y2cF9swY9BCq62i9wXJ9Uo="
	crossorigin="anonymous"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"
	integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg=="
	crossorigin="anonymous"
	referrerpolicy="no-referrer"></script>
<script nonce="{{ $nonce ?? '' }}"
	src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/pdfmake.min.js"
	integrity="sha512-axXaF5grZBaYl7qiM6OMHgsgVXdSLxqq0w7F4CQxuFyrcPmn0JfnqsOtYHUun80g6mRRdvJDrTCyL8LQqBOt/Q=="
	crossorigin="anonymous"
	referrerpolicy="no-referrer"></script>

<script nonce="{{ $nonce ?? '' }}"
	src="{{ asset('metronic/js/scripts.bundle.js') }}"></script>
<script nonce="{{ $nonce ?? '' }}"
	src="{{ asset('metronic/plugins/custom/datatables/datatables.bundle.js') }}"></script>


@php
    $scriptVersion = substr(hash('sha256', implode('', array_map(
        static fn (string $path): string => hash_file('sha256', $path),
        glob(public_path('js/*.js')),
    ))), 0, 16);
@endphp
<script type="module" nonce="{{ $nonce ?? '' }}" src="{{ asset('js/app.js') }}?v={{ $scriptVersion }}" data-navigate-track></script>
