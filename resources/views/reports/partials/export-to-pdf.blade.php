@props(['type', 'startDate', 'endDate'])
<form action="{{ route('reports.' . $type . '.pdf', ['startDate' => $startDate, 'endDate' => $endDate]) }}" method="post"
    id="export-to-pdf-form" target="_blank" data-no-loader>
    @csrf
    <button class="btn btn-primary" type="submit">
        <i class="fas fa-file-pdf"></i>
        Export all to PDF
    </button>
</form>
<script nonce="{{ $nonce ?? '' }}">
    const form = document.getElementById('export-to-pdf-form');

    // List all chart IDs you want to include in the PDF
    const chartIds = [
        'beneficiary-per-gender',
        'beneficiary-per-month',
        'beneficiary-per-religion',
        'beneficiary-per-barangay',
        'claimant-per-barangay',
        'claimant-per-relationship',
        'cheques-per-status',
        'funerals-per-status',
        'clients-per-barangay',
        'clients-per-assistance',
    ];

    function getChartImage(chartId) {
        try {
            // Try instance first
            if (window.Chart && Chart.instances) {
                const instances = Object.values(Chart.instances || {});
                const inst = instances.find(i => i.canvas && i.canvas.id === chartId);
                if (inst) return inst.toBase64Image();
            }

            // Fallback to canvas DOM
            const canvas = document.getElementById(chartId);
            return canvas ? canvas.toDataURL() : null;
        } catch (e) {
            console.warn(`Failed to get image for chart: ${chartId}`, e);
            return null;
        }
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Remove any previously added chart inputs
        form.querySelectorAll('input[name^="charts["]').forEach(input => input.remove());

        // Loop through chart IDs
        chartIds.forEach(id => {
            const chartImage = getChartImage(id);

            if (chartImage) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `charts[${id}]`;
                input.value = chartImage;
                form.appendChild(input);
            }
        });
        form.submit();
    });
</script>
