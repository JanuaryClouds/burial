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
            <td>{{ $provider->phone }}</td>
            <td class="d-flex align-items-center gap-1">
                <x-table-actions :data="$provider" :type="'provider'" />
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