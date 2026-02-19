<section class="section motion-ready tint-blue">
    <div class="container">
        <div class="card">
            <div class="card-accent-c">
                <span class="r"></span>
                <span class="y"></span>
                <span class="b"></span>
            </div>
            <div class="card-body">
                <h2 class="card-title">Application Process</h2>
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
