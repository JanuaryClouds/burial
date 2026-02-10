<div class="card multicolor-border shadow-sm bg-body">
    <div class="card-header">
        <h4 class="card-title fw-bold">Services Offered</h4>
    </div>
    <div class="card-body">
        <div id="services-carousel" class="carousel slide carousel-custom" data-bs-ride="carousel">
            <ol class="carousel-indicators">
                <li data-bs-target="#services-carousel" data-bs-slide-to="0" class="active" aria-current="true"
                    aria-label="Burial Assistance"></li>
                <li data-bs-target="#services-carousel" data-bs-slide-to="1" aria-label="Libreng Libing"></li>
            </ol>
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fs-2">Libreng Libing at Taguig City Public Cemetery</h5>
                                <p class="card-text fs-4">
                                    {{ $LibrengLibingDesc ??
                                        'Libreng Libing for deceased bonafide residents of Taguig City. Residents will be buried in Taguig City Recognized Public Cemeteries without any fee.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fs-2">Burial Assistances</h5>
                            <p class="card-text fs-4">
                                {{ $burialServiceDesc ??
                                    'Incentives to be given to families of the deceased to assist in the burial process of the beneficiary.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#services-carousel"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#services-carousel"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</div>
