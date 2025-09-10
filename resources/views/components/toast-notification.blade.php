<!-- TODO: Either use Toastr or use bootstrap's toast notification -->
@if (session()->has('success'))
    <div class="toast toast-success" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img class="rounded mr-2" src="" alt="Toast icon alternate text">
            <strong class="mr-auto">Title</strong>
            <small>Note</small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            
        </div>
    </div>
@endif