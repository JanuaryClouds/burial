@props(['icon_name', 'paths_count' => 0, 'icon_size' => '2x'])

<i class="ki-duotone ki-{{ $icon_name }} fs-{{ $icon_size }}">
    @for ($i = 1; $i <= $paths_count; $i++)
        <span class="path{{ $i }}"></span>
    @endfor
</i>
