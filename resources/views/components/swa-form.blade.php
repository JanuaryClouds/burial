@props([
    'application' => [],
    'disabled' => false,
    'readonly' => false,    
])
<div class="bg-white shadow-sm rounded p-4">
    <h2>Social Worker's Assessment</h2>
    <form action="{{ route('burial-assistances.swa.save', ['id' => $application->id]) }}" method="post">
        @csrf
        @include('components.form-textarea', [
            'name' => 'swa',
            'label' => 'Social Worker\'s Assessment',
            'value' => $application->swa ?? null,
            'readonly' => $application->swa,
        ])
        @if (Request::is('admin/applications/*'))
            <button class="btn btn-info" type="submit">
                <i class="fas fa-floppy-disk"></i>
                Update SWA
            </button>
        @endif
    </form>
</div>