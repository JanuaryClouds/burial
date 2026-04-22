@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="row">
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\Client'" :label="'Total Client Records'" :scope="'Total'" :iconName="'people'"
                    :iconPathsCount="5" />
            </div>
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\Client'" :label="'Referrals'" :scope="'Referral'" :iconName="'route'"
                    :iconPathsCount="4" />
            </div>
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\Client'" :label="'With Burial Assistances'" :scope="'BurialAssistance'" :iconName="'file-up'"
                    :iconPathsCount="2" :route="route('burial.index')" />
            </div>
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\Client'" :label="'With Libreng Libing'" :scope="'FuneralAssistance'" :iconName="'file-up'"
                    :iconPathsCount="2" :route="route('funeral.index')" />
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('partials.datatable.index', [
                    'data' => $data,
                    'columns' => $columns,
                ])
            </div>
        </div>
    </div>
@endsection
