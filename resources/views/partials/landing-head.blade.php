<head>
    <meta charset="UTF-8">
    <title>{{ $page_title ?? config('app.name') }}</title>
    <link rel="preload" as="image" href="{{ asset('images/sunray.jpg') }}">
    <link rel="preload" as="image" href="{{ asset('images/footer-trim.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>
    @include('partials.landing-css')
</head>
