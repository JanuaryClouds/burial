@props([
    'stepId',
    'id'
])
<form action="{{ route('admin.application.deleteLog', ['id' => $id, 'stepId' => $stepId]) }}" method="post" class="m-0 p-0">
    @csrf
    <button class="btn btn-danger ml-2" type="submit">
        <i class="fas fa-trash"></i>
    </button>
</form>