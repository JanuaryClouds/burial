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

                <a href="{{ route(Auth::user()->getRoleNames()->first() . '.client.index') }}"
                    class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                    <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i class="fa-solid fa-person me-2"></i>
                        Clients</span>
                    <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                            class="fa-solid fa-person"></i></span>
                </a>

                <div x-data="{ cmsOpen: false }" class="block">
                    <button @click="cmsOpen = !cmsOpen"
                        class="w-full text-left py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition focus:outline-none">
                        <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                                class="fa-solid fa-toolbox me-2"></i>
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
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.sex.index') }}"
                            class="font-medium block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4">
                                <i class="fa-solid fa-venus-mars me-2"></i> Gender
                            </span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium">
                                <i class="fa-solid fa-venus-mars"></i>
                            </span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.religion.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-cross me-2"></i> Religion</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-cross"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.nationality.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-earth-americas me-2"></i> Nationality</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-earth-americas"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.civil.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-heart me-2"></i> Civil Status</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-heart"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.relationship.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-people-roof me-2"></i> Relationship</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-people-roof"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.education.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-book me-2"></i> Education</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-book"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.assistance.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-handshake-angle me-2"></i> Assistance</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-handshake-angle"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.district.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-map-pin me-2"></i> District</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-map-pin"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.barangay.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-building me-2"></i> Barangay</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-building"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.moa.index') }}"
                            class="block py-2 px-3 rounded hover:bg-[#F4C027] hover:text-black text-white transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium pl-4"><i
                                    class="fa-solid fa-money-bill me-2"></i> Mode of Assitance</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-money-bill"></i></span>
                        </a>
                    </div>
                </div>
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
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.role.index') }}"
                            class="block py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user-tie me-2"></i> Role</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user-tie"></i></span>
                        </a>
                        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.permission.index') }}"
                            class="block py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-lock me-2"></i> Permission</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-lock"></i></span>
                        </a>
                        <a href="#" class="block py-2 px-3 hover:bg-[#F4C027] hover:text-black text-black transition">
                            <span x-show="!sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user me-2"></i> Profile</span>
                            <span x-show="sidebarCollapsed" x-cloak class="font-medium"><i
                                    class="fa-solid fa-user"></i></span>
                        </a>
                        <form action="#" method="POST" class="block">
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
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>

</html>