<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gmaps.js/0.4.25/gmaps.min.js" integrity="sha512-gauu0VKZq9ry8hOZHgNpcB2ogbSDojs+XLDItpOLY0IyA+KigeuT/suwSdPgfU/TGYLAn4Nan4OeaCa/UPr70Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

        @vite('resources/css/app.css')
        @vite('resources/js/app.js')
    </head>

    <body class="min-vh-100 d-flex flex-column align-items-center"
        style="background: url('{{ asset('images/cover.webp') }}') no-repeat center center / cover;">
        @yield('content')

        <div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3 d-flex justify-content-end"
            style="z-index: 2;">
            @if (session()->has('success'))
                <div class="toast bg-success text-black" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success gap-2 align-items-center">
                        <i class="fa-solid fa-circle-check"></i>
                        <strong class="me-auto">Success</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                </div>
            @elseif (session()->has('error'))
                <div class="toast bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-danger gap-2 align-items-center">
                        <i class="fa-solid fa-xmark"></i>
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body text-black">
                        {{ session('error') }}
                    </div>
                </div>
            @elseif (session()->has('info'))
                <div class="toast bg-info text-black" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-info gap-2 align-items-center">
                        <i class="fa-solid fa-xmark"></i>
                        <strong class="me-auto">Failed</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ session('info') }}
                    </div>
                </div>
            @endif
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toastEl = document.querySelector('.toast');
                if (toastEl) {
                    new bootstrap.Toast(toastEl).show();
                }
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
            crossorigin="anonymous"></script>
    </body>

</html>