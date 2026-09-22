<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
	data-kt-menu-placement="right-start"
	@class([
		'menu-item',
		'here' => Route::is('system.*') || Route::is('activity.logs'),
	])>
	<span class="menu-link menu-center d-flex flex-column">
		<span class="menu-icon me-0">
			<x-icon.keen icon="setting-2"
				size="2x"
				:pathsCount="2" />
		</span>
		<small class="text-center text-gray-400 fw-semibold mt-1">System</small>
	</span>
	<div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
		<div class="menu-item">
			<div class="menu-content ">
				<span class="menu-section fs-5 fw-bolder ps-1 py-1">
					System
				</span>
			</div>
		</div>
	</div>
</div>
