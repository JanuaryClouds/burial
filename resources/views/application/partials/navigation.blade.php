<x-card>
	<x-slot:header>
		Table of Contents
	</x-slot:header>
	<div class="list-group">
		<a href="#application-summary"
			class="list-group-item list-group-item-action">Application Summary</a>
		<a href="#status"
			class="list-group-item list-group-item-action">Status</a>
		@role('staff')
			@if (!$application->assessment)
				<a href="#assessment"
					class="list-group-item list-group-item-action">Assessment</a>
			@endif
			@if ($application->recommendations->count() == 0)
				<a href="#recommendation"
					class="list-group-item list-group-item-action">Recommendation</a>
			@endif
			<a href="#workflow-history"
				class="list-group-item list-group-item-action">Process History</a>
			<a href="#workflow-history-create-form"
				class="list-group-item list-group-item-action">Process Form</a>
		@endrole
		<a href="#documents"
			class="list-group-item list-group-item-action">Documents</a>
	</div>
</x-card>
