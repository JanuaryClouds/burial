@php
	$assessment = $application->assessment ?? null;
@endphp
<form action="{{ route('assessment.store', $application) }}"
	method="POST"
	id="assessment-form">
	@csrf
	@method('POST')
	<x-form.input name="problem_presented"
		label="Problem Presented"
		value="{{ $assessment?->problem_presented }}"
		readonly="isset($assessment)"
		required="true"
		type="textarea" />
	<x-form.input name="swa"
		label="Social Worker's Assessment"
		value="{{ $assessment?->swa }}"
		readonly="isset($assessment)"
		required="true"
		type="textarea" />
	<x-form.input name="assistance_requested"
		label="Assistance Requested"
		value="{{ $assessment?->assistance_requested }}"
		readonly="isset($assessment)"
		required="true"
		type="textarea" />
</form>
