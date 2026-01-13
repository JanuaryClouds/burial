@can('create', $resource)
    <button class="btn btn-primary rounded" type="button" data-bs-toggle="modal" data-bs-target="#newContent">
        Add New {{ ucfirst($resource) }}
    </button>
@endcan
