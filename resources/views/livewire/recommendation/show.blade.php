<div class="d-flex flex-column gap-2">
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
