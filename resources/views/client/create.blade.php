@extends('layouts.dashboard')
@section('content')

@section('breadcrumb')
<x-breadcrumb :items="[
        ['label' => 'Client', 'url' => route(Auth::user()->getRoleNames()->first() . '.' . $resource . '.index')],
        ['label' => $page_title],
    ]" />
@endsection

<div class="flex justify-between mb-5 overflow-auto">
    <h1 class="text-3xl font-bold mb-2 text-center text-gray-800">{{ $page_title }}</h1>
    <a href="{{ route(Auth::user()->getRoleNames()->first() . '.client.create') }}">
        <button @click="showModal = true"
            class="px-5 py-2 text-white bg-[#1A4798] rounded-lg hover:bg-[#F4C027] hover:text-black hover:border border-[#F4C027] transition-colors">
            <i class="fa-solid fa-plus"></i> Add {{ $resource }}
        </button>
    </a>
</div>

<div class="w-full bg-white p-8 rounded-lg shadow-lg border border-gray-200">
    <form method="POST" action="{{ route(Auth::user()->getRoleNames()->first() . '.client.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:grid-cols-4">
            <div>
                <label class="block font-medium text-gray-700">Tracking Number</label>
                <input type="text" name="tracking_no" id="tracking_no" value="{{ old('tracking_no') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('tracking_no')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
            <div>
                <label class="block font-medium text-gray-700">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('first_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Middle Name</label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('middle_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('last_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Gender</label>
                <select name="sex_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select gender --</option>
                    @foreach($sexes as $gender)
                    <option value="{{ $gender->id }}" {{ old('sex_id') == $gender->id ? 'selected' : '' }}>
                        {{ $gender->name }}</option>
                    @endforeach
                </select>
                @error('sex_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mt-2">
            <div>
                <label class="block font-medium text-gray-700">House No.</label>
                <input type="text" name="house_no" value="{{ old('house_no') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('house_no')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Street</label>
                <input type="text" name="street" value="{{ old('street') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('street')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">District</label>
                <select name="district_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select District --</option>
                    @foreach($districts as $district)
                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}</option>
                    @endforeach
                </select>
                @error('district_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Barangay</label>
                <select name="barangay_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select Barangay --</option>
                    @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>
                        {{ $barangay->name }}</option>
                    @endforeach
                </select>
                @error('barangay_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">City</label>
                <input type="text" name="city" value="{{ old('city', 'Taguig city') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    readonly required>
                @error('city')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mt-2">
            <div>
                <label class="block font-medium text-gray-700">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('date_of_birth')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Age</label>
                <input type="number" name="age" id="age" value="{{ old('age') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    readonly required>
                @error('age')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Relationship</label>
                <select name="relationship_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select Relationship --</option>
                    @foreach($relationships as $relationship)
                    <option value="{{ $relationship->id }}">
                        {{ old('barangay_id') == $relationship->name ? 'selected' : $relationship->name }}</option>
                    @endforeach
                </select>
                @error('barangay_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Civil status</label>
                <select name="civil_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select Civil status --</option>
                    @foreach($civils as $civil)
                    <option value="{{ $civil->id }}">{{ old('civil_id') == $civil->name ? 'selected' : $civil->name }}
                    </option>
                    @endforeach
                </select>
                @error('civil_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Nationality</label>
                <select name="nationality_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select Nationality --</option>
                    @foreach($nationalities as $nationality)
                    <option value="{{ $nationality->id }}">
                        {{ old('nationality_id') == $nationality->name ? 'selected' : $nationality->name }}</option>
                    @endforeach
                </select>
                @error('nationality_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mt-2">
            <div>
                <label class="block font-medium text-gray-700">Religion</label>
                <select name="religion_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select Religion --</option>
                    @foreach($religions as $religion)
                    <option value="{{ $religion->id }}">
                        {{ old('religion_id') == $religion->name ? 'selected' : $religion->name }}
                    </option>
                    @endforeach
                </select>
                @error('religion_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Educational Attainment</label>
                <select name="education_id"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="">-- Select Educational Attainment --</option>
                    @foreach($educations as $education)
                    <option value="{{ $education->id }}">
                        {{ old('education_id') == $education->name ? 'selected' : $education->name }}
                    </option>
                    @endforeach
                </select>
                @error('education_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Skill/Occupation</label>
                <input type="text" name="skill" value="{{ old('skill') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('skill')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Monthly income</label>
                <input type="text" name="income" value="{{ old('income') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('income')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium text-gray-700">Philhealth no.</label>
                <input type="text" name="philhealth" value="{{ old('philhealth') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('philhealth')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700">Contact Number</label>
                <input type="text" name="contact_no" value="{{ old('contact_no') }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('contact_no')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 text-right">
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
</script>
@endsection