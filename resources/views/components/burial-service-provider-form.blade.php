<form action="" method="post" id="burialServiceProviderForm" class="flex flex-col items-center gap-12">
    @csrf
    @method('post')

    <div class="flex flex-col gap-4 bg-white shadow-lg rounded-md p-4 mx-36">
        <header class="flex justify-between">
            <span class="flex justify-between items-center gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="w-24 mr-2">
                <span class="flex flex-col gap-2">
                    <h3 class="text-4xl font-semibold">Burial Service Provider Form</h3>
                    <p class="text-lg text-gray-600">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <p class="text-gray-400 text-xs">All fields marked by (*) are required</p>
        <hr class="border-2 border-gray-700">
        <div class="flex flex-col gap-4">
            <h4 class="text-lg font-semibold">Details of the Provider</h4>
            <div class="flex justify-between items-start w-full gap-2">
                <span class="flex flex-col w-2/3 justify-between">
                    <input type="text" required name="provider_name" id="provider_name" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="provider_name" class="text-sm text-gray-400 text-center">Name of Company*</label>
                </span>
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" required name="provider_contact" id="provider_contact" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="provider_contact" class="text-sm text-gray-400 text-center">Contact Details*</label>
                </span>
            </div>
            <div class="flex justify-between items-start w-full gap-2">
                <span class="flex flex-col w-full justify-between">
                    <input type="text" required name="provider_address" id="provider_address" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="provider_address" class="text-sm text-gray-400 text-center">Address (Lot No., Street, Barangay, District)*</label>
                </span>
            </div>
        </div>
        <hr class="border-2 border-gray-700">
        <div class="flex flex-col gap-4">
            <h4 class="text-lg font-semibold">Remarks</h4>
            <div class="flex justify-between items-start w-full gap-2">
                <span class="flex flex-col w-full justify-between">
                    <textarea type="text" rows="4" name="provider_remarks" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    </textarea>
                </span>
            </div>
        </div>
    </div>
    <span class="flex justify-center items-center gap-4">
        <button type="submit" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-yellow-400 rounded-lg hover:bg-yellow-500 hover:text-black transition-colors">
            Submit
        </button>
        <button type="reset" class="px-4 py-2 text-black font-semibold tracking-widest uppercase text-sm bg-gray-100 rounded-lg hover:bg-gray-300 transition-colors">
            clear
        </button>
        <a href="{{ route('admin.burial.history') }}" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-gray-700 rounded-lg hover:bg-gray-300 hover:text-black transition-colors">
            Cancel
        </a>
    </span>
</form>