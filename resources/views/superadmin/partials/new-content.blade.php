@php
    $controller = class_basename(request()->route()->getController());
    $hasStore = method_exists($controller, 'store');
@endphp
@if ($hasStore)
    <button class="btn btn-primary rounded" type="button" data-bs-toggle="modal" data-bs-target="#newContent">
        Add New {{ ucfirst($resource) }}
    </button>
@endif
