@if ($claimantChange != null && $claimantChange->status == 'pending')
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>
            <i class="fa fa-exclamation-triangle">
            </i>
            Requesting Claimant Change -
        </strong>
        A request to change the claimant is pending. Information regarding the new claimant is provided
        below. Updates are disabled until the request has been addressed.
    </div>
@endif
@if ($claimantChange != null && $claimantChange->status == 'rejected')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>
            <i class="fa fa-exclamation-triangle">
            </i>
            Rejected Claimant Change -
        </strong>
        The request to change claimants has been rejected.
        Updates are only provided for the original claimant.
    </div>
@endif
@if ($claimantChange != null && $claimantChange->status == 'approved')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>
            <i class="fa fa-exclamation-triangle">
            </i>
            Approved Claimant Change -
        </strong>
        This application has been approved to change claimants,
        updates are provided for the new claimant.
    </div>
@endif
