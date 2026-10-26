<div class="d-flex flex-column gap-6" wire:poll.10s="loadData">

    @if ($isStaffDashboard)
        <div class="row dashboard-overview">
            <div class="col-12 col-lg-7">
                @include('dashboard.partials.recent-applications')
            </div>
            <div class="col-12 col-lg-5">
                @include('dashboard.partials.statistics', ['compact' => true])
            </div>
        </div>
    @else
        <div>
            @include('dashboard.partials.statistics')
        </div>
        {{-- start::Datatable --}}
        <div>
            <x-card>
                @include('partials.dataTable.index', [
                    'columns' => $columns,
                    'data' => $mappedApplications,
                    'dataTableId' => $dataTableId,
                ])
            </x-card>
        </div>
        {{-- end::Datatable --}}
    @endif

    @if ($isStaffDashboard)
        {{-- start::Charts --}}
        <div>
            @include('dashboard.partials.charts')
        </div>
        {{-- end::Charts --}}
    @endif
</div>
