<table id="requestsTable" class="table table-borderless dt-body-center">
    <thead>
        <tr class="bg-primary">
            <th class="text-white">Name of Deceased</th>
            <th class="text-white">Representative / Contact Details</th>
            <th class="text-white">Address</th>
            <th class="text-white">Duration of Burial</th>
            <th class="text-white">Status</th>
            <th class="text-white">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($allRequests as $request)
            <tr class="border-1">
                <td>{{ $request->deceased_firstname }} {{ $request->deceased_lastname }}</td>
                <td>{{ $request->representative }} / {{ $request->representative_contact }}</td>
                <td>{{ $request->burial_address }}, {{ $request->barangay->name }}</td>
                <td>{{ Str::limit($request->start_of_burial, 10) }}, {{ Str::limit($request->end_of_burial, 10) }}</td>
                <td>{{ Str::ucfirst($request->status) }}</td>
                <td class="d-flex justify-content-start align-items-center gap-1">
                    <x-table-actions :data="$request" :type="'request'" />
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM fully loaded and parsed');
        const table = document.querySelector('#requestsTable');
        if (table) {
            $(table).DataTable({
                responsive: true,
                pageLength: 10,
            });
        }
    });
</script>