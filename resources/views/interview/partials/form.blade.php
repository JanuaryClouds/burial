<form id="schedule-interview-form" action="{{ route('interview.store', $application->client) }}" method="POST">
    @csrf
    <x-form-input name="schedule" label="Schedule" type="datetime-local"
        min="{{ Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" required />
    <x-form-textarea name="remarks" label="Remarks" />
</form>
