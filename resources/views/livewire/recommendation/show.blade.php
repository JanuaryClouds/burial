<div class="d-flex flex-column gap-2">
    <span><strong>Assigned Workflow:</strong> {{ $recommendation->workflow?->label() ?? 'Unassigned legacy recommendation' }}
        @if ($recommendation->workflow && !$recommendation->workflow->is_active)
            <span class="badge badge-warning">Stage processing paused</span>
        @endif
    </span>
	<span>
		<strong>Type of Funeral Assistance:</strong> {{ $recommendation->funeralAssistanceType?->name ?? 'N/A' }}
	</span>
	<span>
		<strong>Amount Extended:</strong> {{ $recommendation->amount_extended ?? 'N/A' }}
	</span>
	<span>
		<strong>Mode of Assistance:</strong> {{ $recommendation->modeOfAssistance?->name ?? 'N/A' }}
	</span>
</div>
