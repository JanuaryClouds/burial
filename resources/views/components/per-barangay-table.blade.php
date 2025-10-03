<div class="table-responsive">
    <div class="dataTables_wrapper container-fluid">
        <table id="per-barangay-table" class="table data-table" style="width:100%">
            <thead>
                <tr role="row">
                    <th class="sorting">
                        Barangay
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deceasedPerBarangay as $barangay => $records)
                    <tr x-data="{ open: false }">
                        <td colspan="100%" class="border-bottom bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold">
                                    {{ $barangay }}
                                    <span class="badge badge-pill badge-info">{{ count($records) }}</span>
                                </span>
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-link" 
                                    x-on:click="open = !open"
                                    x-text="open ? 'Hide' : 'Show'">
                                </button>
                            </div>

                            {{-- Records under this barangay --}}
                            <template x-if="open" x-transition x-cloak>
                                <table class="table table-sm mt-2">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Date of Birth</th>
                                            <th>Date of Death</th>
                                            <th>Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $app)
                                            <tr class="border-1 border-bottom bg-white">
                                                <td>
                                                    {{ $app->first_name }} {{ $app->middle_name ?? '' }} {{ $app->last_name }} {{ $app->suffix ?? '' }}
                                                </td>
                                                <td>{{ $app->date_of_birth }}</td>
                                                <td>{{ $app->date_of_death }}</td>
                                                <td>
                                                    {{ $app->address }}, {{ $app->barangay->name }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </template>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>