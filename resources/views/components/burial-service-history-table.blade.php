<table id="serviceHistoryTable" class="table table-borderless">
    <thead>
        <tr class="bg-primary">
            <th class="text-white">Name of Deceased</th>
            <th class="text-white">Address of Burial</th>
            <th class="text-white">Date of Burial</th>
            <th class="text-white">Funeral Service</th>
            <th class="text-white">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($burialServices as $service)
        <tr class="*:py-2 *:px-4 border-2 border-gray-100">
            <td>{{ $service->deceased_firstname }} {{ $service->deceased_lastname }}</td>
            <td>{{ $service->burial_address }}, {{ $service->barangay->name }}</td>
            <td>{{ Str::limit($service->start_of_burial, 10) }} - {{ Str::limit($service->end_of_burial,10) }}</td>
            <td>{{ Str::limit($service->provider->name, 25) }}</td>
            <td class="flex items-center gap-2">

                <!-- TODO: Add a modal to show the image -->
                <button class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <!-- TODO: Button to Redirect to a dedicated page for burial service details -->
                <button class="btn btn-secondary">
                    <i class="fa-solid fa-up-right-from-square"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
    
</table>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM fully loaded and parsed');
        const table = document.querySelector('#serviceHistoryTable');
        if (table) {
            $(table).DataTable({
                responsive: true,
                pageLength: 10,
            });
        }
    });
</script>