<div class="row">
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="pending"
			iconName="time"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="assessment"
			iconName="message-question"
			:iconPathsCount="3" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="processing"
			iconName="file-up"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="releasing"
			iconName="book-open"
			:iconPathsCount="4" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="closed"
			iconName="check-circle"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="cancelled"
			iconName="archive-tick"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="rejected"
			iconName="cross-square"
			:iconPathsCount="2" />
	</div>
	<div class="col-6 col-md-4 col-lg-3">
		<livewire:application.statistics.status :applications="$applications"
			status="referred"
			iconName="double-right-arrow"
			:iconPathsCount="2" />
	</div>
</div>
