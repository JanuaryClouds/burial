<div class="row">
	<div class="col-6 col-md-4 col-lg-3">
		<x-counter :count="$applicationsPerStatusCount['pending'] ?? 0"
			label="Pending"
			iconName="time"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<x-counter :count="$applicationsPerStatusCount['assessment'] ?? 0"
			label="Assessment"
			iconName="message-question"
			:iconPathsCount="3" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<x-counter :count="$applicationsPerStatusCount['processing'] ?? 0"
			label="Processing"
			iconName="file-up"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<x-counter :count="$applicationsPerStatusCount['cancelled'] ?? 0"
			label="Cancelled"
			iconName="flag"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<x-counter :count="$applicationsPerStatusCount['rejected'] ?? 0"
			label="Rejected"
			iconName="cross-square"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<x-counter :count="$applicationsPerStatusCount['referred'] ?? 0"
			label="Referred"
			iconName="double-right-arrow"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<x-counter :count="$applicationsPerStatusCount['closed'] ?? 0"
			label="Closed"
			iconName="check-circle"
			:iconPathsCount="2" />
	</div>
</div>
