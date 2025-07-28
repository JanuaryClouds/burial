<table>
    <tr class="*:text-gray-400 *:text-left *:py-2 *:px-4 *:font-semibold border-b-2 border-b-gray-200 border-gray-100 border-2 rounded-t-lg">
        <th>Name of Deceased</th>
        <th>Address of Burial</th>
        <th>Date of Burial</th>
        <th>Funeral Service</th>
        <th>Funds Collected</th>
        <th>Action</th>
    </tr>
    @foreach ($burialServices as $service)
    <tr class="*:py-2 *:px-4 border-2 border-gray-100">
        <td>{{ $service->deceased_firstname }} {{ $service->deceased_lastname }}</td>
        <td>{{ $service->burial_address }}</td>
        <td>{{ Str::limit($service->start_of_burial, 10) }} - {{ Str::limit($service->end_of_burial,10) }}</td>
        <td>{{ $service->provider->name }}</td>
        <td>{{ $service->collected_funds }}</td>
        <td class="flex items-center gap-2">

            <!-- TODO: Add a modal to show the image -->
            <button class="bg-yellow-300 rounded-lg p-2">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <!-- TODO: Button to Redirect to a dedicated page for burial service details -->
            <button class="bg-gray-400 rounded-lg p-2">
                <i class="fa-solid fa-up-right-from-square"></i>
            </button>
        </td>
    </tr>

    @endforeach
</table>