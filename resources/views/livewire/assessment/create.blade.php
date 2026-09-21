<x-slot:pageTitle>
	{{ $application->tracking_no }} | Create Assessment
</x-slot:pageTitle>
<x-slot:pageSubTitle>
	Funeral Assistance System | CSWDO Taguig
</x-slot:pageSubTitle>

<div class="d-flex flex-column gap-6">
	<div class="row">
		<div class="col-12 col-lg-8">
			{{-- Application Summary to help the social worker write the assessment --}}
			<x-card>
				<x-slot:header>Application Summary</x-slot:header>
				<livewire:application.summary :application="$application"
					defer />
				<x-slot:footer>
					<a href="{{ route('application.show', $application) }}"
						class="btn btn-sm btn-light">
						<x-icon.font-awesome class="fa-up-right-from-square" />
						Show Application
					</a>
					<a href="{{ route('client.show', $application->client) }}"
						class="btn btn-sm btn-light">
						<x-icon.font-awesome class="fa-up-right-from-square" />
						Show Client
					</a>
					<a href="{{ route('beneficiary.show', $application->beneficiary) }}"
						class="btn btn-sm btn-light">
						<x-icon.font-awesome class="fa-up-right-from-square" />
						Show Beneficiary
					</a>
				</x-slot:footer>
			</x-card>
		</div>
		<div class="col-12 col-lg-4 d-flex flex-column gap-4">
			<x-card>
				<x-slot:header>Assessment</x-slot:header>
				<x-form.textarea name="form.problem_presented"
					wire:model="form.problem_presented"
					label="Problem Presented"
					required />
				<x-form.textarea name="form.swa"
					wire:model="form.swa"
					label="Social Worker's Assessment"
					required />
				<x-slot:footer>
					<x-button wire:click='save'
						class="btn-sm btn-success">
						<x-icon.font-awesome class="fa-floppy-disk" />
						Save
					</x-button>
				</x-slot:footer>
			</x-card>
			<x-card>
				<x-slot:header>Scheduled Interviews</x-slot:header>
				<livewire:interview.index :client="$application->client"
					defer />
			</x-card>
		</div>
	</div>
</div>
