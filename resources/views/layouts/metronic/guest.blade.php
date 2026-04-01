<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="system" data-bs-theme-mode="system"
    class="overflow-x-hidden">

<head>
    @include('partials.document-head')
</head>

<body id="kt_body" class="app-blank"
    style="background: url('{{ asset('images/white_bg_city.png') }}') no-repeat center center / cover; overflow-x: hidden; background-attachment: fixed; max-width: 100vw;">
    @include('partials.theme-script')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <x-loader />
        @includeWhen(!Route::is('login'), 'client.partials.header')
        @yield('content')
        @include('guest.partials.footer')
    </div>
    @include('components.alert')
    @include('partials.document-scripts')
</body>

</html>
