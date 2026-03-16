@foreach (config('system_setting') as $key => $setting)
    @if ($setting['type'] === 'boolean')
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input type="hidden" name="{{ $key }}" value="0">
            <input class="form-check-input" type="checkbox" value="1" name="{{ $key }}"
                id="{{ $key }}" {{ $settings->$key == 1 ? 'checked' : '' }} />
            <label class="form-check-label" for="{{ $key }}">
                {{ ucfirst(str_replace('_', ' ', $key)) }}
            </label>
        </div>
    @elseif($setting['type'] === 'integer')
        <label for="{{ $key }}">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
        <input type="number" name="{{ $key }}" id="{{ $key }}" value="{{ $settings->$key }}"
            @isset($setting['min']) min="{{ $setting['min'] }}" @endisset
            @isset($setting['max']) max="{{ $setting['max'] }}" @endisset>
    @elseif($setting['type'] === 'string')
        <label for="{{ $key }}">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
        <input type="text" name="{{ $key }}" id="{{ $key }}" value="{{ $settings->$key }}">
    @endif
@endforeach
