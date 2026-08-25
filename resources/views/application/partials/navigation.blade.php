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
			<a href="#workflow-history"
				class="list-group-item list-group-item-action">Process History</a>
			<a href="#workflow-stage-form"
				class="list-group-item list-group-item-action">Process Form</a>
		@endrole
		<a href="#documents"
			class="list-group-item list-group-item-action">Documents</a>
		@role('staff')
			<a href="#assessment"
				class="list-group-item list-group-item-action">Assessment</a>
			<a href="#recommendation"
				class="list-group-item list-group-item-action">Recommendation</a>
		@endrole
	</div>
</x-card>
