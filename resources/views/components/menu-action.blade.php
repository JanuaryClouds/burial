@props([
    'label',
    'icon',
    'enabled_when' => false,
    'permission' => false,
    'disabled_message' => 'You cannot perform this action',
    'unauthorized_message' => 'You do not have the permissions to perform this action',
])

@php
	$message = $disabled_message;

	if (!$permission) {
	    $message = $message . '. ' . $unauthorized_message;
	}
@endphp

@if ($enabled_when && $permission)
	{{ $slot }}
@else
	<button type="button"
		class="btn btn-secondary"
		data-bs-toggle="tooltip"
		data-bs-placement="bottom"
		title="{{ $message }}">
		<i class="{{ $icon }}"></i>
		{{ $label }}
	</button>
@endif
