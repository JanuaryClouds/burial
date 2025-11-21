@extends('layouts.metronic.admin')
<title>Permissions</title>
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4 class="card-title">Permissions</h4>
    </div>
    <div class="card-body">
        <x-cms-data-table type="permissions" :data="$data" />
    </div>
</div>
@endsection