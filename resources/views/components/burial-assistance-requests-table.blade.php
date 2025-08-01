<div class="flex flex-col gap-2" x-data="{ showRequest: 'pending' }">
    <span class="flex justify-content-start align-items-center *:py-2 *:px-3 *:hover:cursor-pointer gap-2 *:rounded-md">
        <button @click="showRequest = 'pending'"
            x-bind:class="showRequest === 'pending' ? 'bg-red-400 text-white' : 'bg-gray-200'"
            x-transition
        >
            Pending
        </button>
        <button @click="showRequest = 'approved'"
            x-bind:class="showRequest === 'approved' ? 'bg-red-400 text-white' : 'bg-gray-200'"
            x-transition
            x-transition
        >
            Approved
        </button>
        <button @click="showRequest = 'rejected'"
            x-bind:class="showRequest === 'rejected' ? 'bg-red-400 text-white' : 'bg-gray-200'"
            x-transition
        >
            Rejected
        </button>
    </span>

    <table>
        <thead class="border-2 border-gray-200 gap-4">
            <tr class="*:text-gray-400 *:text-left *:py-2 *:px-4 *:font-semibold border-b-2 border-b-gray-200 border-gray-100 border-2 rounded-t-lg">
                <td>Name of Deceased</td>
                <td>Representative / Contact Details</td>
                <td>Address</td>
                <td>Duration of Burial</td>
                <td></td>
            </tr>
        </thead>
        <tbody x-show="showRequest === 'pending'" x-cloak x-transition">
            @foreach ($pendingRequests as $request)
                <tr class="border-2 border-gray-100 *:py-2 *:px-4" x-cloak x-transition>
                    <td>{{ $request->deceased_firstname }} {{ $request->deceased_lastname }}</td>
                    <td>{{ $request->representative }} / {{ $request->representative_contact }}</td>
                    <td>{{ $request->burial_address }}, {{ $request->barangay->name }}</td>
                    <td>{{ Str::limit($request->start_of_burial, 10) }}, {{ Str::limit($request->end_of_burial, 10) }}</td>
                    <td>
                        <a href="{{ route('admin.burial.request.view', ['uuid' => $request->uuid]) }}">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tbody x-show="showRequest === 'approved'" x-cloak x-transition">
            @foreach ($approvedRequests as $request)
                <tr class="border-2 border-gray-100 *:py-2 *:px-4" x-cloak x-transition>
                    <td>{{ $request->deceased_firstname }} {{ $request->deceased_lastname }}</td>
                    <td>{{ $request->representative }} / {{ $request->representative_contact }}</td>
                    <td>{{ $request->burial_address }}, {{ $request->barangay->name }}</td>
                    <td>{{ Str::limit($request->start_of_burial, 10) }}, {{ Str::limit($request->end_of_burial, 10) }}</td>
                    <td>
                        <a href="{{ route('admin.burial.request.view', ['uuid' => $request->uuid]) }}">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tbody x-show="showRequest === 'rejected'" x-cloak x-transition">
            @foreach ($rejectedRequests as $request)
                <tr class="border-2 border-gray-100 *:py-2 *:px-4" x-cloak x-transition>
                    <td>{{ $request->deceased_firstname }} {{ $request->deceased_lastname }}</td>
                    <td>{{ $request->representative }} / {{ $request->representative_contact }}</td>
                    <td>{{ $request->burial_address }}, {{ $request->barangay->name }}</td>
                    <td>{{ Str::limit($request->start_of_burial, 10) }}, {{ Str::limit($request->end_of_burial, 10) }}</td>
                    <td>
                        <a href="{{ route('admin.burial.request.view', ['uuid' => $request->uuid]) }}">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>