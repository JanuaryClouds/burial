<div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>
    <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
        class="bg-white rounded-lg shadow-lg z-10 p-6 w-full max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Add new {{ $resource }}</h2>
            <button @click="showModal = false"
                class="text-gray-600 hover:text-gray-800 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route(Auth::user()->getRoleNames()->first() . '.' . $resource . '.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700">{{ $page_title }}:</label>
                <input type="text" id="name" name="name" placeholder="Enter name"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Remarks:</label>
                <input type="text" id="remarks" name="remarks" placeholder="Enter remarks"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>
            @if ($resource === 'barangay')
            <div class="mb-4">
                <label for="district_id" class="block text-gray-700">District:</label>
                <div class="relative">
                    <select id="district_id" name="district_id"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                        @foreach($subRecords as $material)
                        <option value="{{ $material->id }}">{{ $material->remarks }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586 
                         l3.293-3.293a1 1 0 011.414 1.414 
                         l-4 4a1 1 0 01-1.414 0 
                         l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </div>
                </div>
            </div>
            @endif
            <div class="flex justify-end">
                <button type="button" @click="showModal = false"
                    class="px-4 py-2 mr-2 text-black rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 text-white bg-gray-700 rounded-lg hover:bg-gray-300 hover:text-black transition-colors">Submit</button>
            </div>
        </form>
    </div>
</div>