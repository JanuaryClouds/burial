<div class="flex items-center gap-4 mb-5 mt-7">
    <h1 class="text-lg font-semibold text-gray-800">V. Recommended assistance</h1>
    <hr class="flex-grow border-t border-gray-300 mt-1">
</div>

<div x-data="{
        selected: @js(array_map('strval', old('rec_assistance_id', []))),
    }">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($assistances as $assistance)
        <label class="flex items-center space-x-3">
            <input type="checkbox" name="rec_assistance_id[]" value="{{ $assistance->id }}"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" x-model="selected">
            <span class="text-gray-700">{{ $assistance->name }}</span>
        </label>
        @endforeach
    </div>

    <!-- Section shown if Burial (id = 8) is selected -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4 mt-4" x-show="selected.includes('8')" x-cloak>
        <div>
            <label for="rec_burial_referral" class="block font-sm text-gray-700">Referral</label>
            <input type="text" name="rec_burial_referral[]" id="rec_burial_referral"
                value="{{ old('rec_burial_referral.0') }}"
                class="mt-2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter referral details for Burial assistance">
            @error('rec_burial_referral')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="rec_moa" class="block font-sm text-gray-700">Mode of financial assistance</label>
            <select name="rec_moa[]" id="rec_moa"
                class="mt-2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach ($moas as $moa)
                <option value="{{ $moa->id }}" {{ old('rec_moa.0') === $moa->id ? 'selected' : '' }}>{{ $moa->name }}
                </option>
                @endforeach
            </select>
            @error('rec_moa')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="rec_amount" class="block font-sm text-gray-700">Amount of assistance</label>
            <input type="text" name="rec_amount[]" id="rec_amount" value="{{ old('rec_amount.0') }}"
                class="mt-2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('rec_amount')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Section shown if Others (id = 14) is selected -->
    <div class="mt-4" x-show="selected.includes('14')" x-cloak>
        <label for="rec_assistance_other" class="block font-sm text-gray-700">Please specify "Others"</label>
        <input type="text" name="rec_assistance_other[]" id="rec_assistance_other"
            value="{{ old('rec_assistance_other.0') }}"
            class="mt-2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Specify the assistance type">
        @error('rec_assistance_other')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
