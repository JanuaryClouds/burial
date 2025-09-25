@extends('layouts.stisla.admin')
@section('content')
<title>Dashboard</title>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
    </section>
    <section class="section">
        <div class="section-title">Activity</div>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Last updates you added</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                @if ($lastLogs->isEmpty())
                                    <p class="text-muted">No Activity as of yet</p>
                                @else
                                    @foreach ($lastLogs as $log)
                                        <ul class="list-unstyled">
                                            <div class="media mb-2 pb-2 border-bottom">
                                                <div class="media-body">
                                                    <span class="float-right text-muted">{{ $log->created_at->diffForHumans() }}</span>
                                                    <a href="{{ route('admin.applications.manage', ['id' => $log->burialAssistance->id]) }}">
                                                        <h5 class="mb-2">
                                                            {{ $log->burialAssistance->deceased->last_name }} {{ $log->burialAssistance->deceased->first_name }}
                                                        </h5>
                                                    </a>
                                                    {{ $log->loggable?->description ?? '' }}
                                                </div>
                                            </div>
                                        </ul>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Pending Applications</h4>
                        </div>
                        <div class="card-body">
                            @if ($pendingApplications->isEmpty())
                                <p class="text-muted">No Pending Applications</p>
                            @else
                                @foreach ($pendingApplications as $pa)
                                    <ul class="list-unstyled border-bottom">
                                        <div class="media mb-2">
                                            <div class="media-body">
                                                <a href="{{ route('admin.applications.manage', ['id' => $pa->id]) }}">
                                                    <h5 class="mb-2">
                                                        {{ $pa->deceased->last_name }} {{ $pa->deceased->first_name }}
                                                    </h5>
                                                </a>
                                                {{ $pa->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </ul>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </section>
    <section class="section">
        <div class="section-title">Charts</div>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Burial Assistance Applications per Barangay</h4>
                        </div>
                        <div class="card-body">
                            <canvas 
                                id="applicationsDistributions"
                                data-chart-data='@json($perBarangay->pluck('count'))'
                                data-chart-labels='@json($perBarangay->pluck('name'))'
                                data-chart-type="pie"
                            ></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 ">
                    <div class="card">
                        <div class="card-header">
                            <h4>Your Activity Summary</h4>
                        </div>
                        <div class="card-body">
                            <canvas 
                                id="montlyActivityGraph"
                                data-chart-data='@json($monthlyActivity->pluck('count'))'
                                data-chart-labels='@json($monthlyActivity->pluck('month'))'
                                data-chart-type="line"
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection