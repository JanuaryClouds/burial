<form action="{{ route('recommendation.store', $application) }}" id="recommendation-form" method="POST">
    @csrf
    @method('POST')
    <x-form-multiselect name="funeral_assistance_types" label="Funeral Assistance Types" :options="$funeralAssistanceTypes"
        :required="true" optionLabel="name" optionValue="uuid" />
    <x-form-input name="amount_extended" label="Amount" type="number" min="0" required="true" />
    <x-form-select name="mode_of_assistance_id" label="Mode of Assistance" :options="$modes" required="true" />
</form>
