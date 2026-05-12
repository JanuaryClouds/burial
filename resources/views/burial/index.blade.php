@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @role('staff')
            @if (isset($cardData) && count($cardData) > 0)
                <div class="row">
                    @foreach ($cardData as $card)
                        <div class="col-6 col-lg-3">
                            <livewire:counter :model="$card['model']" :label="$card['label']" :scope="$card['scope']" :iconName="$card['iconName']" />
                        </div>
                    @endforeach
                </div>
            @endif
        @endrole
        <div class="card">
            <div class="card-body">
                @include('partials.datatable.index', [
                    'data' => $data,
                    'classes' => 'with-status with-actions',
                    'columns' => $columns,
                ])
            </div>
        </div>
        @can('create-reports')
            @if (isset($data) && $data->count() > 0)
                <div class="d-flex justify-content-center mt-10">
                    <a name="" id="" class="btn btn-primary align-self-end" data-no-loader
                        href="{{ route('applications.export.all') }}" role="button">Export Database to Excel</a>
                </div>
            @endif
        @endcan
    </div>
@endsection
