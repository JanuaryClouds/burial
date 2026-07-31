@props(['columns' => []])
<tr role="row">
	@foreach ($columns as $column)
		@if (!in_array($column['data'], ['show_route']))
			<th>{{ Str::title(Str::replace('_', ' ', $column['data'] ?? '')) }}</th>
		@else
			<th></th>
		@endif
	@endforeach
</tr>
