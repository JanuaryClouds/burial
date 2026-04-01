@extends('layouts.metronic.admin')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="alert alert-danger fade show" role="alert">
            <strong>
                <i class="fa fa-exclamation-triangle">
                </i>
                Warning -
            </strong>
            This page manipulates how the system behaves. Please proceed with caution.
        </div>
        <div class="card">
            <form action="{{ route('system.update') }}" method="post">
                <div class="card-header">
                    <h4 class="card-title">System Settings</h4>
                </div>
                <div class="card-body">
                    @csrf
                    @include('system.partials.setting')
                </div>
                <div class="card-footer d-flex justify-content-end align-items-center gap-2">
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
