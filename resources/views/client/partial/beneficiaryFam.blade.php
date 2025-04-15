<div x-data="familyForm()" x-init="initErrors(@js($errors->getMessages()))" class="space-y-6">
    <div class="flex items-center gap-4 mb-5 mt-7">
        <h1 class="text-lg font-semibold text-gray-800">III. Beneficiary Identifying Information</h1>
        <hr class="flex-grow border-t border-gray-300 mt-1">
        <button type="button" @click="addRow"
            class="mt-1 px-5 py-2 text-white bg-[#1A4798] rounded-lg hover:bg-[#F4C027] hover:text-white hover:border hover:border-[#F4C027] transition-colors">
            <i class="fa-solid fa-square-plus"></i> Add a row
        </button>
    </div>

    <template x-for="(row, index) in rows" :key="index">
        <div class="family-group space-y-4" :data-index="index">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">Name</label>
                    <input type="text" name="fam_name[]" x-model="row.name"
                        class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <template x-if="errors[`fam_name.${index}`]">
                        <p class="text-sm text-red-600 mt-1" x-text="errors[`fam_name.${index}`][0]"></p>
                    </template>
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Gender</label>
                    <select name="fam_sex_id[]" x-model="row.sex_id"
                        class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select gender --</option>
                        @foreach ($sexes as $gender)
                        <option value="{{ $gender->id }} {{ old('fam_sex_id') === $gender->id ? 'selected' : '' }}">
                            {{ $gender->name }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors[`fam_sex_id.${index}`]">
                        <p class="text-sm text-red-600 mt-1" x-text="errors[`fam_sex_id.${index}`][0]"></p>
                    </template>
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Age</label>
                    <input type="number" name="fam_age[]" x-model="row.age"
                        class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <template x-if="errors[`fam_age.${index}`]">
                        <p class="text-sm text-red-600 mt-1" x-text="errors[`fam_age.${index}`][0]"></p>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 relative">
                <div class="relative">
                    <label class="block font-medium text-gray-700">Civil status</label>
                    <select name="fam_civil_id[]" x-model="row.civil_id"
                        class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Civil status --</option>
                        @foreach($civils as $civil)
                        <option value="{{ $civil->id }}">{{ $civil->name }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors[`fam_civil_id.${index}`]">
                        <p class="text-sm text-red-600 mt-1" x-text="errors[`fam_civil_id.${index}`][0]"></p>
                    </template>
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Relationship</label>
                    <select name="fam_relationship_id[]" x-model="row.relationship_id"
                        class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Relationship --</option>
                        @foreach($relationships as $relationship)
                        <option value="{{ $relationship->id }}">{{ $relationship->name }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors[`fam_relationship_id.${index}`]">
                        <p class="text-sm text-red-600 mt-1" x-text="errors[`fam_relationship_id.${index}`][0]"></p>
                    </template>
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Occupation</label>
                    <input type="text" name="fam_occupation[]" x-model="row.occupation"
                        class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <template x-if="errors[`fam_occupation.${index}`]">
                        <p class="text-sm text-red-600 mt-1" x-text="errors[`fam_occupation.${index}`][0]"></p>
                    </template>
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Income</label>
                    <input type="text" name="fam_income[]" x-model="row.income"
                        class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <template x-if="errors[`fam_income.${index}`]">
                        <p class="text-sm text-red-600 mt-1" x-text="errors[`fam_income.${index}`][0]"></p>
                    </template>
                </div>
                <div class="absolute -top-2 right-0 mt-1">
                    <button type="button" @click="removeRow(index)"
                        class="bg-[#ff5147] text-white hover:bg-red-600 px-2 py-1 text-sm rounded-full transition">
                        <i class="fa-solid fa-xmark"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>