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
                    @includeWhen(class_basename($data) === 'FuneralAssistance', 'tracker.partials.funeral')
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    @if (class_basename($data) === 'BurialAssistance')
                        @auth()
                            @php
                                $isOwner = auth()
                                    ->user()
                                    ->clients()
                                    ->whereHas('claimant', function ($q) use ($data) {
                                        $q->where('id', $data->claimant_id);
                                    })
                                    ->exists();
                            @endphp
                            @includeWhen($isOwner, 'burial.partials.claimant-change-modal')
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
