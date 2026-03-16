@extends('layouts.metronic.guest')
@section('content')
    <div class="container-xxl min-vh-100 py-10">
        <div class="d-flex flex-column gap-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $page_title }}</h4>
                </div>
                <div class="card-body">
                    @includeWhen(class_basename($data) === 'BurialAssistance', 'tracker.partials.burial')
                    @includeWhen(class_basename($data) === 'FuneralAssistance', 'funeral.partials.tracker', [
                        'funeralAssistance' => $data,
                    ])
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    {{-- TODO add change claimant modal here --}}
                </div>
            </div>
        </div>
    </div>
@endsection
