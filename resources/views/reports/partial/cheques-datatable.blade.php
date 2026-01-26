@if (!$cheques->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="table-responsive overflow-x-hidden">
                <div class="dataTables_wrapper">
                    <table id="generic-table" class="table data-table generic-table" style="width:100%">
                        <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                            <tr role="row">
                                @foreach ($cheques->first()->getAttributes() as $column => $value)
                                    @php
                                        $excemptions = ['id', 'created_at', 'updated_at'];
                                    @endphp
                                    @if (!in_array($column, $excemptions))
                                        <th class="sorting sort-handler">
                                            {{ Str::title(Str::replace('_', ' ', $column)) }}</th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cheques as $entry)
                                <tr class="">
                                    @foreach ($entry->getAttributes() as $key => $value)
                                        @if (!in_array($key, $excemptions))
                                            @if ($key == 'burial_assistance_id')
                                                <td>{{ $entry->burialAssistance->tracking_no }}</td>
                                            @elseif ($key == 'claimant_id')
                                                <td>{{ $entry->claimant->first_name }}
                                                    {{ Str::limit($entry->claimant->middle_name, 1, '.') }}
                                                    {{ $entry->claimant->last_name }} </td>
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
        </div>
    </div>
@endif
