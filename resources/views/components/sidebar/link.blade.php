<a href="{{ $route ? $route : '#' }}"
	data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
	data-kt-menu-placement="right-start"
	@class(['menu-item', 'here' => Route::is($active_link)])>
	<span class="menu-link menu-center d-flex flex-column">
		<span class="menu-icon me-0">
			<x-icon.keen :icon="$icon"
				:size="'2x'"
				:pathsCount="$icon_paths" />
		</span>
		<small class="text-center text-gray-400 fw-semibold mt-1">{{ $text }}</small>
	</span>
	<!--end:Menu link-->
	<!--begin:Menu sub-->
	<div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
		<div class="menu-item">
			<div class="menu-content">
				<span class="menu-section fs-5 fw-bolder py-1">{{ $long_text ?? $text }}</span>
			</div>
		</div>
	</div>
	<!--end:Menu sub-->
</a>
<!--end:Common Pages-->
