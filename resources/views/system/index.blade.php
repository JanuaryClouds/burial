@extends('layouts.metronic.admin')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="card bg-danger text-white">
            <div class="card-header">
                <h4 class="card-title text-white">Warning</h4>
            </div>
            <div class="card-body">
                <p>This page controls how the system behaves. Please proceed with caution.</p>
            </div>
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
