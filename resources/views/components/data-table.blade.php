@props(['dataTableId', 'columns', 'data' => collect(), 'cardFields' => [], 'countPerPage' => 10])

<div class="record-data-table" data-responsive-data-table data-card-fields='@json($cardFields)' wire:ignore>
    <div data-table-toolbar></div>
    <div class="record-data-table-desktop">
        @include('partials.dataTable.index', compact('dataTableId', 'columns', 'data', 'countPerPage'))
    </div>
    <div class="record-data-table-cards" data-table-cards></div>
    <div data-table-footer></div>
</div>
