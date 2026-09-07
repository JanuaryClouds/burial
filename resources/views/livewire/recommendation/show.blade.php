<div class="d-flex flex-column gap-2">
	<span>
		<strong>Type of Funeral Assistance:</strong> {{ $recommendation->funeralAssistanceType->name }}
	</span>
	<span>
		<strong>Amount Extended:</strong> {{ $recommendation->amount_extended }}
	</span>
	<span>
		<strong>Mode of Assistance:</strong> {{ $recommendation->modeOfAssistance->name }}
	</span>
</div>
