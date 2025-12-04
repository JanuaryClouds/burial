@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    <form action="{{ route($type . '.update', [$type => $data]) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $data->name }}</h4>
            </div>
            <div class="card-body">
                @foreach ($data->getAttributes() as $field => $value)
                    @if (!in_array($field, ['id', 'created_at', 'updated_at']))
                        @include('components.form-input', [
                            'name' => $field,
                            'value' => $value,
                            'type' => 'text',
                            'label' => Str::replace('_', ' ', ucfirst($field)),
                        ])
                    @endif
                @endforeach
            </div>
            <div class="card-footer d-flex gap-2 justify-content-end">
                <a href="{{ route('cms.' . $type . 's') }}" class="btn btn-light">
                    Back
                </a>
                <button type="submit" class="btn btn-primary">
                    Save
                </button>
            </div>
        </div>
    </form>
@endsection
