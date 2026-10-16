@foreach ($rows as $key => $value)
	@if ($columns->contains('data', $key))
		<tr>
			<td>{{ $value }}</td>
		</tr>
	@endif
@endforeach
