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
        <tr class="border-1">
            <td>{{ $service->deceased_firstname }} {{ $service->deceased_lastname }}</td>
            <td>{{ $service->burial_address }}, {{ $service->barangay->name }}</td>
            <td>{{ Str::limit($service->start_of_burial, 10) }} - {{ Str::limit($service->end_of_burial,10) }}</td>
            <td>{{ Str::limit($service->provider->name, 25) }}</td>
            <td class="d-flex justify-content-start align-items-center gap-1">

                <x-table-actions :data="$service" :type="'service'" />
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