@props(['applications'])
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
                @foreach ($applications as $barangay => $records)
                    <tr x-data="{ open: false }">
                        <td colspan="100%" class="border-bottom">
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
                                            <th>Deceased</th>
                                            <th>Claimant</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $app)
                                            <tr class="border-1 border-bottom">
                                                <td>
                                                    {{ $app->deceased->first_name }} {{ $app->deceased->middle_name ?? '' }} {{ $app->deceased->last_name }} {{ $app->deceased->suffix ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $app->claimant->first_name }} {{ $app->claimant->middle_name ?? '' }} {{ $app->claimant->last_name }} {{ $app->claimant->suffix ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $app->claimant->address }}, {{ $app->claimant->barangay->name }}
                                                </td>
                                                <td>
                                                    {{ Str::ucfirst($app->status)}}
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