@if ($parent)
	<nav class="page-navigation rounded border bg-body px-5 py-4 shadow-sm"
		aria-label="Breadcrumb">
		<a href="{{ $parent['url'] }}"
			class="d-flex d-md-none align-items-center gap-2 text-primary fw-semibold"
			aria-label="{{ 'Back to ' . $parent['label'] }}">
			<i class="fa-solid fa-arrow-left flex-shrink-0" aria-hidden="true"></i>
			<span class="text-truncate">{{ $parent['label'] }}</span>
		</a>
		<ol class="breadcrumb d-none d-md-flex flex-wrap align-items-center mb-0">
			@foreach ($items as $item)
				<li @class(['breadcrumb-item', 'active' => $loop->last])
					@if ($loop->last) aria-current="page" @endif>
					@if (!$loop->last && $item['url'])
						<a href="{{ $item['url'] }}" class="text-primary">{{ $item['label'] }}</a>
					@else
						<span class="text-body-secondary">{{ $item['label'] }}</span>
					@endif
				</li>
			@endforeach
		</ol>
	</nav>
@endif
