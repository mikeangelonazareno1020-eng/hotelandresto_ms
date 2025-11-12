<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\AccountAdmin;
use Illuminate\Http\Request;
use App\Models\AccountCustomer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdministratorStaffController extends Controller
{
    // Show staff list
    public function staffIndex()
    {
        $staffs = Staff::orderBy('id', 'desc')->get();
        return view('admin.staff_side.adminStaff', compact('staffs'));
    }

    // Store staff in database
    public function store(Request $request)
    {
        if (\App\Models\AccountAdmin::where('email', $request->email)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('validation_error', 'Email already exists')
                ->with('focus', 'email');
        }
        if (\App\Models\AccountCustomer::where('email', $request->email)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('validation_error', 'Email already exists')
                ->with('focus', 'email');
        }

        try {
            // validate with custom messages
            $request->validate([
                'firstName' => 'required|string|max:255',
                'middleName' => 'nullable|string|max:255',
                'lastName' => 'required|string|max:255',
                'region' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'street' => 'nullable|string|max:255',
                'gender' => 'required|in:Male,Female,Other',
                'dob' => 'required|date|before:today',
                'phone' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
                'email' => 'required|email|unique:staffs,email',
                'department' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'status' => 'required|in:On Duty, Off Duty',
            ], [
                'email.unique' => 'Email already exists in Staffs.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $firstErrorField = array_key_first($errors);
            $firstErrorMessage = $errors[$firstErrorField][0];

            return redirect()->back()
                ->withInput()
                ->with('validation_error', $firstErrorField === 'email' ? $firstErrorMessage : 'Invalid Staff Credentials')
                ->with('focus', $firstErrorField);
        }

        // format Staff ID
        $nextId = DB::select("SELECT AUTO_INCREMENT 
                  FROM information_schema.TABLES 
                  WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'staffs'")[0]->AUTO_INCREMENT;

        $staffId = 'HC' . str_pad((100000 + $nextId), 6, '0', STR_PAD_LEFT);

        // create staff
        Staff::create([
            'staffId' => $staffId,
            'firstName' => $request->firstName,
            'middleName' => $request->middleName,
            'lastName' => $request->lastName,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'street' => $request->street,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'email' => $request->email,
            'department' => $request->department,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.staff')
            ->with('success', 'Staff added successfully');
    }

    // Show staff credential in edit form
    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        return view('admin.staff_side.adminStaffEdit', compact('staff'));
    }

    // Update staff in database
    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        // First, check if email exists in admins table
        if (AccountAdmin::where('email', $request->email)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('validation_error', 'Email already exists')
                ->with('focus', 'email');
        }
        if (AccountCustomer::where('email', $request->email)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('validation_error', 'Email already exists')
                ->with('focus', 'email');
        }
        try {
            // Validate with custom messages
            $request->validate([
                'firstName' => 'required|string|max:255',
                'middleName' => 'nullable|string|max:255',
                'lastName' => 'required|string|max:255',
                'region' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'street' => 'nullable|string|max:255',
                'gender' => 'required|in:Male,Female,Other',
                'dob' => 'required|date|before:today',
                'phone' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('staffs', 'email')->ignore($staff->id),
                ],
                'department' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'status' => 'required|in:On Duty,Off Duty',
            ], [
                'email.unique' => 'Email already exists in Staffs.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $firstErrorField = array_key_first($errors);
            $firstErrorMessage = $errors[$firstErrorField][0]; // first message for that field

            return redirect()->back()
                ->withInput()
                ->with('validation_error', $firstErrorField === 'email' ? $firstErrorMessage : 'Invalid Staff Credentials')
                ->with('focus', $firstErrorField);
        }

        // Update staff
        $staff->update([
            'firstName' => $request->firstName,
            'middleName' => $request->middleName,
            'lastName' => $request->lastName,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'street' => $request->street,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'email' => $request->email,
            'department' => $request->department,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.staff')
            ->with('success', 'Staff updated successfully!');
    }

    // Delete staff in database
    public function destroy($id)
    {
        try {
            $staff = Staff::findOrFail($id);
            $staff->delete(); // Permanently deletes the record

            return redirect()->route('admin.staff')
                ->with('success', 'Staff deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.staff')
                ->with('error', 'Failed to delete staff record!');
        }
    }
}
