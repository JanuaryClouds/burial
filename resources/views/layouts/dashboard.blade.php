<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true, sidebarCollapsed: false }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script>
    <title>Burial assistance</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-[#ff5147] text-gray-800">
    <div class="fixed inset-0 z-20 bg-black/50 bg-opacity-50 transition-opacity lg:hidden"
        :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false">
    </div>

    <div class="flex h-screen overflow-hidden">
        <aside
            class="fixed inset-y-0 left-0 z-30 transform transition-transform duration-200 ease-in-out flex flex-col bg-[#ff5147] lg:bg-[#ff5147]"
            :class="{'-translate-x-full': !sidebarOpen, 'w-18': sidebarCollapsed, 'w-64': !sidebarCollapsed}">

            <div
                class="block m-3 flex items-center justify-center h-16 rounded hover:bg-[#F4C027] hover:text-black text-white">
                <span x-show="!sidebarCollapsed" x-cloak class="flex items-center">
                    <img src="" alt="" class="w-10 mr-2">
                    (Office name here)
                </span>
                <span x-show="sidebarCollapsed" x-cloak class="font-bold text-xl">
                    <img src="" alt="" class="w-10">
                </span>
            </div>

            <nav class="p-4 space-y-2">
                <a href="" class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                            class="fa-solid fa-table-columns"></i> Dashboard</span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                            class="fa-solid fa-table-columns"></i></span>
                </a>

                <a href="#" class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                            class="fa-solid fa-graduation-cap"></i> Students</span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                            class="fa-solid fa-graduation-cap"></i></span>
                </a>

                <div x-data="{ cmsOpen: false }" class="block">
                    <button @click="cmsOpen = !cmsOpen"
                        class="w-full text-left py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition focus:outline-none">
                        <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i class="fa-solid fa-toolbox"></i>
                            Cms</span>
                        <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                class="fa-solid fa-toolbox"></i></span>
                        <svg x-show="!sidebarCollapsed" x-cloak
                            class="w-4 h-4 inline ml-2 transform transition-transform duration-200"
                            :class="{'rotate-180': cmsOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="cmsOpen" x-cloak class="mt-2 space-y-2">
                        <a href="#"
                            class="font-medium block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4">
                                <i class="fa-solid fa-school"></i> School
                            </span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium">
                                <i class="fa-solid fa-school"></i>
                            </span>
                        </a>
                        <a href="#"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-building"></i> Barangay</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-building ml-1"></i></span>
                        </a>
                        <a href="#"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-map-pin"></i> District</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-map-pin ml-1"></i></span>
                        </a>
                    </div>
                </div>
            </nav>
            <div class="mt-auto p-4 border-gray-200" x-data="{ userDropdownOpen: false }">
                <div class="relative">
                    <button @click="userDropdownOpen = !userDropdownOpen"
                        class="w-full flex items-center justify-between py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition focus:outline-none">
                        <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                            (name here)
                        </span>
                        <span x-show="sidebarCollapsed" x-cloak class="font-medium">(NH)</span>
                        <svg x-show="!sidebarCollapsed" x-cloak class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 15l-7-7-7 7" />
                        </svg>
                    </button>
                    <div x-show="userDropdownOpen" x-cloak x-transition
                        class="absolute left-0 bottom-full mb-2 w-full bg-white shadow-md rounded z-10">
                        <a href="#" class="block py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-gear"></i> Admin Menu</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-gear"></i></span>
                        </a>
                        <a href="#" class="block py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user"></i> Profile</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user"></i></span>
                        </a>
                        <form action="#" method="POST" class="block">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition focus:outline-none">
                                <span x-show="!sidebarCollapsed" x-cloak class="font-medium">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout
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
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>

</html>