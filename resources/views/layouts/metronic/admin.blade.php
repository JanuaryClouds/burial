<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="system" data-bs-theme-mode="system">

<head>
    @include('partials.document-head')
</head>

<body id="kt_body"
    class="header-fixed header-mobile-fixed aside-enabled aside-fixed aside-secondary-disabled overflow-x-hidden">
    @include('partials.theme-script')
    <div class="d-flex flex-column flex-root min-vh-100">
        <x-loader />
        <div class="page d-flex flex-row flex-column-fluid">
            @include('admin.partial.sidebar')
            <div class="wrapper d-flex flex-column flex-row-fluid">
                @include('admin.partial.header')
                <div class="content d-flex flex-column flex-column-fluid">
                    <div class="container-xxl">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.alert')
    @include('partials.document-scripts')
</body>

</html>
