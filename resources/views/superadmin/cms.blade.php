@extends('layouts.metronic.admin')
<title>CMS - {{ $type }}</title>
@php
	$routeName = 'superadmin.cms.update';
	if($type === 'barangays') {
		$paramKey = 'id';	
	} elseif ($type === 'relationships') {
		$paramKey = 'id';	
	} elseif ($type === 'providers') {
		$paramKey = 'id';
	} elseif ($type === 'services') {
		$paramKey = 'id';
	} else {
		$paramKey = 'uuid';
	}
@endphp

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between">
		<h4 class="card-title">{{ Str::substr(Str::ucfirst($type), 0, -1) }}</h4>
	</div>
	<div class="card-body">
		<x-cms-data-table :type="$type" :data="$data" />
	</div>
</div>
@endsection