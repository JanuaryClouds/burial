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
                    @includeWhen($isOwner, 'burial.partials.claimant-change-form')
                @endauth
            @endif
        </div>
    </div>
@endsection
