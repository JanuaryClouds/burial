<div class="flex items-center gap-4 mb-5 mt-7">
    <h1 class="text-lg font-semibold text-gray-800">V. Recommended assistance</h1>
    <hr class="flex-grow border-t border-gray-300 mt-1">
</div>

<div x-data="{
        selected: @js(old('assistance_ids', [])),
    }" class="grid grid-cols-1 md:grid-cols-2 gap-4">

    @foreach ($assistances as $assistance)
    <label class="flex items-center space-x-3">
        <input type="checkbox" name="assistance_ids[]" value="{{ $assistance->id }}"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" x-model="selected">
        <span class="text-gray-700">{{ $assistance->name }}</span>
    </label>

    @if (strtolower($assistance->name) === 'burial')
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4"
        x-show="selected.includes('{{ $assistance->id }}')" x-cloak>
        <div class="">
            <label for="rec_burial_referral" class="block font-sm text-gray-700">Referral</label>
            <input type="text" name="rec_burial_referral" id="rec_burial_referral" value="{{ old('burial_referral') }}"
                class="mt-2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter referral details for Burial assistance">
        </div>
        <div>
            <label for="rec_amount" class="block font-sm text-gray-700">Amount of assistance</label>
            <input type="text" name="rec_amount" id="rec_amount"
                class="mt-2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="rec_moa" class="block font-sm text-gray-700">Mode of financial assistance</label>
            <select name="rec_moa" id="rec_moa"
                class="mt-2 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach ($moas as $moa)
                <option value="{{ $moa->id }}">{{ $moa->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif
    @endforeach
</div>