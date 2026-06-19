<style>
    .btn-custom {
        padding-bottom: 10px;
        padding-top: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 800;
        overflow: hidden;
    }

    .btn-custom:hover {
        padding-top: 16px;
        color: var(--bs-btn-hover-color);
    }

    .btn.multicolor-border {
        position: relative;
        --red-end: 30%;
        --yellow-end: 50%;
    }

    .btn.multicolor-border::before {
        content: "";
        position: absolute;
        top: 0px;
        left: 50%;
        width: 100%;
        height: 0px;
        transform: translateX(-50%);
        /* border thickness */
        background: linear-gradient(to right,
                #dc3545 0%,
                #dc3545 var(--red-end),
                #ffc107 var(--red-end),
                #ffc107 var(--yellow-end),
                #0d6efd var(--yellow-end),
                #0d6efd 100%);
        border-top-left-radius: 50px;
        border-top-right-radius: 50px;
        transition: all 0.2s ease-in-out;
    }

    .btn.multicolor-border:hover:before {
        height: 6px;
    }
</style>
<button class="btn btn-custom btn-light multicolor-border hover-elevate-up">
    Submit
</button>
<button type="button" name="" id="" class="btn btn-light">
    Button
</button>
