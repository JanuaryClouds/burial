<ol class="flex items-center space-x-2 text-sm p-1">
    <li>
        <button class="mr-2 text-gray-600 focus:outline-none"
            @click="(window.innerWidth >= 1024) ? sidebarCollapsed = !sidebarCollapsed : sidebarOpen = !sidebarOpen">
            <i class="fa-solid fa-columns hover:bg-[#F4C027] hover:text-black hover:text-white p-3 rounded"></i>
        </button>
    </li>
    <li>
        <svg class="h-4 w-0.5 text-gray-300" viewBox="0 0 2 20" fill="none">
            <line x1="1" y1="0" x2="1" y2="24" stroke="currentColor" stroke-width="2" />
        </svg>
    </li>
    <li>
        <a href="{{ route(Auth::user()->getRoleNames()->first() . '.dashboard') }}"
            class="text-blue-500 hover:bg-[#F4C027] hover:text-black hover:text-white p-3 rounded font-medium">Dashboard</a>
    </li>
    @foreach ($items as $index => $item)
    <li>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </li>
    <li>
        @if (!empty($item['url']) && $index !== count($items) - 1)
        <a href="{{ $item['url'] }}"
            class="text-blue-500 hover:bg-[#F4C027] hover:text-black hover:text-white p-3 rounded font-medium">{{ $item['label'] }}</a>
        @else
        <span class="text-gray-700 font-medium">{{ $item['label'] }}</span>
        @endif
    </li>
    @endforeach
</ol>