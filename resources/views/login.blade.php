@extends('layouts.guest')
<title>Login</title>
@section('content')
	<div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
		<div class="card shadow-sm w-100 w-md-75 w-lg-50">
			<div class="card-body d-flex flex-center flex-column">
				<div class="d-flex flex-center flex-column flex-column-fluid">
					<img src="{{ asset('images/CSWDO.webp') }}"
						alt="cswdo logo"
						class="w-25">
					<h1 class="text-dark fw-bolder mb-3">Sign In</h1>
					<div class="col d-flex flex-column flex-center">
						<p class="text-start text-sm-center text-muted fw-semibold fs-6">
							Please enter your credentials to access the dashboard.
						</p>
					</div>
					<div class="col">
						<livewire:auth.login />
					</div>
				</div>
			</div>
		</div>
	</div>
    <script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/staff-login.js') }}" defer></script>
@endsection
