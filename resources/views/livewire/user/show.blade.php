<div class="d-flex flex-column gap-4">
	<div class="row">
		<div class="col-12 col-md-8 col-lg-4">
			<x-form.display :content="$user->first_name"
				label="First Name" />
		</div>
		<div class="col-12 col-md-4 col-lg-3">
			<x-form.display :content="$user->middle_name"
				label="Middle Name" />
		</div>
		<div class="col-12 col-md-8 col-lg-3">
			<x-form.display :content="$user->last_name"
				label="Last Name"
				:required="true" />
		</div>
		<div class="col-12 col-md-4 col-lg-2">
			<x-form.display :content="$user->suffix"
				label="Suffix" />
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-md-6">
			<x-form.display :content="$user->email"
				label="Email Address" />
		</div>
		<div class="col-12 col-md-6">
			<x-form.display :content="$user->contact_number"
				label="Contact Number" />
		</div>
	</div>
	<div class="d-flex justify-content-end align-items-center gap-4">
		<a href="{{ back()->getTargetUrl() }}"
			class="btn btn-sm btn-light">
			<x-icon.font-awesome class="fa-arrow-left" />
			Back
		</a>
	</div>
</div>
