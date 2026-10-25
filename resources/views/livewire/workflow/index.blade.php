<div class="d-flex flex-column gap-6">
    <x-card>
        @include('partials.dataTable.index', ['columns' => $columns, 'dataTableId' => $dataTableId])
        <x-slot:footer>
            <a href="{{ route('workflows.create') }}" class="btn btn-primary">
                <x-icon.font-awesome class="fa-plus" />
                Create Workflow
            </a>
        </x-slot:footer>
    </x-card>
</div>
