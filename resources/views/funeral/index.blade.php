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
            <div class="card multicolor-border">
                <div class="card-header">
                    <h5 class="card-title">All Applications</h5>
                </div>
                <div class="card-body">
                    @include('partials.datatable.index', [
                        'data' => $allData,
                        'columns' => $allDataColumns,
                        'src' => 'allData',
                    ])
                </div>
            </div>
        @endrole
        <div class="card multicolor-border">
            <div class="card-header">
                <h5 class="card-title">My Applications</h5>
            </div>
            <div class="card-body">
                @include('partials.datatable.index', [
                    'data' => $personalData,
                    'columns' => $personalDataColumns,
                    'src' => 'personalData',
                ])
            </div>
        </div>
    </div>
@endsection
