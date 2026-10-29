@props(['dataTableId'])

<script type="module" nonce="{{ $nonce ?? '' }}">
    import { initDataTable } from @json(asset('js/datatable.js'));

    // Reuse the same escaping and URL checks as Livewire-managed tables.
    const initialize = () => {
        const element = document.getElementById(@json('dataTable-' . $dataTableId));
        if (element) initDataTable($(element));
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
</script>
