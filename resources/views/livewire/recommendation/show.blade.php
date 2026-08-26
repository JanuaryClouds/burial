<div class="d-flex flex-column gap-2 border-2 border-dashed border-gray-300 px-4 py-3 rounded">
	<h4>Recommendation</h4>
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
