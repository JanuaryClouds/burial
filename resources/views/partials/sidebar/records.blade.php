<!--begin:records-->
<div data-kt-menu-trigger="{default: 'click'}"
	data-kt-menu-placement="right-start"
	@class([
		'menu-item',
		'here' =>
			Route::is('interview*') ||
			Route::is('application*') ||
			Route::is('client*') ||
			Route::is('beneficiary*') ||
			Route::is('referral*') ||
			Route::is('rejection*') ||
			Route::is('cancellation*'),
	])>
	<!--begin:Menu link-->
	<span class="menu-link menu-center d-flex flex-column">
		<span class="menu-icon me-0">
			<x-icon.keen :icon="'folder'"
				:size="'2x'"
				:pathsCount="2" />
		</span>
		<small class="text-center text-gray-400 fw-semibold mt-1">Records</small>
	</span>
	<!--end:Menu link-->
	<!--begin:Menu sub-->
	<div class="menu-sub menu-sub-dropdown menu-sub-indentation px-2 py-4 w-250px mh-75 overflow-auto">
		<div class="menu-item">
			<div class="menu-content">
				<span class="menu-section fs-5 fw-bolder ps-1 py-1">Records</span>
			</div>
		</div>
		<x-sidebar.sub-link :route="route('client.index')"
			text="Clients" />
		<x-sidebar.sub-link :route="route('beneficiary.index')"
			text="Beneficiaries" />
		<x-sidebar.sub-link :route="route('interview.index')"
			text="Interviews" />
		<x-sidebar.sub-link :route="route('referral.index')"
			text="Referrals" />
		{{-- TODO: include rejections --}}
		{{-- TODO: include cancellations --}}
		<x-sidebar.sub-link :route="route('application.index')"
			text="Applications" />
	</div>
	<!--end:Menu sub-->
</div>
<!--end:records-->
