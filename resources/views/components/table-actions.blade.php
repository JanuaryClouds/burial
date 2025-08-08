@props([
    'data' => [],
    'type' => null,
])

<div
    class="row justify-content-center align-items-center g-2"
>
    @if ($type === 'service')
        <div class="col" title="View Service">
            <a href="{{ route('admin.burial.view', ['id' => $data->id]) }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    @elseif ($type === 'request')
        <div class="col" title="View Request">
            <a href="{{ route('admin.burial.request.view', ['uuid' => $data->uuid]) }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    @elseif ($type === 'provider')
        <div class="col" title="View Request">
            <a href="{{ route('admin.burial.request.view', ['uuid' => $data->uuid]) }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    @endif

    <div class="col">
        
    </div>
    <div class="col">
        
    </div>
</div>
