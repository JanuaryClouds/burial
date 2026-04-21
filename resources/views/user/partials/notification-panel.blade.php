<div class="card">
    <div class="card-header">
        <h4 class="card-title">Notifications</h4>
    </div>
    <div class="card-body">
        @forelse ($notifications as $notification)
            <div class="alert alert-primary" role="alert">
                <h4 class="alert-heading">{{ $notification['subject'] }}</h4>
                <p>Alert Content</p>
                <hr />
                <p class="mb-0">{{ $notification['body'] }}</p>
            </div>
        @empty
            <p class="text-muted text-center">No Notifications</p>
        @endforelse
    </div>
    <div class="card-footer">

    </div>
</div>
