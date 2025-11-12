<?php

namespace App\Http\Controllers;

use App\Models\AccountAdmin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        return view('super.superDashboard');
    }

    // Admin Accounts (index + create form)
    public function accounts(Request $request)
    {
        $query = AccountAdmin::query();
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('role', 'like', "%{$q}%");
            });
        }
        $admins = $query->orderBy('created_at', 'desc')->get();
        $roles = ['Super Admin','Administrator','Hotel Manager','Hotel Frontdesk','Restaurant Manager','Restaurant Cashier'];
        return view('super.superAdminaccount', compact('admins','roles'));
    }

    public function accountStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required','email','max:150','unique:account_admins,email'],
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'role' => ['required', Rule::in(['Super Admin','Administrator','Hotel Manager','Hotel Frontdesk','Restaurant Manager','Restaurant Cashier'])],
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        AccountAdmin::create($data); // password auto-hashed via mutator
        return back()->with('success', 'Admin account created');
    }

    public function accountUpdate(Request $request, AccountAdmin $admin)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required','email','max:150', Rule::unique('account_admins','email')->ignore($admin->id)],
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'role' => ['required', Rule::in(['Super Admin','Administrator','Hotel Manager','Hotel Frontdesk','Restaurant Manager','Restaurant Cashier'])],
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $admin->update($data);
        return back()->with('success', 'Admin account updated');
    }

    public function accountDestroy(AccountAdmin $admin)
    {
        $admin->delete();
        return back()->with('success', 'Admin account deleted');
    }

    public function customers()
    {
        return view('super.superCustomer');
    }

    public function gps()
    {
        // Load transport vehicles with latest location + online/offline status
        $rows = \Illuminate\Support\Facades\DB::table('transport_vehicles as v')
            ->leftJoinSub(
                \Illuminate\Support\Facades\DB::table('transport_vehicle_locations')
                    ->select('vehicle_id', \Illuminate\Support\Facades\DB::raw('MAX(id) as max_id'))
                    ->groupBy('vehicle_id'),
                'm',
                function($join){ $join->on('v.id','=','m.vehicle_id'); }
            )
            ->leftJoin('transport_vehicle_locations as loc', function($join){
                $join->on('loc.vehicle_id','=','v.id')->on('loc.id','=','m.max_id');
            })
            ->orderBy('v.id')
            ->get([
                'v.id',
                \Illuminate\Support\Facades\DB::raw('v.plate_no as plate_number'),
                \Illuminate\Support\Facades\DB::raw('v.gps_device_id as gps_device'),
                'loc.latitude','loc.longitude','loc.created_at'
            ])
            ->map(function($r){
                $r->status = ($r->created_at && now()->diffInSeconds(\Carbon\Carbon::parse($r->created_at)) <= 60)
                    ? 'online' : 'offline';
                return $r;
            });

        $devices = \App\Models\ApiDevice::orderByDesc('last_used_at')
            ->orderBy('id', 'desc')
            ->get();

        return view('super.superGps', [
            'vehicles' => $rows,
            'devices' => $devices,
        ]);
    }

    public function gpsStore(Request $request)
    {
        $data = $request->validate([
            'plate_number' => 'required|string|max:80|unique:transport_vehicles,plate_no',
            'gps_device'   => 'nullable|string|max:120',
        ]);
        \App\Models\TransportVehicle::create([
            'plate_no' => $data['plate_number'],
            'gps_device_id' => $data['gps_device'] ?? null,
        ]);
        return back()->with('success', 'Vehicle added');
    }

    public function gpsUpdate(Request $request, \App\Models\TransportVehicle $vehicle)
    {
        $data = $request->validate([
            'plate_number' => 'required|string|max:80|unique:transport_vehicles,plate_no,' . $vehicle->id,
            'gps_device'   => 'nullable|string|max:120',
        ]);
        $vehicle->update([
            'plate_no' => $data['plate_number'],
            'gps_device_id' => $data['gps_device'] ?? null,
        ]);
        return back()->with('success', 'Vehicle updated');
    }

    public function gpsDestroy(\App\Models\TransportVehicle $vehicle)
    {
        $vehicle->delete();
        return back()->with('success', 'Vehicle deleted');
    }

    public function logs()
    {
        return view('super.superLogs');
    }
}
