@if (!$funerals->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="table-responsive overflow-x-hidden">
                <div class="dataTables_wrapper">
                    <table id="generic-table" class="table data-table generic-table" style="width:100%">
                        <thead>
                            <tr role="row">
                                @foreach ($funerals->first()->getAttributes() as $column => $value)
                                    @php
                                        $excemptions = ['id', 'created_at', 'updated_at'];
                                    @endphp
                                    @if (!in_array($column, $excemptions))
                                        @if ($column == 'client_id')
                                            <th class="sorting sort-handler">
                                                Client
                                            </th>
                                            <th class="sorting sort-handler">Beneficiary</th>
                                        @else
                                            <th class="sorting sort-handler">
                                                {{ Str::title(Str::replace('_', ' ', $column)) }}</th>
                                        @endif
                                    @endif
                                    @empty
                                        <th>No Funerals</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($funerals as $entry)
                                    <tr class="bg-white">
                                        @foreach ($entry->getAttributes() as $key => $value)
                                            @if (!in_array($key, $excemptions))
                                                @if ($key == 'client_id')
                                                    <td>{{ $entry->client->first_name }} {{ $entry->client->middle_name }}
                                                        {{ $entry->client->last_name }} {{ $entry->client->suffix ?? '' }}
                                                    </td>
                                                    <td>{{ $entry->client->beneficiary->first_name }}
                                                        {{ Str::limit($entry->client->beneficiary->middle_name, '1', '.') }}
                                                        {{ $entry->client->beneficiary->last_name }}
                                                        {{ $entry->client->beneficiary->suffix ?? '' }}</td>
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
