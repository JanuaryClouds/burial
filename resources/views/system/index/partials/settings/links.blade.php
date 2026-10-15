<div class="row">
	<div class="col-6 col-md-4 col-lg-3">
		<a href="{{ route('system.settings') }}"
			class="btn btn-lg btn-outline btn-outline-primary w-100">
			<x-icon.font-awesome class="fa-external-link" />
			System Settings
		</a>
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<a href="{{ route('system.logs.index') }}"
			class="btn btn-lg btn-outline btn-outline-primary w-100">
			<x-icon.font-awesome class="fa-external-link" />
			System Logs
		</a>
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<a href="{{ route('role.index') }}"
			class="btn btn-lg btn-outline btn-outline-primary w-100">
			<x-icon.font-awesome class="fa-external-link" />
			Roles
		</a>
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<a href="{{ route('user.index') }}"
			class="btn btn-lg btn-outline btn-outline-primary w-100">
			<x-icon.font-awesome class="fa-external-link" />
			Users
		</a>
	</div>
</div>
