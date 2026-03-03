@props(['columns' => []])
<tr role="row">
    @foreach ($columns as $column)
        <th>{{ Str::title(Str::replace('_', ' ', $column['data'])) }}</th>
    @endforeach
</tr>
