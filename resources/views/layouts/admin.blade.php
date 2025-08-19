<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true, sidebarCollapsed: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="min-vh-100 row row-gap-0 vw-100 g-0" style="background-color: #fafafa;">
    <!-- sidebar -->
    <nav
        class="col-2 row d-flex flex-column mt-0 g-2 ps-0"
        class="z-index: 1;"
        x-show="sidebarOpen"
    >
        <!-- sidebar content -->
        <div
            class="col row flex-column gap-4 bg-white position-sticky g-0 p-2 top-0 h-100 overflow-y-auto"
        >
            <!-- logo -->
            <div class="col-2 row g-2 mt-0 w-auto">
                <div class="col-4 mt-0">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="w-100 mt-0">
                </div>
                <div class="col row d-flex flex-column justify-content-center">
                    <p class="fw-semibold mb-0">CSWDO</p>
                    <p class="fw-semibold text-black mb-0">Burial Assistance</p>
                </div>
            </div>
            <!-- sidebar links -->
            <div class="col-10 g-2 w-100 d-flex flex-column gap-1" style="">
                <a href="{{ route('admin.dashboard') }}" class="nav-link btn btn-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-house"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.burial.new') }}" class="nav-link btn btn-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-file-circle-plus"></i>
                    New Burial Service
                </a>
                <a href="{{ route('admin.burial.history') }}" class="nav-link btn btn-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Burial History
                </a>
                <a href="{{ route('admin.burial.requests') }}" class="nav-link btn btn-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-list"></i>
                    Burial Requests
                </a>
                <a href="{{ route('admin.burial.providers') }}" class="nav-link btn btn-link d-flex gap-1 align-items-center">
                    <i class="fa-solid fa-building" style="padding-left: 2px; margin-right: 5px;"></i>
                    Burial Service Providers
                </a>
            </div>
        </div>
    </nav>

    <!-- main -->
    <div class="col-10 g-0 vh-100 overflow-y-auto overflow-x-hidden" style="">
        <div class="position-relative px-4" style="background-color: #ff5147; z-index: -1; height: 10em;">
        </div>

        <div class="d-flex dropdown open justify-content-between mx-4" style="z-index: 3; margin-top: -8em;">
            <div>
                <!-- TODO: Search -->
                 <!-- 1. When the user clicks on the search  -->
                <!-- Modal trigger button -->
                <button
                    type="button"
                    class="btn btn-primary btn-outline-light"
                    data-bs-toggle="modal"
                    data-bs-target="#searchModal"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                
                <!-- Modal Body -->
                <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
                <div
                    class="modal fade"
                    id="searchModal"
                    tabindex="-1"
                    data-bs-backdrop="static"
                    data-bs-keyboard="false"
                    
                    role="dialog"
                    aria-labelledby="modalTitleId"
                    aria-hidden="true"
                >
                    <div
                        class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm position-relative"
                        role="document"
                    >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTitleId">
                                    Modal title
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>
                            </div>
                            <div class="modal-body">Body</div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button type="button" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Optional: Place to the bottom of scripts -->
                <script>
                    const myModal = new bootstrap.Modal(
                        document.getElementById("modalId"),
                        options,
                    );
                </script>
                

                <input type="text"
                    id="globalSearch"
                    class="form-control"
                    placeholder="Search..."
                    autocomplete="off"
                >
                <div id="searchResults"
                    class="list-group position-absolute w-100 shadow-sm"
                    style="z-index: 1050; display: none; max-height: 300px; overflow-y: auto;"
                ></div>
            </div>
            
            <button
                class="btn btn-light dropdown-toggle d-flex gap-2 align-items-center"
                type="button"
                id="triggerId"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <i class="fa-solid fa-user"></i>{{ Auth::user()->first_name }} {{ Auth::user()->middle_name }} {{ Auth::user()->last_name }}
            </button>
            <div class="dropdown-menu" aria-labelledby="triggerId">
                <a href="#" class="btn w-100">
                    <span x-show="!sidebarCollapsed" x-cloak class="fw-medium"><i
                            class="fa-solid fa-user me-2"></i> Profile</span>
                    <span x-show="sidebarCollapsed" x-cloak class="fw-medium"><i
                            class="fa-solid fa-user"></i></span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit"
                        class="btn w-100">
                        <span x-show="!sidebarCollapsed" x-cloak class="fw-medium">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="fw-medium">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <main class="row mx-4 p-0 pb-5 g-0 rounded-2" style="z-index: 1; margin-top: 2em;">

            @yield('content')

            <div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3 d-flex justify-content-end" style="z-index: 2;">
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
                    <strong class="me-auto">Failed</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ session('failed') }}
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
        </main>  
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("globalSearch");
            const resultsBox = document.getElementById("searchResults");
            const searchUrl = @json(route('admin.admin.search'));
            let timeout = null;

            searchInput.addEventListener("keyup", function () {
                clearTimeout(timeout);
                const query = this.value.trim();

                if (!query) {
                    resultsBox.style.display = "none";
                    return;
                }

                // debounce: wait 300ms after typing stops
                timeout = setTimeout(() => {
                    fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                        headers: { "Accept": "application/json" }
                    })
                        .then(res => res.json())
                        .then(data => {
                            resultsBox.innerHTML = "";
                            if (data.length > 0) {
                                data.forEach(item => {
                                    const link = document.createElement("a");
                                    link.href = item.url;
                                    link.classList.add("list-group-item", "list-group-item-action");
                                    link.innerHTML = `
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><strong>${item.name}</strong></span>
                                            <small class="text-muted">${item.type}</small>
                                        </div>`;
                                    resultsBox.appendChild(link);
                                });
                                resultsBox.style.display = "block";
                            } else {
                                resultsBox.style.display = "none";
                            }
                        });
                }, 300);
            });

            // hide results when clicking outside
            document.addEventListener("click", function (e) {
                if (!resultsBox.contains(e.target) && e.target !== searchInput) {
                    resultsBox.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>