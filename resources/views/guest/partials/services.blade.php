<section id="services" class="section">
    <div class="container">
        <div class="section-head">
            <h2 class="h2">Services Offered</h2>
        </div>
        <div class="divider">
            <span class="r"></span>
            <span class="y"></span>
            <span class="b"></span>
        </div>
        <div class="row g-2">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <span class="ribbon">Libreng Libing</span>
                        <p>
                            {{ $LibrengLibingDesc ??
                                'Libreng Libing for deceased bonafide residents of Taguig City. Residents will be buried in Taguig City Recognized Public Cemeteries without any fee.' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="ribbon">Burial Assistance</span>
                        <p>
                            {{ $burialServiceDesc ??
                                'Incentives to be given to families of the deceased to assist in the burial process of the beneficiary.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
