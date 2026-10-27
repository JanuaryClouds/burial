<x-card>
	<x-slot:header>
		Table of Contents
	</x-slot:header>
	<div class="list-group application-navigation">
		<a href="#application-summary"
			class="list-group-item list-group-item-action">Application Summary</a>
		<a href="#status"
			class="list-group-item list-group-item-action">Status</a>
        @if (auth()->user()->isInternal())

            <a href="#assessment" class="list-group-item list-group-item-action">Interview &amp; Assessment</a>
            <a href="#recommendation" class="list-group-item list-group-item-action">Recommendation</a>
            @can('office-access')
                <a href="#printable-documents" class="list-group-item list-group-item-action">Printable Documents</a>
            @endcan
			<a href="#workflow-history"
				class="list-group-item list-group-item-action">Process History</a>
			<a href="#workflow-history-create-form"
				class="list-group-item list-group-item-action">Process Form</a>
        @endif
		<a href="#documents"
			class="list-group-item list-group-item-action">Documents</a>
	</div>
</x-card>
