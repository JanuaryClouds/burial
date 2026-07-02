@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @role('staff')
            @if (isset($cardData) && count($cardData) > 0)
                <div class="row gy-4">
                    @foreach ($cardData as $card)
                        <div class="col-6 col-lg-3">
                            <livewire:counter :model="$card['model']" :label="$card['label']" :scope="$card['scope']" :iconName="$card['iconName']"
                                :iconPathsCount="$card['iconPathsCount']" :route="$card['route']" />
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="card multicolor-border">
                <div class="card-header">
                    <h5 class="card-title">All Beneficiaries</h5>
                </div>
                <div class="card-body">
                    @include('partials.datatable.index', [
                        'data' => $allData,
                        'columns' => $allDataColumns,
                        'src' => 'allData',
                    ])
                </div>
            </div>
        @else
            <div class="card multicolor-border">
                <div class="card-header">
                    <h5 class="card-title">My Beneficiaries</h5>
                </div>
                <div class="card-body">
                    @include('partials.datatable.index', [
                        'columns' => $personalDataColumns,
                        'data' => $personalData,
                        'src' => 'personalData',
                    ])
                </div>
            </div>
        @endrole
    </div>
@endsection
