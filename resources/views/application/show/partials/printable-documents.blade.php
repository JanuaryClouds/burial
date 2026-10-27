<x-card>
    <x-slot:header>Printable Documents</x-slot:header>
    <div class="d-flex flex-wrap gap-3">
        <a href="{{ route('application.print', $application) }}" target="_blank" class="btn btn-sm btn-light">
            <x-icon.font-awesome class="fa-print" /> Print GIS Form
        </a>
        @if ($application->currentRecommendation())
            <a href="{{ route('application.certificate', $application) }}" target="_blank" class="btn btn-sm btn-light">
                <x-icon.font-awesome class="fa-certificate" /> Print Certificate
            </a>
        @endif
    </div>
    <h4 class="mt-6">Accessory Documents</h4>
    @if ($application->currentRecommendation())
        <div class="d-flex flex-wrap gap-3">
            @forelse ($application->activeRecommendation->funeralAssistanceType?->documentTemplates ?? [] as $template)
                <a href="{{ route('application.templates.download', [$application, $template]) }}"
                    class="btn btn-sm btn-primary" data-no-loader>
                    <x-icon.font-awesome class="fa-file-word" /> Download {{ $template->name }} (.docx)
                </a>
            @empty
                <p class="text-muted mb-0">No accessory documents are configured for this funeral assistance type.</p>
            @endforelse
        </div>
    @else
        <p class="text-muted mb-0">Accessory documents will be available after a recommendation is made.</p>
    @endif
    <div class="alert alert-warning mt-4 mb-0" role="note">
        Accessory documents are provided in Word (.docx) format so you can make last-minute edits before printing.
        The generated document's layout may differ from the uploaded template. Open the downloaded file in
        Microsoft Word, review its contents and layout, and make any necessary adjustments before printing.
    </div>
</x-card>
