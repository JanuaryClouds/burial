<table>
    <tr class="*:text-gray-400 *:text-left *:py-2 *:px-4 *:font-semibold border-b-2 border-b-gray-200 border-gray-100 border-2 rounded-t-lg">
        <th>Name of Provider</th>
        <th>Address</th>
        <th>Contact</th>
        <th>Actions</th>
    </tr>
    @foreach ($providers as $provider)
    <tr class="*:py-2 *:px-4 border-2 border-gray-100">
        <td>{{ $provider->name }}</td>
        <td>{{ $provider->address }}, {{ $provider->barangay->name }}</td>
        <td>{{ $provider->contact_details }}</td>
        <td class="flex items-center gap-2">
            <!-- TODO: Add a modal to show the image -->
            <a href="{{ route('admin.burial.provider.view', ['id' => $provider->id]) }}" class="bg-yellow-300 rounded-lg p-2 cursor:pointer">
                <i class="fa-solid fa-pen"></i>
            </a>
        </td>
    </tr>
    @endforeach
</table>