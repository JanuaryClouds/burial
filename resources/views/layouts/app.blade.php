<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
	data-bs-theme="system">

<head>
	@include('partials.document.head')
</head>

<body id="kt_body"
	class="header-fixed header-mobile-fixed aside-enabled aside-fixed aside-secondary-disabled overflow-x-hidden">
	@include('partials.theme.script')
	<x-loader />
	<div class="d-flex flex-column flex-root min-vh-100">
		<div class="page d-flex flex-row flex-column-fluid">
			@include('partials.sidebar.index')
			<div class="wrapper d-flex flex-column flex-row-fluid">
				@include('partials.header')
				<div class="content d-flex flex-column flex-column-fluid">
					<div class="container-xxl">
						<div class="d-flex flex-column gap-6">
							@yield('content')
							@if ($errors->any())
								<div class="alert alert-warning alert-dismissible fade show"
									role="alert">
									<button type="button"
										class="btn-close"
										data-bs-dismiss="alert"
										aria-label="Close"></button>
									<strong>Invalid Form</strong> Some fields are incorrectly answered
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@include('components.notification.modal')
	@include('partials.document.scripts')
</body>

</html>
