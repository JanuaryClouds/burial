@extends('layouts.stisla.admin')
<title>{{ $page_title }}</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ $page_title }}</h1>
        </div>
    </section>
    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Manage Funeral Assistances</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="dataTables_wrapper container-fluid">
                        <table id="{{ $resource }}-table" class="table data-table" style="width:100%">
                            <thead>
                                <tr role="row">
                                    @foreach ($data->first()->getAttributes() as $column => $value)
                                        @if (in_array($column, $renderColumns))
                                            @if ($column == 'client_id')
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
                                @foreach ($data as $entry)
                                    <tr class="bg-white">
                                        @foreach ($entry->getAttributes() as $key =>$value)
                                            @if (in_array($key, $renderColumns))
                                                @if ($key == 'client_id')
                                                    <td>
                                                        {{ $entry->client->first_name }} 
                                                        {{ Str::limit($entry->client->middle_name, '1', '.') }} 
                                                        {{ $entry->client->last_name }}
                                                    </td>
                                                @endif
                                            @endif
                                        @endforeach
                                        <td>
                                            <a href="{{ route('clients.view', ['id' => $entry->id]) }}" class="btn btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection