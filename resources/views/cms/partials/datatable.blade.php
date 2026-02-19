@props(['data', 'resource'])
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
                    @foreach ($data->first()->getAttributes() as $column => $value)
                        @if (!in_array($column, $excemptions))
                            <th class="sorting sort-handler">{{ Str::replace('_', ' ', Str::title($column)) }}</th>
                        @endif
                    @endforeach
                    @if (Request::is('users.manage'))
                        <th>Role</th>
                    @endif
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $entry)
                    <tr class="">
                        @foreach ($entry->getAttributes() as $key => $value)
                            @if (!in_array($key, $excemptions))
                                <td>{{ $value }}</td>
                            @endif
                        @endforeach
                        <td>
                            @can('update', $entry)
                                <a href="{{ route($resource . '.edit', [$resource => $entry]) }}" class="btn btn-primary">
                                    <i class="fas fa-edit pe-0"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
