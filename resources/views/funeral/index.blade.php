@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @role('staff')
            @if (isset($cardData) && count($cardData) > 0)
                <div class="row">
                    @foreach ($cardData as $card)
                        <div class="col-6 col-lg-4">
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
                    'columns' => $columns,
                    'classes' => 'with-status with-actions',
                ])
            </div>
        </div>
    </div>
@endsection
