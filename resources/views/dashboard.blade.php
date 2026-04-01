@extends('layouts.metronic.admin')
@section('content')
    <title>Dashboard</title>
    @include('admin.partials.cards')
    <div class="row mt-5 mt-xl-8">
        <div class="col-12">
            @include('client.partials.latest-table', [
                'data' => $data,
                'columns' => $columns,
            ])
        </div>
    </div>
@endsection
