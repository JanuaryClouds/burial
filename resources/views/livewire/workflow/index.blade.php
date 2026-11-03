<div class="d-flex flex-column gap-6">
    <x-card>
        <x-data-table :columns="$columns" :data="$mappedWorkflows" :data-table-id="$dataTableId"
            :card-fields="['primary' => 'name', 'secondary' => 'version', 'tertiary' => 'assigned_funeral_assistance_types', 'badge' => 'status', 'labels' => ['version' => 'Version', 'assigned_funeral_assistance_types' => 'Assigned assistance types']]" />
        <x-slot:footer>
            <a href="{{ route('workflows.create') }}" class="btn btn-primary">
                <x-icon.font-awesome class="fa-plus" />
                Create Workflow
            </a>
        </x-slot:footer>
    </x-card>
</div>
