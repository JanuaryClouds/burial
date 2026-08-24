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
			<a href="#interview-history"
				class="list-group-item list-group-item-action">Interview History</a>
			<a href="#assessment"
				class="list-group-item list-group-item-action">Assessment</a>
		@endrole
	</div>
</x-card>
