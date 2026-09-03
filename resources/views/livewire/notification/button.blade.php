<a href="#"
	class="btn btn-icon btn-custom btn-active-color-primary position-relative show menu-dropdown"
	data-kt-menu-trigger="click"
	data-kt-menu-attach="parent"
	data-kt-menu-placement="bottom-end"
	data-bs-toggle="tooltip"
	data-bs-placement="bottom"
	title="Notifications or updates regarding your applications"
	wire:poll.30s>
	<x-icon.keen :icon="'notification'"
		:size="'1'"
		:pathsCount="3" />

	@if ($notifications)
		<span
			class="bullet bullet-dot bg-success h-15px w-15px position-absolute translate-middle top-0 start-100 animation-blink"></span>
	@endif
</a>
