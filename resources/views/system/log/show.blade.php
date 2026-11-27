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
                @if($security = $log->properties->get('security'))
                    <div class="col-6"><x-form.display label="Risk Score" :contents="$security['risk_score'] ?? 'Assessment unavailable'" /></div>
                    <div class="col-6"><x-form.display label="Risk Level" :contents="isset($security['risk_level']) ? ucfirst($security['risk_level']) : 'Assessment unavailable'" /></div>
                    <div class="col-6"><x-form.display label="Outcome" :contents="$security['outcome'] ?? 'Not recorded'" /></div>
                    <div class="col-6"><x-form.display label="Affected Account" :contents="($security['affected_account_name'] ?? 'Account').' (#'.($security['affected_account_id'] ?? 'unavailable').')'" /></div>
                    <div class="col-6"><x-form.display label="IP Address" :contents="$security['ip_address'] ?? 'Not recorded'" /></div>
                    <div class="col-6"><x-form.display label="User Agent" :contents="$security['user_agent'] ?? 'Not recorded'" /></div>
                    <div class="col-12">
                        <x-form.display label="Risk Reasons" :contents="($security['assessment'] ?? '') === 'unavailable' ? 'Assessment unavailable' : (collect($security['reasons'] ?? [])->pluck('reason')->implode('; ') ?: 'No suspicious signals detected.')" />
                    </div>
                @elseif(\App\Services\Auth\AuthenticationLog::isSecurity($log))
                    <div class="col-12"><x-form.display label="Risk Score" contents="Not assessed" /></div>
                @endif
                @foreach($log->properties->except('security') as $property => $value)
                    @if(is_array($value))
                        @foreach($value as $key => $item)
                            <div class="col-12 col-md-6">
                                <x-form.display :contents="is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $item"
                                    :label="ucwords(str_replace('_', ' ', (string) $key))" />
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 col-md-6">
                            <x-form.display :contents="$value" :label="ucwords(str_replace('_', ' ', $property))" />
                        </div>
                    @endif
                @endforeach
            </div>
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
