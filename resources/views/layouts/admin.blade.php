<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true, sidebarCollapsed: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="min-vh-100 row row-gap-0 vw-100 g-0">
    <!-- sidebar -->
    <nav
        class="col-2 row d-flex flex-column g-2 px-4"
        x-show="sidebarOpen"
    >
        <!-- sidebar content -->
        <div
            class="col row flex-column gap-4 bg-white position-sticky g-0 top-0 h-100 overflow-y-auto"
        >
            <!-- logo -->
            <div class="col-2 row g-2 mt-0 w-auto">
                <div class="col-4 mt-0">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="w-100 mt-0">
                </div>
                <div class="col row d-flex flex-column">
                    <p class="fw-semibold mb-0">CSWDO</p>
                    <p class="fw-semibold text-black mb-0">Burial Assistance</p>
                </div>
            </div>
            <!-- sidebar links -->
            <div class="col-10 g-2 text-black w-100 d-flex flex-column gap-4">
                <a href="{{ route('admin.burial.new') }}" class="nav-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-file-circle-plus"></i>
                    New Burial Service
                </a>
                <a href="{{ route('admin.burial.history') }}" class="nav-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Burial History
                </a>
                <a href="{{ route('admin.burial.requests') }}" class="nav-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-list"></i>
                    Burial Requests
                </a>
                <a href="{{ route('admin.burial.providers') }}" class="nav-link d-flex gap-2 align-items-center">
                    <i class="fa-solid fa-building" style="margin-left: 1.5px; margin-right: 1.5px;"></i>
                    Burial Service Providers
                </a>
            </div>

        </div>
    </nav>

    <!-- main -->
    <div class="col-10 g-0 vh-100 overflow-y-auto position-relative">
        <div class="position-absolute top-0 start-0 w-100 h-25" style="background-color: #ff5147; z-index: 0;"></div>
        <div class="position-relative container d-flex justify-content-between align-items-center mx-2" style="z-index: 2; height: 5em;">
            
            <div class="dropdown open" style="z-index: 2;">
                <button
                    class="btn btn-primary dropdown-toggle"
                    type="button"
                    id="triggerId"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    {{ Auth::user()->first_name }} {{ Auth::user()->middle_name }} {{ Auth::user()->last_name }}
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
            
        </div>
        <main class="mx-4 position-relative" style="z-index: 1;">
            @yield('content')
        </main>
    </div>
    

<!--
    <div class="fixed inset-0 z-20 bg-black/50 bg-opacity-50 transition-opacity lg:hidden"
        :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false">
    </div>

    <div class="flex min-h-screen overflow-hidden">
        <aside
            class="fixed inset-y-0 left-0 z-30 transform transition-transform duration-200 ease-in-out flex flex-col bg-[#ff5147] lg:bg-[#ff5147]"
            :class="{'-translate-x-full': !sidebarOpen, 'w-18': sidebarCollapsed, 'w-64': !sidebarCollapsed}">

            <div
                class="block m-3 flex items-center justify-center h-16 rounded hover:bg-[#F4C027] hover:text-black text-white">
                <span x-show="!sidebarCollapsed" x-cloak class="flex items-center">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="w-10 mr-2">
                    CSWDO - Burial
                </span>
                <span x-show="sidebarCollapsed" x-cloak class="font-bold text-xl">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="w-10">
                </span>
            </div>

            <nav class="p-4 space-y-2">
                <a href="{{ route(Auth::user()->getRoleNames()->first() . '.dashboard') }}"
                    class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                            class="fa-solid fa-table-columns me-2"></i> Dashboard</span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                            class="fa-solid fa-table-columns"></i></span>
                </a>
                <a href="{{ route('admin.burial.new') }}" class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-file-circle-plus me-1.5"></i>
                        New Burial Service
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-file-circle-plus me-1.5"></i>
                    </span>
                </a>
                <a href="{{ route('admin.burial.history') }}" class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>
                        Burial History
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    </span>
                </a>
                <a href="{{ route('admin.burial.requests') }}" class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-list me-2"></i>
                        Burial Requests
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-list me-2"></i>
                    </span>
                </a>
                <a href="{{ route('admin.burial.providers') }}" class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-building ml-0.5 me-2.5"></i>
                        Burial Service Providers
                    </span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium">
                        <i class="fa-solid fa-building me-2 ms-0.5"></i>
                    </span>
                </a>

            </nav>
            <div class="mt-auto p-4 border-gray-200" x-data="{ userDropdownOpen: false }">
                <div class="relative">
                    <button @click="userDropdownOpen = !userDropdownOpen"
                        class="w-full flex items-center justify-between py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition focus:outline-none">
                        <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                            {{ Auth::user()->first_name }} {{ Auth::user()->middle_name }} {{ Auth::user()->last_name }}
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak
                            class="font-medium">{{ substr(Auth::user()->first_name, 0,1) }}{{ substr(Auth::user()->last_name, 0,1) }}</span>
                        <svg x-show="!sidebarCollapsed" x-cloak class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 15l-7-7-7 7" />
                        </svg>
                    </button>
                    <div x-show="userDropdownOpen" x-cloak x-transition
                        class="absolute left-0 bottom-full mb-2 w-full bg-white shadow-md rounded z-10">
                        <a href="#" class="block py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user me-2"></i> Profile</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user"></i></span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="block">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition focus:outline-none">
                                <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                </span>
                                <span x-show="sidebarCollapsed" x-cloak class="font-medium">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col transition-all duration-200"
            :class="sidebarCollapsed ? 'lg:ml-18' : 'lg:ml-64'">
            <div class="bg-white flex-1 shadow-lg rounded-lg p-6 m-3">
                <nav class="mb-3">
                    @hasSection('breadcrumb')
                    @yield('breadcrumb')
                    @endif
                </nav>
                <main class="p-5 overflow-y-auto">
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</body>
</html>