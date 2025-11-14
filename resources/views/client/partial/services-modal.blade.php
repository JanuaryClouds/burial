<div id="services-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="services-modal" aria-hidden="true">
    <form action="{{ route('clients.recommendation.store', ['id' => $client->id]) }}" method="post">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('client.partial.recommended-assistance')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>