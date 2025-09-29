@props(['pendingApplications'])
<div class="card" style="font-size: small;">
    <div class="card-header">
        <h4>Pending Applications</h4>
        @if (auth()->user()->isAdmin())
            <div class="card-header-action">
                <a href="{{ route('admin.applications.pending') }}" class="btn btn-primary">
                    View All
                </a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if ($pendingApplications->isEmpty())
            <p class="text-muted">No Pending Applications</p>
        @else
            @foreach ($pendingApplications as $pa)
                <ul class="list-unstyled border-bottom">
                    <div class="media mb-2">
                        <div class="media-body">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.applications.manage', ['id' => $pa->id]) }}">
                                    <h5 class="mb-2">
                                        {{ $pa->deceased->last_name }}
                                        {{ Str::limit($pa->deceased?->middle_name, 1, '.') }}
                                        {{ $pa->deceased->first_name }}
                                        {{ $pa->deceased?->suffix }}
                                    </h5>
                                </a>
                            @else
                                <h5 class="mb-2">
                                    {{ $pa->deceased->last_name }}
                                    {{ Str::limit($pa->deceased?->middle_name, 1, '.') }}
                                    {{ $pa->deceased->first_name }}
                                    {{ $pa->deceased?->suffix }}
                                </h5>
                            @endif
                            <span class="d-flex align-items-baseline text-muted">
                                <p>{{ $pa->created_at->diffForHumans() }}</p>
                                <i class="fas fa-image-portrait ml-4 mr-1"></i>
                                <p>{{ $pa->claimant->first_name }} {{ $pa->claimant->last_name }}</p>
                                <i class="fas fa-city ml-4 mr-1"></i>
                                <p>{{ $pa->claimant->barangay->name }}</p>
                                <i class="fas fa-money-bill ml-4 mr-1"></i>
                                <p>{{ $pa->amount }}</p>
                            </span>
                        </div>
                    </div>
                </ul>
            @endforeach
        @endif
    </div>
</div>