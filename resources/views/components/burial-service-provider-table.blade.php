<table id="providersTable" class="table table-borderless">
    <thead>
        <tr class="bg-primary">
            <th class="text-white">Name of Provider</th>
            <th class="text-white">Address</th>
            <th class="text-white">Contact</th>
            <th class="text-white">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($providers as $provider)
        <tr class="">
            <td>{{ $provider->name }}</td>
            <td>{{ $provider->address }}, {{ $provider->barangay->name }}</td>
            <td>{{ $provider->contact_details }}</td>
            <td class="flex items-center gap-2">
                <!-- TODO: Add a modal to show the image -->
                <a href="{{ route('admin.burial.provider.view', ['id' => $provider->id]) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pen"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM fully loaded and parsed');
        const table = document.querySelector('#providersTable');
        if (table) {
            $(table).DataTable({
                responsive: true,
                pageLength: 10,
            });
        }
    });
</script>