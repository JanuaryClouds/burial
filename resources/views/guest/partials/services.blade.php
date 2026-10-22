<section id="services"
	class="section">
	<div class="container">
		<div class="section-head">
			<h2 class="h2">Services Offered</h2>
		</div>
		<div class="divider">
			<span class="r"></span>
			<span class="y"></span>
			<span class="b"></span>
		</div>
		<div class="row g-2">
			@foreach ($services as $service)
				<div class="col-12 col-lg-6">
					<div class="card">
						<div class="card-body">
							<span class="ribbon">{{ $service->name }}</span>
							<p>
								{{ $service->description }}
							</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
