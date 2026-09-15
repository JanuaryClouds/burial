<h4>Social Information</h4>
<div class="row">
	<div class="col-12 col-md-6 col-lg-5 col-xl-4">
		<x-form.select wire:model='religionId'
			name="religionId"
			label="Religion"
			:selected="$religionId ?? ''"
			:options="$religions ?? []"
			:required="true" />
	</div>
</div>
