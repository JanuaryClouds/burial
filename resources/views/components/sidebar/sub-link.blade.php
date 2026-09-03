<div class="menu-item">
	<a href="{{ $route }}"
		@class(['active' => Route::is($route . '*'), 'menu-link'])>
		<span class="menu-bullet">
			<span class="bullet bullet-dot"></span>
		</span>
		<span class="menu-title">{{ $text }}</span>
	</a>
</div>
