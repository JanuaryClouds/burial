<div class="d-flex flex-column">
    <h4 class="fs-1">Services Offered</h4>
    <div id="services-carousel" class="carousel slide carousel-custom" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <li data-bs-target="#services-carousel" data-bs-slide-to="0" class="active" aria-current="true"
                aria-label="Burial Assistance"></li>
            <li data-bs-target="#services-carousel" data-bs-slide-to="1" aria-label="Funeral Assistance"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fs-2">Funeral Assistances</h5>
                            <p class="card-text fs-4">
                                {{ $funeralServiceDesc ??
                                    'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quo nam earum possimus eligendi molestiae dolor aliquam. Impedit vel sit non voluptate natus facere nam eum at, tenetur nihil repellendus soluta.' }}
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
                                'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quo nam earum possimus eligendi molestiae dolor aliquam. Impedit vel sit non voluptate natus facere nam eum at, tenetur nihil repellendus soluta.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#services-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#services-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
