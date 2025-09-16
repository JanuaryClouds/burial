@props([
    'application' => []
])

<div class="container">
    <div class="d-flex justify-content-start align-items-center">
        <a name="" id="" class="btn btn-primary" href="{{ route('admin.applications.manage', ['id' => $application->id]) }}" role="button">
          <i class="fas fa-external-link-square-alt"></i>  
        </a>
    </div>
</div>
