@props(['type', 'startDate', 'endDate'])
<form action="{{ route('reports.' . $type . '.pdf', ['startDate' => $startDate, 'endDate' => $endDate]) }}" method="post" id="export-to-pdf-form" target="_blank">
    @csrf
    <button class="btn btn-primary" type="submit">
        <i class="fas fa-file-pdf"></i>
        Export all to PDF
    </button>
</form>
<script>
    const form = document.getElementById('export-to-pdf-form');

    // List all chart IDs you want to include in the PDF
    const chartIds = [
        'deceased-per-gender',
        'deceased-per-month',
        'deceased-per-religion',
        'deceased-per-barangay',
        'claimant-per-barangay',
        'claimant-per-relationship',
        'cheques-per-status',
    ];

    function getChartImage(chartId) {
        // Try instance first
        if (window.Chart && Chart.instances) {
            const instances = Object.values(Chart.instances || {});
            const inst = instances.find(i => i.canvas && i.canvas.id === chartId);
            if (inst) return inst.toBase64Image();
        }

        // Fallback to canvas DOM
        const canvas = document.getElementById(chartId);
        return canvas ? canvas.toDataURL() : null;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

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