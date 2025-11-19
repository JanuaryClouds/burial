@if (!$clients->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="table-responsive overflow-x-hidden">
                <div class="dataTables_wrapper">
                    <table id="generic-table" class="table data-table generic-table" style="width:100%">
                        <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                            <tr role="row">
                                @php
                                    $renderColumns = ['tracking_no', 'first_name', 'house_no', 'street', 'barangay_id', 'contact_no'];
                                @endphp
                                @foreach ($clients->first()->getAttributes() as $column => $value)
                                    @if (in_array($column, $renderColumns))
                                        @if ($column == 'first_name')
                                            <th class="sorting sort-handler">Name</th>
                                        @else
                                            <th class="sorting sort-handler">{{ str_replace('Id', '', str_replace('_', ' ', Str::title($column))) }}</th>
                                        @endif
                                    @endif
                                @endforeach
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $entry)
                                @if (!$entry->hasApplication())
                                    <tr class="">
                                        @foreach ($entry->getAttributes() as $key =>$value)
                                            @if (in_array($key, $renderColumns))
                                                @if (($key == 'first_name'))
                                                    <td>
                                                        {{ $entry->first_name }} {{ Str::limit($entry->middle_name, '1', '.') }} {{ $entry->last_name }} {{ $entry?->suffix }}
                                                    </td>
                                                @elseif (($key == 'barangay_id'))
                                                    <td>{{ $entry->barangay->name }}</td>
                                                @else
                                                    <td>{{ $value }}</td>
                                                @endif
                                            @endif
                                        @endforeach
                                        <td>
                                            <a href="{{ route('clients.view', ['id' => $entry->id]) }}" class="btn btn-primary">
                                                <i class="fas fa-eye pe-0"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif