@extends('layout.app')

@section('title', 'Staff Management - Add')

@php
    // $bgImage = asset('pictures/Background_adminLogin.jpg');
@endphp

@section('content')
    <main class="mt-0 p-6 bg-[#FFFBEA]/50 backdrop-blur-md min-h-[calc(100vh-2rem)] rounded-lg shadow-xl border border-amber-200">
        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Add New Staff</h2>
        <p class="text-gray-700 mb-6">Fill out the information to add a new employee</p>

        <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-6 text-sm">
            @csrf

            <!-- Full Name -->
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">First Name</label>
                    <input type="text" name="firstName" value="{{ old('firstName') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Middle Name (Optional)</label>
                    <input type="text" name="middleName" value="{{ old('middleName') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Last Name</label>
                    <input type="text" name="lastName" value="{{ old('lastName') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                </div>
            </div>

            <!-- Address -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-gray-700 text-sm">Region</label>
                        <select id="region" name="region"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                            <option value="" data-regioncode="">Select Region</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-700 text-sm">Province</label>
                        <select id="province" name="province"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                            <option value="" data-provincecode="">Select Province</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-700 text-sm">City / Municipality</label>
                        <select id="city" name="city"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                            <option value="" data-citycode="">Select City</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-gray-700 text-sm">Barangay</label>
                        <select id="barangay" name="barangay"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                            <option value="" data-barangaycode="">Select Barangay</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-gray-700 text-sm">Street Address (Optional)</label>
                        <input type="text" name="street" value="{{ old('street') }}"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                    </div>
                </div>
            </div>

            <!-- Gender, DOB, Age -->
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Gender</label>
                    <select name="gender"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="{{ old('dob') }}"
                        max="{{ \Carbon\Carbon::now()->subYears(18)->format('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Age</label>
                    <input type="text" name="age" id="age" value="{{ old('age') }}" readonly
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-700">
                </div>
            </div>

            <!-- Contact -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Phone</label>
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden text-sm focus-within:ring-2 focus-within:ring-[#008C45] bg-white/90">
                        <span class="px-2">
                            <img src="https://flagcdn.com/w20/ph.png" alt="PH Flag" class="w-6 h-4">
                        </span>
                        <span class="px-2 text-gray-700 bg-gray-100 border-r">+63</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" inputmode="numeric" maxlength="10"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full px-3 py-2 text-sm focus:outline-none bg-transparent" placeholder="9XXXXXXXXX">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Email</label>
                    <input type="text" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                </div>
            </div>

            <!-- Department, Role, Status -->
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Department</label>
                    <select name="department" id="department"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                        <option value="">Select Department</option>
                        <option value="front_office" {{ old('department') == 'front_office' ? 'selected' : '' }}>Front Office</option>
                        <option value="housekeeping" {{ old('department') == 'housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                        <option value="kitchen" {{ old('department') == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                        <option value="restaurant" {{ old('department') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                        <option value="management" {{ old('department') == 'management' ? 'selected' : '' }}>Management</option>
                        <option value="maintenance" {{ old('department') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Role</label>
                    <select name="role" id="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                        <option value="">Select Role</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1 text-sm">Status</label>
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#008C45] focus:border-[#008C45] bg-white/90">
                        <option value="On Duty" {{ old('status') == 'On Duty' ? 'selected' : '' }}>On Duty</option>
                        <option value="Off Duty" {{ old('status') == 'Off Duty' ? 'selected' : '' }}>Off Duty</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6 text-sm">
                <a href="{{ route('admin.staff') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</a>
                <button type="submit"
                    class="px-6 py-2 bg-[#008C45] text-white rounded-lg hover:bg-[#007338]">Save Staff</button>
            </div>
        </form>
    </main>

    {{-- Age Calculation --}}
    <script>
        const dobInput = document.getElementById('dob');
        const ageInput = document.getElementById('age');

        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            if (dob == 'Invalid Date') {
                ageInput.value = '';
                return;
            }

            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            ageInput.value = age;
        });
    </script>

    {{-- Error Message --}}
    @if(session('validation_error'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('validation_error') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Auto-focus field if provided
        const focusField = "{{ session('focus') }}";
        if (focusField) {
            document.getElementsByName(focusField)[0]?.focus();
        }
    </script>
    @endif

    {{-- for address selector --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const regionSelect = document.getElementById("region");
            const provinceSelect = document.getElementById("province");
            const citySelect = document.getElementById("city");
            const barangaySelect = document.getElementById("barangay");

            const oldRegion = "{{ old('region') }}";
            const oldProvince = "{{ old('province') }}";
            const oldCity = "{{ old('city') }}";
            const oldBarangay = "{{ old('barangay') }}";

            // Load regions
            fetch("{{ url('staff/api/regions') }}")
                .then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
                .then(data => {
                    data.forEach(region => {
                        regionSelect.innerHTML += `<option value="${region.region_name}" data-regioncode="${region.region_code}" ${region.region_name === oldRegion ? 'selected' : ''}>${region.region_name}</option>`;
                    });

                    // Auto-load provinces if old region exists
                    if (oldRegion) {
                        const selectedOption = regionSelect.options[regionSelect.selectedIndex];
                        loadProvinces(selectedOption.dataset.regioncode);
                    }
                })
                .catch(err => {
                    console.error('Load regions failed', err);
                    if (window.Swal) Swal.fire({ icon: 'error', title: 'Address lookup failed', text: 'Unable to load regions.' });
                });

            // Load provinces
            function loadProvinces(regionCode) {
                const code = encodeURIComponent(regionCode);
                fetch(`{{ url('staff/api/provinces') }}/${code}`)
                    .then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
                    .then(data => {
                        provinceSelect.innerHTML = `<option value="">Select Province</option>`;
                        data.forEach(prov => {
                            provinceSelect.innerHTML += `<option value="${prov.province_name}" data-provincecode="${prov.province_code}" ${prov.province_name === oldProvince ? 'selected' : ''}>${prov.province_name}</option>`;
                        });

                        if (oldProvince) {
                            const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
                            loadCities(selectedOption.dataset.provincecode);
                        }
                    })
                    .catch(err => {
                        console.error('Load provinces failed', err);
                        if (window.Swal) Swal.fire({ icon: 'error', title: 'Address lookup failed', text: 'Unable to load provinces.' });
                    });
            }

            // Load cities
            function loadCities(provinceCode) {
                const code = encodeURIComponent(provinceCode);
                fetch(`{{ url('staff/api/cities') }}/${code}`)
                    .then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
                    .then(data => {
                        citySelect.innerHTML = `<option value="">Select City</option>`;
                        data.forEach(city => {
                            citySelect.innerHTML += `<option value="${city.city_name}" data-citycode="${city.city_code}" ${city.city_name === oldCity ? 'selected' : ''}>${city.city_name}</option>`;
                        });

                        if (oldCity) {
                            const selectedOption = citySelect.options[citySelect.selectedIndex];
                            loadBarangays(selectedOption.dataset.citycode);
                        }
                    })
                    .catch(err => {
                        console.error('Load cities failed', err);
                        if (window.Swal) Swal.fire({ icon: 'error', title: 'Address lookup failed', text: 'Unable to load cities.' });
                    });
            }

            // Load barangays
            function loadBarangays(cityCode) {
                const code = encodeURIComponent(cityCode);
                fetch(`{{ url('staff/api/barangays') }}/${code}`)
                    .then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
                    .then(data => {
                        barangaySelect.innerHTML = `<option value="">Select Barangay</option>`;
                        data.forEach(brgy => {
                            barangaySelect.innerHTML += `<option value="${brgy.brgy_name}" ${brgy.brgy_name === oldBarangay ? 'selected' : ''}>${brgy.brgy_name}</option>`;
                        });
                    })
                    .catch(err => {
                        console.error('Load barangays failed', err);
                        if (window.Swal) Swal.fire({ icon: 'error', title: 'Address lookup failed', text: 'Unable to load barangays.' });
                    });
            }

            // Event listeners
            regionSelect.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                const regionCode = selectedOption.dataset.regioncode;
                provinceSelect.innerHTML = `<option value="">Select Province</option>`;
                citySelect.innerHTML = `<option value="">Select City</option>`;
                barangaySelect.innerHTML = `<option value="">Select Barangay</option>`;

                if (regionCode) loadProvinces(regionCode);
            });

            provinceSelect.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                const provinceCode = selectedOption.dataset.provincecode;
                citySelect.innerHTML = `<option value="">Select City</option>`;
                barangaySelect.innerHTML = `<option value="">Select Barangay</option>`;

                if (provinceCode) loadCities(provinceCode);
            });

            citySelect.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                const cityCode = selectedOption.dataset.citycode;
                barangaySelect.innerHTML = `<option value="">Select Barangay</option>`;

                if (cityCode) loadBarangays(cityCode);
            });
        });
    </script>

    {{-- for department and role selector --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const departmentSelect = document.getElementById("department");
            const roleSelect = document.getElementById("role");
            const oldRole = "{{ old('role') }}"; // 👈 Laravel old value

            function loadRoles(department, selectedRole = null) {
                roleSelect.innerHTML = '<option value="">Select Role</option>';

                if (department) {
                    const dept = encodeURIComponent(department);
                    fetch(`{{ url('staff/api/roles') }}/${dept}`)
                        .then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
                        .then(data => {
                            data.forEach(role => {
                                let option = document.createElement("option");
                                option.value = role;
                                option.text = role;

                                if (selectedRole && selectedRole === role) {
                                    option.selected = true; // 👈 keep old role selected
                                }

                                roleSelect.appendChild(option);
                            });
                        })
                        .catch(err => {
                            console.error('Load roles failed', err);
                            if (window.Swal) Swal.fire({ icon: 'error', title: 'Role lookup failed', text: 'Unable to load roles for the selected department.' });
                        });
                }
            }

            // If department has old value, load roles immediately
            if (departmentSelect.value) {
                loadRoles(departmentSelect.value, oldRole);
            }

            // On department change
            departmentSelect.addEventListener("change", function () {
                loadRoles(this.value);
            });
        });
    </script>

@endsection
