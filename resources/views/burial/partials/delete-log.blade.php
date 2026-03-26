@props(['id', 'step' => null])
@if (!in_array($step, [9, 10, 11, 12]) || app()->hasDebugModeEnabled())
    <form action="{{ route('process-logs.delete', ['id' => $id]) }}" method="post" class="m-0 p-0">
        @csrf
        <button class="btn text-danger btn-sm" type="submit">
            <i class="fas fa-trash text-white"></i>
        </button>
    </form>
@endif
