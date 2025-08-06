<!-- TODO: convert to Datatable -->

<div class="grid grid-cols-3 w-full gap-4">
<!-- Waiting -->
     <div class="flex flex-col gap-2">
        <h3 class="text-gray-700 font-semibold">Waiting</h3>
        @if (count($waitingBurials) == 0)
            <p class="text-gray-400 text-sm">No burials waiting for scheduled date</p>
        @endif
        @foreach ($waitingBurials as $waiting)
        <a href="{{ route('admin.burial.request.view', ['uuid' => $waiting->uuid]) }}">
            <div class="flex flex-col bg-white rounded-md shadow-md p-4 gap-1">
                <h4 class="font-semibold">{{ $waiting->deceased_firstname }} {{ $waiting->deceased_lastname }}</h4>
                <p class="text-gray-400 text-sm">{{ $waiting->burial_address }}, {{ $waiting->barangay->name }}</p>
                <p class="font-semibold">{{ Str::limit($waiting->start_of_burial, 10) }}</p>
            </div>
        </a>
        @endforeach
    </div>
    
    <!-- On-Going -->
    <div class="flex flex-col gap-2">
        <h3 class="text-gray-700 font-semibold">On-Going</h3>
        @if (count($onGoingBurials) == 0)
            <p class="text-gray-400 text-sm">No on-going burials</p>
        @endif
        @foreach ($onGoingBurials as $onGoing)
            <a href="{{ route('admin.burial.request.view', ['uuid' => $onGoing->uuid]) }}">
                <div class="flex flex-col bg-white rounded-md shadow-md p-4 gap-1">
                    <h4 class="font-semibold">{{ $onGoing->deceased_firstname }} {{ $onGoing->deceased_lastname }}</h4>
                    <p class="text-gray-400 text-sm">{{ $onGoing->burial_address }}, {{ $onGoing->barangay->name }}</p>
                    <p class="font-semibold">{{ Str::limit($onGoing->start_of_burial, 10) }} - {{ Str::limit($onGoing->end_of_burial, 10) }}</p>
                </div>
            </a>
        @endforeach
    </div>
    
    <!-- Completed -->
    <div class="flex flex-col gap-2">
        <h3 class="text-gray-700 font-semibold">Completed</h3>
        @if (count($completedBurials) == 0)
            <p class="text-gray-400 text-sm">No burials waiting to be recorded as serviced</p>
        @endif
        @foreach ($completedBurials as $completed)
            <a href="{{ route('admin.burial.request.view', ['uuid' => $completed->uuid]) }}">
                <div class="flex flex-col bg-white rounded-md shadow-md p-4 gap-2">
                    <h4 class="font-semibold">{{ $completed->deceased_firstname }} {{ $completed->deceased_lastname }}</h4>
                    <p class="text-gray-400 text-sm">{{ $completed->burial_address }}, {{ $completed->barangay->name }}</p>
                    <p class="font-semibold">{{ Str::limit($completed->end_of_burial, 10) }}</p>
                    <a href="{{ route('admin.burial.request.to.service', ['uuid' => $completed->uuid]) }}" class="border-2 border-gray-800 hover:bg-gray-700 text-black hover:text-white w-fit font-semibold py-2 px-4 rounded transition-all duration-300">
                        Generate Service Form
                    </a>
                </div>
            </a>
        @endforeach
     </div>
    </div>