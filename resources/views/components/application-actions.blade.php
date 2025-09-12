@props([
    'application' => []
])
@php
    if($application->status === 'pending') {
        $routeName = 'admin.applications.manage';
        $status = 'pending';
    } elseif ($application->status === 'processing') {
        $routeName = 'admin.applications.manage';
        $status = 'processing';
    } elseif ($application->status === 'approved') {
        $routeName = 'admin.applications.manage';
        $status = 'approved';
    } elseif ($application->status === 'released') {
        $routeName = 'admin.applications.manage';
        $status = 'released';
    }
@endphp

<div class="container">
    <div class="d-flex justify-content-start align-items-center">
        <a name="" id="" class="btn btn-primary" href="{{ route($routeName, ['id' => $application->id]) }}" role="button">
          <i class="fas fa-external-link-square-alt"></i>  
        </a>
    </div>
</div>
