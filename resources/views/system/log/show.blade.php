@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<div class="row">
				<div class="col-6">
					<x-form.display :contents="ucwords(str_replace('_', ' ', $log->log_name))"
						label="Action" />
				</div>
				<div class="col-6">
					<x-form.display :contents="$log->causer ? $log->causer->fullname().' (#'.$log->causer_id.')' : ($log->causer_id ? 'Account #'.$log->causer_id.' (unavailable)' : 'Not recorded')"
						label="Done By" />
				</div>

                @if($log->log_name === 'account-management' && $log->subject_type === (new \App\Models\User)->getMorphClass() && $log->subject_id)
                <div class="col-12">
                    <x-form.display label="Affected Account"
                        :contents="$log->subject ? $log->subject->fullname().' (#'.$log->subject_id.')' : 'Account #'.$log->subject_id.' (unavailable)'" />
                </div>
                @endif
				<div class="col-12">
					<x-form.display :contents="$log->description"
						label="Description" />
				</div>
				@foreach ($log->properties as $property => $value)
					@if (is_array($value))
						@foreach ($value as $key => $val)
							<div class="col-12 col-md-6">
								<x-form.display :contents="$val"
									label="{{ ucwords(str_replace('_', ' ', $key)) }}" />
							</div>
						@endforeach
					@else
						<div class="col-12 col-md-6">
							<x-form.display :contents="$value"
								label="{{ ucwords(str_replace('_', ' ', $property)) }}" />
					@endif
			</div>
			@endforeach
			<x-slot:footer>
				<a href="{{ route('system.logs.index') }}"
					class="btn btn-sm btn-light">
					<x-icon.font-awesome class="fa-external-link" />
					Back
				</a>
			</x-slot:footer>
		</x-card>
	</div>
@endsection
