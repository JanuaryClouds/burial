@extends('layouts.dashboard')
@section('content')

@section('breadcrumb')
<x-breadcrumb :items="[
        ['label' => 'Client', 'url' => route(Auth::user()->getRoleNames()->first() . '.' . $resource . '.index')],
        ['label' => $page_title],
    ]" />
@endsection

<div class="flex justify-between mb-3 overflow-auto">
    <h1 class="text-3xl font-bold mb-2 text-center text-gray-800">{{ $page_title }}</h1>
</div>

<div class="w-full bg-white p-8 rounded-lg shadow-lg border border-gray-200 overflow-auto max-h-[75vh]">
    <p class="text-sm text-red-600 mb-3"><i class="fa-solid fa-asterisk"></i> Put N/A if not applicable</p>
    <form method="POST" action="{{ route(Auth::user()->getRoleNames()->first() . '.client.store') }}">
        @csrf
        @include('client.partial.clientInfo')
        @include('client.partial.beneficiaryInfo')
        @include('client.partial.beneficiaryFam')
        @include('client.partial.recommendedAssistance')

        <div class="mt-6 text-right mb-6">
            <button type="submit"
                class="px-5 py-2 text-white bg-[#1A4798] rounded-lg hover:bg-[#F4C027] hover:text-black hover:border border-[#F4C027] transition-colors">
                Submit
            </button>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("client.latest-tracking") }}')
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('tracking_no');
            input.value = data.tracking_no;
        });
});

document.addEventListener('DOMContentLoaded', function() {
    const dobInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');

    dobInput.addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        ageInput.value = isNaN(age) ? '' : age;
    });
});


function familyForm() {
    return {
        rows: [{
            name: '',
            sex_id: '',
            age: '',
            civil_id: '',
            relationship_id: '',
            occupation: '',
            income: ''
        }],
        errors: {},
        initErrors(serverErrors) {
            this.errors = serverErrors || {};
        },
        addRow() {
            this.rows.push({
                name: '',
                sex_id: '',
                age: '',
                civil_id: '',
                relationship_id: '',
                occupation: '',
                income: ''
            });
        },
        removeRow(index) {
            this.rows.splice(index, 1);
        }
    }
}
</script>
@endsection