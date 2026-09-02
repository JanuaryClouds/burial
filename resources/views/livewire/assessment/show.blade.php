<div class="d-flex flex-column gap-2 border-2 border-dashed border-gray-300 px-4 py-3 rounded">
	<h4>Assessment</h4>
	<strong>Problem Presented:</strong>
	<div class="bg-gray-200 rounded px-2 py-1">
		{{ $assessment->problem_presented }}
	</div>
	<div class="separator separator-dotted my-4"></div>
	<strong>Social Worker's Assessment:</strong>
	<div class="bg-gray-200 rounded px-2 py-1">
		{{ $assessment->swa }}
	</div>
</div>
