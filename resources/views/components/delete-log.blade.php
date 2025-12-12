@props(['stepId', 'id'])
@if (!in_array($stepId, [9, 10, 11, 12]))
    <form action="{{ route('burial.deleteLog', ['id' => $id, 'stepId' => $stepId]) }}" method="post" class="m-0 p-0">
        @csrf
        <button class="btn text-danger btn-sm" type="submit">
            <i class="fas fa-trash"></i>
        </button>
    </form>
@endif
