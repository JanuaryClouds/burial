<div x-data="assessmentForm()" x-init="initErrors(@js($errors->getMessages()))" class="space-y-6">
    <div class="flex items-center gap-4 mb-5 mt-7">
        <h1 class="text-lg font-semibold text-gray-800">IV. Assessment</h1>
        <hr class="flex-grow border-t border-gray-300 mt-1">
        <button type="button" @click="addAssessment"
            class="mt-1 px-5 py-2 text-white bg-[#1A4798] rounded-lg hover:bg-[#F4C027] hover:text-white hover:border hover:border-[#F4C027] transition-colors">
            <i class="fa-solid fa-square-plus"></i> Add a row
        </button>
    </div>

    <template x-for="(row, index) in assessments" :key="index">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative">
            <div>
                <label class="block font-medium text-gray-700">Problem Presented</label>
                <input type="text" name="ass_problem_presented[]" x-model="row.problem_presented"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <template x-if="errors[`ass_problem_presented.${index}`]">
                    <p class="text-sm text-red-600 mt-1" x-text="errors[`ass_problem_presented.${index}`][0]"></p>
                </template>
            </div>

            <div class="relative">
                <label class="block font-medium text-gray-700">Social Worker's Assessment</label>
                <input type="text" name="ass_assessment[]" x-model="row.assessment"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <template x-if="errors[`ass_assessment.${index}`]">
                    <p class="text-sm text-red-600 mt-1" x-text="errors[`ass_assessment.${index}`][0]"></p>
                </template>

                <div class="absolute -top-2 right-0 mt-1">
                    <button type="button" @click="removeAssessment(index)"
                        class="bg-[#ff5147] text-white hover:bg-red-600 px-2 py-1 text-sm rounded-full transition">
                        <i class="fa-solid fa-xmark"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>