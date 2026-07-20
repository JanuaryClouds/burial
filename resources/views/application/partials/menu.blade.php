<div class="card">
	<div class="card-body">
		<div class="row g-1">
			<div class="col-6 col-lg-auto">
				<x-menu-action label="Print GIS Form"
					icon="fa-solid fa-print"
					:permission="true"
					:enabled_when="true">
					<a href="{{ route('application.print', $application) }}"
						class="btn btn-light w-100 w-lg-auto"
						target="_blank">
						<i class="fa-solid fa-print"></i>
						Print GIS Form
					</a>
				</x-menu-action>
			</div>
			<div class="col-6 col-lg-auto">
				<x-menu-action label="Print Certificate"
					icon="fa-solid fa-certificate"
					:permission="auth()->user()->can('create-certificates')"
					:enabled_when="true">
					<a href="{{ route('application.certificate', $application) }}"
						class="btn btn-light w-100 w-lg-auto"
						target="_blank">
						<i class="fa-solid fa-certificate"></i>
						Print Certificate
					</a>
				</x-menu-action>
			</div>
			<div class="col-6 col-lg-auto">
				<x-menu-action label="Schedule an Interview"
					icon="fa-solid fa-calendar-plus"
					:enabled_when="$recommendations->isEmpty() && !$referral"
					:permission="auth()
					    ->user()
					    ->can('create', [App\Models\Interview::class, $application])"
					disabled_message="Application has already been assessed or referred">
					<x-modal modalId="set-schedule-modal"
						buttonClass="btn-primary"
						modalTitle="Schedule an Interview"
						modalSize="md">
						<x-slot:triggerButton>
							<i class="fa-solid fa-calendar-plus"></i>
							Schedule an Interview
						</x-slot:triggerButton>
						@include('interview.partials.form')
						<x-slot:footer>
							<button type="button"
								class="btn btn-secondary"
								data-bs-dismiss="modal">
								<i class="fa-solid fa-xmark"></i>
								Cancel
							</button>
							<button type="submit"
								form="schedule-interview-form"
								class="btn btn-success">
								<i class="fa-solid fa-check"></i>
								Submit
							</button>
						</x-slot:footer>
					</x-modal>
				</x-menu-action>
			</div>
			<div class="col-6 col-lg-auto">
				<x-menu-action label="Write an Assessment"
					icon="fa-solid fa-pen-to-square"
					:enabled_when="$interviews->isNotEmpty() && !$assessment && $recommendations->isEmpty() && !$referral"
					:permission="auth()
					    ->user()
					    ->can('create', [App\Models\Assessment::class, $application])"
					:disabled_message="'Application already has been assessed'">
					<x-modal modalId="assessment-modal"
						buttonClass="btn-primary"
						modalTitle="Write an Assessment"
						modalSize="md">
						<x-slot:triggerButton>
							<i class="fa-solid fa-pen-to-square"></i>
							Write an Assessment
						</x-slot:triggerButton>
						@include('assessment.partials.form')
						<x-slot:footer>
							<button type="button"
								class="btn btn-secondary"
								data-bs-dismiss="modal">
								<i class="fa-solid fa-xmark"></i>
								Cancel
							</button>
							<button type="submit"
								form="assessment-form"
								class="btn btn-success">
								<i class="fa-solid fa-check"></i>
								Submit
							</button>
						</x-slot:footer>
					</x-modal>
				</x-menu-action>
			</div>
			<div class="col-6 col-lg-auto">
				<x-menu-action label="Recommend an Assistance"
					icon="fa-solid fa-check-to-slot"
					:enabled_when="$assessment && $recommendations->isEmpty() && !$referral"
					:permission="auth()
					    ->user()
					    ->can('create', [App\Models\Recommendation::class, $application])"
					:disabled_message="$recommendations->isNotEmpty()
					    ? 'Application already has been recommended a service'
					    : 'Application already has been referred'">
					<x-modal modalId="recommendation-modal"
						buttonClass="btn-success"
						modalTitle="Recommend an Assistance"
						modalSize="md">
						<x-slot:triggerButton>
							<i class="fa-solid fa-check-to-slot"></i>
							Recommend an Assistance
						</x-slot:triggerButton>
						@include('recommendation.partials.form')
						<x-slot:footer>
							<button type="button"
								class="btn btn-secondary"
								data-bs-dismiss="modal">
								<i class="fa-solid fa-xmark"></i>
								Cancel
							</button>
							<button type="submit"
								form="recommendation-form"
								class="btn btn-success">
								<i class="fa-solid fa-check"></i>
								Submit
							</button>
						</x-slot:footer>
					</x-modal>
				</x-menu-action>
			</div>
			<div class="col-6 col-lg-auto">
				<x-menu-action label="Referral"
					icon="fa-solid fa-forward"
					:enabled_when="$assessment && $recommendations->isEmpty() && !$referral"
					:permission="auth()
					    ->user()
					    ->can('create', [App\Models\Referral::class, $application])"
					:disabled_message="$referral
					    ? 'Application already has been referred'
					    : 'Application already has been recommended a service'">
					<x-modal modalId="referral-modal"
						buttonClass="btn-success"
						modalTitle="Referral an Assistance"
						modalSize="md">
						<x-slot:triggerButton>
							<i class="fa-solid fa-forward"></i>
							Referral
						</x-slot:triggerButton>
						@include('referral.partials.form')
						<x-slot:footer>
							<button type="button"
								class="btn btn-secondary"
								data-bs-dismiss="modal">
								<i class="fa-solid fa-xmark"></i>
								Cancel
							</button>
							<button type="submit"
								form="referral-form"
								class="btn btn-success">
								<i class="fa-solid fa-check"></i>
								Submit
							</button>
						</x-slot:footer>
					</x-modal>
				</x-menu-action>
			</div>
		</div>
	</div>
</div>
