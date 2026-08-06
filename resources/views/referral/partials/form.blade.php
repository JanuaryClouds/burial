<form id="referral-form" action="{{ route('referral.store', $application) }}" method="POST">
    @csrf
    @method('POST')
    <x-form-input name="referral_to" label="Referral To" :required="true" />
</form>
