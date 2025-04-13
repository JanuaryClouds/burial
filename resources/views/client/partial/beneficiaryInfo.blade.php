<div class="flex items-center gap-4 mb-5 mt-7">
    <h1 class="text-lg font-semibold text-gray-800">Beneficiary Identifying Information</h1>
    <hr class="flex-grow border-t border-gray-300 mt-1">
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
    <div>
        <label for="ben_first_name" class="block font-medium text-gray-700">First name</label>
        <input type="text" name="ben_first_name" id="ben_first_name" value="{{ old('ben_first_name') }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-non focus:ring-2 focus:ring-blue-500">
        @error('ben_first_name')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="ben_middle_name" class="block font-medium text-gray-700">Middle name</label>
        <input type="text" name="ben_middle_name" id="ben_middle_name" value="{{ old('ben_middle_name') }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-non focus:ring-2 focus:ring-blue-500">
        @error('ben_middle_name')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="ben_last_name" class="block font-medium text-gray-700">Last name</label>
        <input type="text" name="ben_last_name" id="ben_last_name" value="{{ old('ben_last_name') }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-non focus:ring-2 focus:ring-blue-500">
        @error('ben_last_name')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
    <div>
        <label class="block font-medium text-gray-700">Gender</label>
        <select name="ben_sex_id"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
            <option value="">-- Select gender --</option>
            @foreach($sexes as $gender)
            <option value="{{ $gender->id }}" {{ old('ben_sex_id') == $gender->id ? 'selected' : $gender->id }}>
                {{ $gender->name }}</option>
            @endforeach
        </select>
        @error('ben_sex_id')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block font-medium text-gray-700">Date of Birth</label>
        <input type="date" name="ben_date_of_birth" id="ben_date_of_birth" value="{{ old('ben_date_of_birth') }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('ben_date_of_birth')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block font-medium text-gray-700">Date of Birth</label>
        <input type="text" name="ben_place_of_birth" id="ben_place_of_birth" value="{{ old('ben_place_of_birth') }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('ben_place_of_birth')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>