@extends('layout.app')

@section('title', 'Staff Management')

@php
    // $bgImage = asset('pictures/Background_homepage_1.jpg');
@endphp

@section('content')
    <main :class = "collapsed ? 'w-[1150px] ' : 'w-[975px]'" class="mt-0 p-6 bg-[#FFFBEA]/50 backdrop-blur-lg min-h-[calc(100vh-2rem)] rounded-lg shadow-lg border border-amber-200">
        <!-- Header -->
        <h1 class="text-2xl font-bold  text-gray-800 mb-2">Staff Management</h1>
        <p class="text-gray-600 mb-6">Manage hotel and restaurant staff</p>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Staff -->
            <div class="bg-white shadow-md rounded-xl p-6 flex items-center space-x-4 border-l-4 border-[#008C45]">
                <div class="p-3 bg-[#E6F4EA] text-[#008C45] rounded-lg">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $staffs->count() }}</h2>
                    <p class="text-gray-500 text-sm">Total Staff</p>
                </div>
            </div>

            <!-- On Duty -->
            <div class="bg-white shadow-md rounded-xl p-6 flex items-center space-x-4 border-l-4 border-[#D97706]">
                <div class="p-3 bg-[#FFF3E6] text-[#D97706] rounded-lg">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $staffs->where('status', 'On Duty')->count() }}</h2>
                    <p class="text-gray-500 text-sm">On Duty</p>
                </div>
            </div>

            <!-- Off Duty -->
            <div class="bg-white shadow-md rounded-xl p-6 flex items-center space-x-4 border-l-4 border-gray-400">
                <div class="p-3 bg-gray-100 text-gray-600 rounded-lg">
                    <i data-lucide="user-x" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $staffs->where('status', 'Off Duty')->count() }}</h2>
                    <p class="text-gray-500 text-sm">Off Duty</p>
                </div>
            </div>
        </div>

        <!-- Staff Directory -->
        <div class="w-full sm:max-w-[calc(100%)]">
            <!-- Header with button -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Staff Directory</h3>
                    <p class="text-gray-500 text-sm">Employee information and status</p>
                </div>
                <a href="{{ route('admin.staff.add') }}"
                class="flex items-center px-4 py-2 bg-[#008C45] text-white text-sm font-medium rounded-lg shadow hover:bg-[#007338] transition">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Staff
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto shadow rounded-lg">
                <table class="min-w-[1100px] bg-white">
                    <thead class="bg-[#FFF3E6] text-[#D97706] uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Staff ID</th>
                            <th class="px-6 py-3 text-left">Full Name</th>
                            <th class="px-6 py-3 text-left">Full Address</th>
                            <th class="px-6 py-3 text-left">Gender</th>
                            <th class="px-6 py-3 text-left">Date of Birth</th>
                            <th class="px-6 py-3 text-left">Age</th> <!-- New Age Column -->
                            <th class="px-6 py-3 text-left">Phone</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Department</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        <!-- Staff Loop -->
                        @forelse($staffs as $staff)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $staff->staffId }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    {{ $staff->lastName }}, {{ $staff->firstName }}
                                    @if($staff->middleName) {{ $staff->middleName }} @endif
                                </td>
                                <td class="px-6 py-3 max-w-md text-sm leading-snug"
                                    title="{{ implode(', ', array_filter([$staff->street, $staff->barangay, $staff->city, $staff->province, $staff->region])) }}">
                                    <div class="truncate">
                                        {{ implode(', ', array_filter([$staff->street, $staff->barangay])) }}
                                    </div>
                                    <div class="truncate">
                                        {{ implode(', ', array_filter([$staff->city, $staff->province, $staff->region])) }}
                                    </div>
                                </td>

                                <td class="px-6 py-3 whitespace-nowrap text-center">{{ $staff->gender }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-center">{{ $staff->dob }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-center">
                                    {{ \Carbon\Carbon::parse($staff->dob)->age }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-center">{{ $staff->phone }}</td>
                                <td class="px-6 py-3 max-w-xs truncate" title="{{ $staff->email }}">
                                    {{ $staff->email }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $staff->department }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $staff->role }}</td>
                                <td class="px-6 py-3 text-center whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 rounded text-xs
                                            {{ $staff->status === 'On Duty' ? 'bg-[#E6F4EA] text-[#008C45]' : 'bg-red-100 text-red-700' }}">
                                        {{ $staff->status }}
                                    </span>
                                </td>


                                <td class="px-6 py-3">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('admin.staff.edit', $staff->id) }}" 
                                            class="flex items-center px-3 py-1 bg-[#D97706] text-white text-xs rounded hover:bg-[#b56205]">
                                            <i data-lucide="edit" class="w-4 h-4 mr-1"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                class="flex items-center text-gray-600 text-xs hover:underline delete-btn" 
                                                data-id="{{ $staff->id }}" onclick="confirmDelete(this)">
                                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-6 py-3 text-center text-gray-500 text-sm">
                                    No staff records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- Delete Decission --}}
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action will permanently delete the staff record!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D97706', // Matches your theme
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>



@endsection