@can('create', $resource)
    <button class="btn btn-primary rounded" type="button" data-bs-toggle="modal" data-bs-target="#newContent">
        Add New {{ ucfirst($resource) }}
    </button>
    <div id="newContent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newContent" aria-hidden="true">
        <form action="{{ route($resource . '.store') }}" method="post">
            @csrf
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New {{ ucfirst($resource) }}</h5>
                        <button class="btn btn-icon btn-sm btn-active-icon-primary" type="button" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-column gap-2">
                            @foreach ($data->last()->getAttributes() as $field => $value)
                                @if (!in_array($field, ['id', 'created_at', 'updated_at', 'email_verified_at', 'remember_token', 'is_active']))
                                    <div class="form-group">
                                        <label
                                            for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        @can('create', $data)
                                            <input type="text" name="{{ $field }}" id="{{ $field }}"
                                                class="form-control">
                                        </div>
                                    @endcan
                                @endif
                            @endforeach
                            @if ($data->first() instanceof \Spatie\Permission\Models\Role)
                                @include('cms.partials.create-role')
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button class="btn btn-primary" type="submit">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endcan
