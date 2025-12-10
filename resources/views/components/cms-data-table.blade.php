@props(['data', 'type'])
@php
    use Spatie\Permission\Models\Permission;
    $permissions = Permission::all();

    $excemptions = [
        'created_at',
        'updated_at',
        'extra_data_schema',
        'is_optional',
        'requires_extra_data',
        'email_verified_at',
        'password',
        'remember_token',
        'is_active',
        'guard_name',
    ];
@endphp
<div class="table-responsive overflow-x-hidden">
    <div class="dataTables_wrapper">
        <table id="cms-table" class="table data-table" style="width:100%">
            <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                <tr role="row">
                    @forelse ($data->first()->getAttributes() as $column => $value)
                        @if (!in_array($column, $excemptions))
                            <th class="sorting sort-handler">{{ Str::replace('_', ' ', Str::title($column)) }}</th>
                        @endif
                    @empty
                        <th>No {{ $type }}</th>
                    @endforelse
                    @if (Request::is('users.manage'))
                        <th>Role</th>
                    @endif
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $entry)
                    <tr class="">
                        @forelse ($entry->getAttributes() as $key => $value)
                            @if (!in_array($key, $excemptions))
                                <td>{{ $value }}</td>
                            @endif
                        @empty
                            <td>No {{ $type }}</td>
                        @endforelse
                        <td>
                            @can('update-resource', $entry)
                                <button class="btn btn-primary" type="button" data-toggle="modal"
                                    data-target="#edit-modal-{{ $entry->id }}">
                                    <i class="fas fa-edit pe-0"></i>
                                </button>
                            @endcan
                            @can('delete-resource', $entry)
                                <button class="btn btn-danger" type="button" data-toggle="modal"
                                    data-target="#delete-modal-{{ $entry->id }}">
                                    <i class="fas fa-trash pe-0"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>

                    @include('superadmin.partial.edit-content-modal')
                    @include('superadmin.partial.delete-content-modal')
                @endforeach
            </tbody>
        </table>
    </div>
</div>
