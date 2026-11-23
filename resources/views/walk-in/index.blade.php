@extends('layouts.app')
@section('content')
    <x-card>
        <x-slot:header>Walk-in Intake</x-slot:header>
        <p>Your saved drafts and submitted walk-in applications appear here. Superadmins can resume any draft.</p>
        @include('partials.dataTable.index', [
            'dataTableId' => 'walk-in-intakes',
            'columns' => collect([
                ['data' => 'applicant', 'title' => 'Applicant'],
                ['data' => 'encoder', 'title' => 'Encoded By'],
                ['data' => 'created', 'title' => 'Created'],
                ['data' => 'status', 'title' => 'Status'],
                ['data' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
            ]),
            'bodyView' => 'walk-in.rows',
            'emptyMessage' => 'No walk-in intakes found.',
        ])
        <x-slot:footer>
            <a class="btn btn-primary" href="{{ route('walk-in.create') }}">New Walk-in Intake</a>
        </x-slot:footer>
    </x-card>
    @include('partials.dataTable.script', ['dataTableId' => 'walk-in-intakes'])
@endsection
