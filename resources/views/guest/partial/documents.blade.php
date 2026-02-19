<section id="requirements" class="section tint-grey motion-ready">
    <div class="container">
        <div class="card">
            <div class="card-accent-b">
                <span class="r"></span>
                <span class="y"></span>
                <span class="b"></span>
            </div>
            <div class="card-body-dashed">
                <h2 class="card-title">Required Documents</h2>
                <div class="row g-2">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <span class="ribbon">For Burial Assistance</span>
                                <ol class="list-check">
                                    @foreach ($burialDocuments as $document)
                                        @if (!$document['is_muslim'])
                                            <li class="">
                                                {{ $document['name'] }}
                                                (For Deceased Muslim Citizens Only)
                                            </li>
                                        @endif
                                    @endforeach
                                </ol>
                                <div class="callout-highlight">
                                    <strong>For Deceased Muslim Citizens, also bring:</strong>
                                    <ol class="list-check">
                                        @foreach ($burialDocuments as $document)
                                            @if ($document['is_muslim'])
                                                <li class="">
                                                    {{ $document['name'] }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <span class="ribbon">For Libreng Libing</span>
                                <ol class="list-check">
                                    @foreach ($funeralDocuments as $document)
                                        <li class="">
                                            {{ $document['name'] }}
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="callout">
                    <span class="ribbon">Examples of Proofs of Relationship</span>
                    <ul class="list-bullet">
                        <li><strong>Marriage Contract (Spouse)</strong> - From Local Civil Registry</li>
                        <li><strong>Birth Certificate</strong> - From Local Civil Registry</li>
                        <li><strong>Baptismal Certificate</strong> (for Siblings/Children/Parent) - Church where the
                            deceased was baptized</li>
                    </ul>
                </div>
                <div class="callout">
                    <span class="ribbon">Convenient to Prepare</span>
                    <ol class="list-check">
                        <li class="">
                            Black Ballpoint Pen
                        </li>
                        <li class="">
                            Brown Envelope
                        </li>
                        <li class="">
                            Your prescripted reading glasses
                        </li>
                        <li class="">
                            Drinking Water
                        </li>
                        <li class="">
                            Vacant time allotted for interview
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>
