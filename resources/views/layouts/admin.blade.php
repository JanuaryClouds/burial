<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true, sidebarCollapsed: $persist(false) }">
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
<body 
    class="min-vh-100 g-0"
    x-bind:class="{ 'row row-gap-0 vw-100': !sidebarCollapsed, 'min-vw-100': sidebarCollapsed }"
    style="background-color: #fafafa;"
>
    <!-- sidebar -->
    <nav
        class="mt-0 g-2 ps-0"
        x-bind:class="{ 'col-2 row d-flex flex-column': !sidebarCollapsed, 'position-fixed fixed-top vh-100': sidebarCollapsed }"
        x-bind:style="{ width: sidebarCollapsed ? '3.25em' : '' }"
        x-transition
        x-cloak
    >
        <!-- sidebar content -->
        <div
            class="col row flex-column gap-4 bg-white position-sticky g-0 p-2 top-0 h-100 overflow-y-auto"
            x-bind:class="{ 'mt-0' : !sidebarCollapsed, 'mt-2': sidebarCollapsed }"
        >
            <!-- logo -->
            <div class="col-2 row g-2 w-auto"
                x-bind:class="{ 'px-2 mt-0': !sidebarCollapsed, 'px-1 mt-1': sidebarCollapsed }"
            >
                <div class="col-4 mt-0">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo"
                        class="mt-0"
                        x-bind:class="{ 'w-100': !sidebarCollapsed }"
                        x-bind:style="{ 'width': sidebarCollapsed ? '25px' : 'auto' }"
                    >
                </div>
                <div class="col row flex-column justify-content-center" 
                    x-bind:class="{ 'd-none': sidebarCollapsed, 'd-flex': !sidebarCollapsed }"
                    x-transition
                    x-cloak
                >
                    <p class="fw-semibold mb-0" x-show="!sidebarCollapsed" x-transition x-cloak>CSWDO</p>
                    <p class="fw-semibold text-black mb-0" x-show="!sidebarCollapsed" x-transition x-cloak>Burial Assistance</p>
                </div>
            </div>
            <!-- sidebar links -->
            <div class="col-10 g-2 w-100 d-flex flex-column" x-bind:class="{ 'gap-2': !sidebarCollapsed, 'gap-4': sidebarCollapsed }">
                <a href="{{ route('admin.dashboard') }}" 
                    class="nav-link btn btn-link d-flex gap-2 align-items-center"
                    x-bind:disabled="sidebarCollapsed"
                    x-bind:class="{ 'btn btn-link': !sidebarCollapsed, 'px-1': sidebarCollapsed }"
                    title="Dashboard"
                    >
                    <i class="fa-solid fa-house"></i>
                    <p x-show="!sidebarCollapsed" class="mb-0" x-cloak>Dashboard</p>
                </a>
                <a href="{{ route('admin.burial.new') }}" 
                    class="nav-link btn btn-link d-flex gap-2 align-items-center"
                    x-bind:disabled="sidebarCollapsed"
                    x-bind:class="{ 'btn btn-link': !sidebarCollapsed, 'px-1': sidebarCollapsed }"
                    title="New Burial Service"
                >
                    <i class="fa-solid fa-file-circle-plus"></i>
                    <p x-show="!sidebarCollapsed" class="mb-0" x-cloak>New Burial Service</p>
                </a>
                <a href="{{ route('admin.burial.history') }}" 
                    class="nav-link btn btn-link d-flex gap-2 align-items-center"
                    x-bind:disabled="sidebarCollapsed"
                    x-bind:class="{ 'btn btn-link': !sidebarCollapsed, 'px-1': sidebarCollapsed }"
                    title="Burial History"
                >
                    <i class="fa-solid fa-clock-rotate-left" style="width: 14px;"></i>
                    <p x-show="!sidebarCollapsed" class="mb-0" x-cloak>Burial History</p>
                </a>
                <a href="{{ route('admin.burial.requests') }}" 
                    class="nav-link btn btn-link d-flex gap-2 align-items-center"
                    x-bind:disabled="sidebarCollapsed"
                    x-bind:class="{ 'btn btn-link': !sidebarCollapsed, 'px-1': sidebarCollapsed }"
                    title="Burial Requests"
                >
                    <i class="fa-solid fa-list" style="width: 14px;"></i>
                    <p x-show="!sidebarCollapsed" class="mb-0" x-cloak>Burial Requests</p>
                </a>
                <a href="{{ route('admin.burial.providers') }}" 
                    class="nav-link btn btn-link d-flex gap-1 align-items-center"
                    x-bind:disabled="sidebarCollapsed"
                    x-bind:class="{ 'btn btn-link': !sidebarCollapsed, 'px-1': sidebarCollapsed }"
                    title="Burial Service Providers"
                >
                    <i class="fa-solid fa-building" style="padding-left: 2px; margin-right: 5px;"></i>
                    <p x-show="!sidebarCollapsed" class="mb-0" x-cloak>Burial Service Providers</p>
                </a>
            </div>
        </div>
    </nav>

    <!-- main -->
    <div 
        class="g-0 vh-100 overflow-y-auto overflow-x-hidden" style=""
        x-bind:class="{ 'vw-100 ps-5 -ms-5': sidebarCollapsed, 'col-10': !sidebarCollapsed}"
        x-transition
        x-cloak
    >
        <div class="position-relative px-4" style="background-color: #ff5147; z-index: -1; height: 10em;">
        </div>

        <div class="d-flex dropdown open justify-content-between mx-4" style="z-index: 3; margin-top: -8em;">
            <div class="d-flex gap-2">
                <button x-on:click="sidebarCollapsed = !sidebarCollapsed" class="btn btn-light">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-toggle="modal"
                    data-bs-target="#searchModal"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
            </div>
            
            <button
                class="btn dropdown-toggle text-white d-flex gap-2 align-items-center"
                onmouseover="this.style.backgroundColor='#00000000';"
                type="button"
                id="accountOptions"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <i class="fa-solid fa-user"></i>{{ Auth::user()->first_name }} {{ Auth::user()->middle_name }} {{ Auth::user()->last_name }}
            </button>
            <div class="dropdown-menu" aria-labelledby="accountOptions">
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
                        <span class="fw-medium">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
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

    <div
        class="modal fade"
        id="searchModal"
        tabindex="-1"
        
        role="dialog"
        aria-labelledby="modalTitleId"
        aria-hidden="true"
    >
        <div
            class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
            role="document"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Search System
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <input type="text"
                        id="globalSearch"
                        class="form-control"
                        placeholder="Search..."
                        autocomplete="off"
                        autofocus="on"
                    >
                    <div id="searchResults"
                        class="list-group w-100 shadow-sm"
                        style="z-index: 1050; display: none; max-height: 300px; overflow-y: auto;"
                    ></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("globalSearch");

            const searchModal = document.getElementById("searchModal");
            searchModal.addEventListener("show.bs.modal", function () {
                setTimeout(() => searchInput.focus({ preventScroll: true }), 500);
            })

            const resultsBox = document.getElementById("searchResults");
            const searchUrl = @json(route('admin.search'));
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
                                    if (item.type === 'Requests') {
                                        link.innerHTML += `
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><strong>${item.deceased_firstname} ${item.deceased_lastname}</strong> - ${item.burial_address}, ${item.barangay} - ${item.start_of_burial.substring(0, 10)} to ${item.end_of_burial.substring(0, 10)}</span>
                                                <small class="text-muted d-flex align-items-center gap-2"><span class="badge badge-dark badge-pill">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span> ${item.type}</small>
                                            </div>`;
                                    } else if (item.type === 'Services') {
                                        link.innerHTML += `
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><strong>${item.deceased_firstname} ${item.deceased_lastname}</strong> ${item.provider} - ${item.burial_address}, ${item.barangay}</span>
                                                <small class="text-muted">${item.type}</small>
                                            </div>`;
                                    } else if (item.type === 'Providers') {
                                        link.innerHTML += `
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><strong>${item.name}</strong> - ${item.contact} - ${item.address}, ${item.barangay}</span>
                                                <small class="text-muted">${item.type}</small>
                                            </div>`;
                                    }
                                    resultsBox.appendChild(link);
                                });
                                resultsBox.style.display = "block";
                            } else if (data.length === 0) {
                                const noResults = document.createElement("p");
                                noResults.classList.add("list-group-item");
                                noResults.textContent = "No results found.";
                                resultsBox.appendChild(noResults);
                                resultsBox.style.display = "block";
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