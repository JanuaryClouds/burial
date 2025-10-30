<div class="table-responsive">
    <div class="dataTables_wrapper container-fluid">
        <table id="generic-table" class="table data-table generic-table" style="width:100%">
            <thead>
                <tr role="row">
                    @foreach ($logs->first()->getAttributes() as $column => $value)
                        @php
                            $excemptions = ['id', 'updated_at', 'causer_type'];
                        @endphp
                        @if (!in_array($column, $excemptions))
                            @if ($column == "causer_id")
                                <th class="sorting sort-handler">Caused By</th>
                            @else
                                <th class="sorting sort-handler">{{ Str::title(Str::replace('_', ' ', $column)) }}</th>     
                            @endif
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr class="bg-white">
                        @foreach ($log->getAttributes() as $key => $value)
                            @if (!in_array($key, $excemptions))
                                @if (auth()->user()->hasRole('superadmin') && $key == 'causer_id')
                                    <td>{{ auth()->user()->where('id', $value)->first()->first_name ?? '' }}</td>
                                @else
                                    <td>{{ $value }}</td>
                                @endif
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>