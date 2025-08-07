<table id="requestsTable" class="table table-borderless">
    <thead>
        <tr class="bg-primary">
            <th class="text-white">Name of Deceased</th>
            <th class="text-white">Representative / Contact Details</th>
            <th class="text-white">Address</th>
            <th class="text-white">Duration of Burial</th>
            <th class="text-white">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($allRequests as $request)
            <tr>
                <td>{{ $request->deceased_firstname }} {{ $request->deceased_lastname }}</td>
                <td>{{ $request->representative }} / {{ $request->representative_contact }}</td>
                <td>{{ $request->burial_address }}, {{ $request->barangay->name }}</td>
                <td>{{ Str::limit($request->start_of_burial, 10) }}, {{ Str::limit($request->end_of_burial, 10) }}</td>
                <td>
                    <a href="{{ route('admin.burial.request.view', ['uuid' => $request->uuid]) }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
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