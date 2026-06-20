@extends('layouts.app')

@section('title', 'Register Your Phone Shop')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Register Your Phone Shop</h1>
            <p class="text-gray-600">Join M-right Digital Receipt platform and start generating professional receipts for your customers</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="{{ route('shops.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Business Information Section -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">
                        Business Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Shop Name -->
                        <div>
                            <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Shop/Business Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="shop_name" 
                                   name="shop_name" 
                                   value="{{ old('shop_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('shop_name') border-red-500 @enderror"
                                   placeholder="ABC Phones and Accessories"
                                   required>
                            @error('shop_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Owner Full Name -->
                        <div>
                            <label for="owner_full_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Owner Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="owner_full_name" 
                                   name="owner_full_name" 
                                   value="{{ old('owner_full_name', Auth::user()->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('owner_full_name') border-red-500 @enderror"
                                   required>
                            @error('owner_full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Email -->
                        <div>
                            <label for="business_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="business_email" 
                                   name="business_email" 
                                   value="{{ old('business_email', Auth::user()->email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('business_email') border-red-500 @enderror"
                                   required>
                            @error('business_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Phone 1 -->
                        <div>
                            <label for="business_phone_1" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Phone 1 <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="business_phone_1" 
                                   name="business_phone_1" 
                                   value="{{ old('business_phone_1') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('business_phone_1') border-red-500 @enderror"
                                   placeholder="08012345678"
                                   required>
                            @error('business_phone_1')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Phone 2 -->
                        <div>
                            <label for="business_phone_2" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Phone 2 (Optional)
                            </label>
                            <input type="tel" 
                                   id="business_phone_2" 
                                   name="business_phone_2" 
                                   value="{{ old('business_phone_2') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('business_phone_2') border-red-500 @enderror"
                                   placeholder="08098765432">
                            @error('business_phone_2')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <select id="country" 
                                    name="country" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('country') border-red-500 @enderror"
                                    required>
                                <option value="">Select Country</option>
                                <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                            </select>
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Business Address -->
                    <div class="mt-6">
                        <label for="business_address" class="block text-sm font-medium text-gray-700 mb-1">
                            Business Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="business_address" 
                                  name="business_address" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('business_address') border-red-500 @enderror"
                                  placeholder="No 22, XYZ Street, ABC Area"
                                  required>{{ old('business_address') }}</textarea>
                        @error('business_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State and LGA -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                                State <span class="text-red-500">*</span>
                            </label>
                            <select id="state" 
                                    name="state" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('state') border-red-500 @enderror"
                                    required>
                                <option value="">Select State</option>
                                <option value="Lagos" {{ old('state') == 'Lagos' ? 'selected' : '' }}>Lagos</option>
                                <option value="Kano" {{ old('state') == 'Kano' ? 'selected' : '' }}>Kano</option>
                                <option value="Abuja" {{ old('state') == 'Abuja' ? 'selected' : '' }}>Abuja (FCT)</option>
                                <!-- Add more states as needed -->
                            </select>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="local_government" class="block text-sm font-medium text-gray-700 mb-1">
                                Local Government <span class="text-red-500">*</span>
                            </label>
                            <select id="local_government" 
                                    name="local_government" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('local_government') border-red-500 @enderror"
                                    required>
                                <option value="">Select LGA</option>
                            </select>
                            @error('local_government')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Optional Information Section -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">
                        Optional Information
                    </h2>
                    
                    <!-- Shop Logo -->
                    <div class="mb-6">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">
                            Shop Logo
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    <div>
                        <label for="terms_and_conditions" class="block text-sm font-medium text-gray-700 mb-1">
                            Custom Terms and Conditions
                        </label>
                        <textarea id="terms_and_conditions" 
                                  name="terms_and_conditions" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('terms_and_conditions') border-red-500 @enderror"
                                  placeholder="Enter any specific terms and conditions for your shop...">{{ old('terms_and_conditions') }}</textarea>
                        @error('terms_and_conditions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">These will appear on your receipts (optional)</p>
                    </div>
                </div>

                <!-- Agreement Section -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="agree_terms" 
                                   name="agree_terms" 
                                   type="checkbox" 
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                   required>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="agree_terms" class="text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a> 
                                and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a> 
                                of M-right Digital Receipt platform. <span class="text-red-500">*</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Register Shop
                    </button>
                </div>
            </form>
        </div>

        <!-- Benefits Section -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-blue-100 rounded-full p-3 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Professional Receipts</h3>
                <p class="text-gray-600">Generate beautiful, professional receipts for your customers with your shop branding.</p>
            </div>

            <div class="text-center">
                <div class="bg-green-100 rounded-full p-3 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Anti-Theft Protection</h3>
                <p class="text-gray-600">Integrate with M-right anti-theft system to help protect your customers' devices.</p>
            </div>

            <div class="text-center">
                <div class="bg-purple-100 rounded-full p-3 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Track Performance</h3>
                <p class="text-gray-600">Monitor your sales, track receipts, and earn commissions for each transaction.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // State and LGA dynamic loading
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('local_government');
    
    const lgaData = {
        'Lagos': ['Alimosho', 'Amuwo-Odofin', 'Apapa', 'Badagry', 'Epe', 'Eti-Osa', 'Ibeju-Lekki', 'Ifako-Ijaiye', 'Ikeja', 'Ikorodu', 'Kosofe', 'Lagos Island', 'Lagos Mainland', 'Mushin', 'Ojo', 'Oshodi-Isolo', 'Shomolu', 'Surulere'],
        'Kano': ['Ajingi', 'Albasu', 'Bagwai', 'Bebeji', 'Bichi', 'Bunkure', 'Dala', 'Dambatta', 'Dawakin Kudu', 'Dawakin Tofa', 'Doguwa', 'Fagge', 'Gabasawa', 'Garko', 'Garun Mallam', 'Gaya', 'Gezawa', 'Gwale', 'Gwarzo', 'Kabo', 'Kano Municipal', 'Karaye', 'Kibiya', 'Kiru', 'Kumbotso', 'Kunchi', 'Kura', 'Madobi', 'Makoda', 'Minjibir', 'Nasarawa', 'Rano', 'Rimin Gado', 'Rogo', 'Shanono', 'Sumaila', 'Takai', 'Tarauni', 'Tofa', 'Tsanyawa', 'Tudun Wada', 'Ungogo', 'Warawa', 'Wudil'],
        'Abuja': ['Abaji', 'Bwari', 'Gwagwalada', 'Kuje', 'Kwali', 'Municipal Area Council']
    };
    
    stateSelect.addEventListener('change', function() {
        const selectedState = this.value;
        lgaSelect.innerHTML = '<option value="">Select LGA</option>';
        
        if (selectedState && lgaData[selectedState]) {
            lgaData[selectedState].forEach(lga => {
                const option = document.createElement('option');
                option.value = lga;
                option.textContent = lga;
                lgaSelect.appendChild(option);
            });
        }
    });
    
    // File upload preview
    const logoInput = document.getElementById('logo');
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You can add preview functionality here
                console.log('Logo selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection