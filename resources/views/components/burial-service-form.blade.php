<form action="" method="post" class="flex flex-col items-center gap-12">
    @csrf
    @method('post')

    <div class="flex flex-col gap-4 bg-white shadow-lg rounded-md p-4 mx-36">
        <header class="flex justify-between">
            <span class="flex justify-between items-center gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="w-24 mr-2">
                <span class="flex flex-col gap-2">
                    <h3 class="text-4xl font-semibold">Burial Service Form</h3>
                    <p class="text-lg text-gray-600">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <hr class="border-2 border-gray-700">

        <div class="flex flex-col gap-4">
            <h4 class="text-lg font-semibold">Details of the Deceased</h4>
            <div class="flex justify-between items-start w-full gap-2">
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" name="deceased_firstname" id="deceased_lastname" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="deceased_name" class="text-sm text-gray-400 text-center">Last Name</label>
                </span>
                <span class="flex flex-col w-2/3 justify-between">
                    <input type="text" name="deceased_lastname" id="deceased_firstname" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="deceased_name" class="text-sm text-gray-400 text-center">First Name</label>
                </span>
            </div>
            <h4 class="text-lg font-semibold">Representative / Contact Person</h4>
            <div class="flex justify-start items-center w-full gap-2">
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" name="representative_name" id="representative_name" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="representative_name" class="text-sm text-gray-400 text-center">Full Name</label>
                </span>
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" name="representative_contact" id="representative_contact" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="representative_contact" class="text-sm text-gray-400 text-center">Contact Details</label>
                </span>

                <!-- NOTE: Can be a dropdown menu -->
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" name="rep_relationship" id="rep_relationship" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="rep_relationship" class="text-sm text-gray-400 text-center">Relationship to the Deceased</label>
                </span>
            </div>
            <hr class="border-2 border-dashed border-gray-700">

            
            <div class="flex flex-col gap-2">
                <h4 class="text-lg font-semibold">Burial Service Details</h4>
                <div class="flex justify-between items-center w-full gap-2">
                    <span class="flex flex-col w-3/5 justify-between">
                        <input type="text" name="burial_address" id="burial_address" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        <label for="burial_address" class="text-sm text-gray-400 text-center">Address of Burial (House No., Street, Barangay, District)</label>
                    </span>
                    <span class="flex flex-col justify-between w-1/5">
                        <input type="date" name="start_of_burial" id="start_of_burial" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        <label for="start_of_burial" class="text-sm text-gray-400 text-center">Start Date of Burial</label>
                    </span>
                    <span class="flex flex-col justify-between w-1/5">
                        <input type="date" name="end_of_burial" id="end_of_burial" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        <label for="end_of_burial" class="text-sm text-gray-400 text-center">End Date of Burial</label>
                    </span>
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <span class="flex flex-col justify-between w-4/5">
                    <select name="funeral_service" id="funeral_service" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        <option value="">Sample 1</option>
                        <option value="">Sample 2</option>
                        <option value="">Sample 3</option>
                    </select>
                    <label for="funeral_service" class="text-sm text-gray-400 text-center">Funeral Service</label>
                </span>
                <span class="flex flex-col w-1/5 justify-between">
                    <input
                        type="text"
                        name="collected_funds"
                        id="collected_funds"
                        value="₱"
                        oninput="this.value = this.value.replace(/[^₱\d.]/g, '').replace(/^([^₱])/, '₱$1')"
                        class="w-full border-2 border-gray-300 rounded-md px-2 py-1"
                        />
                    <label for="collected_funds" class="text-sm text-gray-400 text-center">Collected Funds</label>
                </span>
            </div>
        </div>

        <hr class="border-2 border-dashed border-gray-700">
        <div class="flex flex-col gap-2">
            <h4 class="text-lg font-semibold">Images</h4>
            <input 
                type="file" 
                name="images[]" 
                id="images"
                accept="image/*"
                multiple
                class="w-full border-2 border-gray-300 rounded-md px-2 py-1"
                >
        </div>
    </div>

    <span class="flex justify-center items-center gap-4">
        <button type="submit" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-yellow-400 rounded-lg hover:bg-yellow-500 hover:text-black transition-colors">
            Save
        </button>
        <button type="reset" class="px-4 py-2 text-black font-semibold tracking-widest uppercase text-sm bg-gray-100 rounded-lg hover:bg-gray-300 transition-colors">
            clear
        </button>
        <a href="{{ route('admin.burial.history') }}" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-gray-700 rounded-lg hover:bg-gray-300 hover:text-black transition-colors">
            Cancel
        </a>
    </span>
</form>