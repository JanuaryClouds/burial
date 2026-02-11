<section id="requirements" class="section-block tint-grey motion-ready">
    <div class="container">
        <div class="wrap">
            <div class="topbars-b">
                <span class="r"></span>
                <span class="y"></span>
                <span class="b"></span>
            </div>
            <div class="wrap-body-b">
                <h2 class="wrap-title">Required Documents</h2>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="box panel">
                            <span class="ribbon">For Burial Assistance</span>
                            <ol class="list-check">
                                @foreach ($burialDocuments as $document)
                                    <li class="">
                                        {{ $document['name'] }}
                                        {{ $document['is_muslim'] ? '(For Deceased Muslim Citizens)' : '' }}
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="box panel">
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
                <div class="row">
                    <div class="col">
                        <div class="box panel">
                            <span class="ribbon">Convenient to Prepare</span>
                            <ol class="list-check">
                                <li class="">
                                    Black Ballpoint Pen
                                </li>
                                <li class="">
                                    Active Mobile Phone Number
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
                <div class="panel-flush">
                    <strong>Examples of Proofs of Relationship</strong>
                    <ul class="list-bullet">
                        <li><strong>Marriage Contract (Spouse)</strong> - From Local Civil Registry</li>
                        <li><strong>Birth Certificate</strong> - From Local Civil Registry</li>
                        <li><strong>Baptismal Certificate</strong> (for Siblings/Children/Parent) - Church where the
                            deceased was baptized</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
