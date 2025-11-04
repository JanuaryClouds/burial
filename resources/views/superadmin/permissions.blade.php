@extends('layouts.stisla.superadmin')
<title>Permissions</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h4>Permissions</h4>
        </div>
    </section>
    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Permissions</h4>
                <button class="btn btn-primary rounded py-1" type="button" data-toggle="modal" data-target="#new-permissions-modal">
                    <i class="fas fa-plus"></i>
                    Add New Permission
                </button>
            </div>
            <div class="card-body">
                <x-cms-data-table type="permissions" :data="$data" />
            </div>
        </div>
    </div>
</div>
@endsection