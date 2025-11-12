<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getRoles($department)
    {
        $roles = [
            'front_office' => ['Receptionist', 'Concierge', 'Front Desk Manager'],
            'housekeeping' => ['Room Attendant', 'Laundry Staff', 'Housekeeping Supervisor'],
            'kitchen' => ['Chef', 'Sous Chef', 'Kitchen Assistant'],
            'restaurant' => ['Waiter', 'Bartender', 'Restaurant Supervisor'],
            'management' => ['HR Manager', 'General Manager', 'Finance Officer'],
            'maintenance' => ['Technician', 'Electrician', 'Plumber', 'Maintenance Supervisor'], // added
        ];

        return response()->json($roles[$department] ?? []);
    }
}
