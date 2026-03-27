@extends('layouts.metronic.guest')
@section('content')
    <div class="container-xxl min-vh-100 py-10">
        <div class="d-flex flex-column gap-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ $page_title }}</h4>
                    @auth
                        @if ($show_tracker_code)
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#showTrackingCodeModal">
                                Show Tracking Code
                            </button>
                        @endif
                    @endauth
                </div>
                <div class="card-body d-flex flex-column gap-4">
                    @includeWhen(class_basename($data) === 'BurialAssistance', 'tracker.partials.burial')
                    @includeWhen(class_basename($data) === 'FuneralAssistance', 'tracker.partials.funeral')
                </div>
            </div>
            @if (class_basename($data) === 'BurialAssistance' && $data->claimantChanges->count() == 0)
                @auth()
                    @php
                        $isOwner = auth()
                            ->user()
                            ->clients()
                            ->whereHas('claimant', function ($q) use ($data) {
                                $q->where('id', $data->claimant->id);
                            })
                            ->exists();
                    @endphp
                    @includeWhen(
                        $isOwner &&
                            $data->status != 'approved' &&
                            $data->status != 'released' &&
                            $data->status != 'rejected',
                        'burial.partials.claimant-change-form')
                @endauth
            @endif
            @auth
                @includeWhen($show_tracker_code, 'tracker.partials.code-modal')
            @endauth
        </div>
    </div>
@endsection
