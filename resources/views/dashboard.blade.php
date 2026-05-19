@extends('layouts.app')
@section('content')
    <title>Dashboard</title>
    <div class="d-flex flex-column gap-4">
        @role('staff')
            @if (count($cardData) > 0)
                <div class="row">
                    @foreach ($cardData as $card)
                        <div class="col-6 col-lg-3">
                            <livewire:counter :model="$card['model']" :label="$card['label']" :scope="$card['scope']" :iconName="$card['iconName']"
                                :iconPathsCount="$card['iconPathsCount']" :route="$card['route']" />
                        </div>
                    @endforeach
                </div>
            @endif
        @endrole
        @unlessrole('staff')
            <div class="row">
                <div class="col-12 col-lg-8">
                    @include('user.partials.quick-links')
                </div>
                <div class="col-12 col-lg-4">
                    <livewire:notification.index lazy />
                </div>
            </div>
        @endunlessrole
        @role('staff')
            <div>
                @include('client.partials.latest-table', [
                    'data' => $data,
                    'columns' => $columns,
                ])
            </div>
        @endrole
    </div>
@endsection
