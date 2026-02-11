<section class="section-block motion-ready tint-blue">
    <div class="container-xxl">
        <div class="wrap">
            <div class="topbars-c">
                <span class="r"></span>
                <span class="y"></span>
                <span class="b"></span>
            </div>
            <div class="wrap-body-c">
                <h2 class="wrap-title wrap-title-c">Application Process</h2>
                <ol class="list-number">
                    @foreach ($steps as $step)
                        <li class="">
                            <strong>{{ $step['name'] }} - </strong>
                            {{ $step['description'] }}
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
