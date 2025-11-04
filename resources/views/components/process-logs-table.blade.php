@if ($processLogs->count() > 0)
    <div class="table-responsive">
        <div class="dataTables_wrapper container-fluid">
            <table id="generic-table" class="table data-table generic-table" style="width:100%">
                <thead>
                    <tr role="row">
                        @foreach ($processLogs->first()->getAttributes() as $column => $value)
                            @php
                                $excemptions = ['id', 'updated_at', 'loggable_type'];
                            @endphp
                            @if (!in_array($column, $excemptions))
                                @if ($column == "burial_assistance_id")
                                    <th class="sorting sort-handler">Burial Assistance of</th>
                                @elseif ($column == "loggable_id")
                                    <th class="sorting sort-handler">Process</th>
                                @else
                                    <th class="sorting sort-handler">{{ Str::title(Str::replace('_', ' ', $column)) }}</th>     
                                @endif
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($processLogs as $log)
                        <tr class="bg-white">
                            @foreach ($log->getAttributes() as $key => $value)
                                @if (!in_array($key, $excemptions))
                                    @if (auth()->user()->hasRole('superadmin') && $key == 'added_by')
                                        <td>{{ auth()->user()->where('id', $value)->first()->first_name ?? '' }}</td>
                                    @elseif ($key == 'burial_assistance_id')
                                        @php
                                            $deceased = \App\Models\BurialAssistance::where('id', $value)->first()->deceased;
                                            $fullName = $deceased ? $deceased->first_name . ' ' . Str::limit($deceased->middle_name, 1, '.') . ' ' . $deceased->last_name : ' ' . $deceased?->suffix;
                                        @endphp
                                        <td>{{ $fullName }}</td>
                                    @elseif ($key == 'loggable_id')
                                        @php
                                            if (class_basename($log->loggable) === 'WorkflowStep') {
                                                $processName = $log->loggable->description;
                                            } else {
                                                $processName = $log->comments;
                                            }
                                        @endphp
                                        <td>{{ $processName }}</td>
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
@else
    <div class="alert alert-info">No process logs found.</div>
@endif