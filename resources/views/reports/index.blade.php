@extends('layouts.stisla.superadmin')

<title>Summary</title>
@section('content')
<div class="main-content">
    <div class="row">
        @foreach ($statistics as $statistic)
            <div class="col-12 col-lg-6">
                <x-card-stats :statistics="$statistic"/>
            </div>
        @endforeach
    </div>
</div>
@endsection