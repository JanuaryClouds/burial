@if (isset($resource))
    @if (Route::has($resource . '.store'))
        <button class="btn btn-sm btn-primary rounded" type="button" data-bs-toggle="modal" data-bs-target="#newContent">
            Add New {{ ucfirst($resource) }}
        </button>
    @endif
@endif
