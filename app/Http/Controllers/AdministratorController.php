<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\RoomReservation;

class AdministratorController extends Controller
{
    public function dashboardIndex(Request $request)
    {
        $status = $request->query('status', 'Booked');
        $type = $request->query('type', 'All');
        $filter = $request->query('filter', 'all');

        $query = RoomReservation::query();

        // Filter by status
        if ($status !== 'All') {
            $query->where('reservation_status', $status);
        }

        // Filter by reservation type
        if ($type !== 'All') {
            $query->where('reservation_process', $type);
        }

        // Upcoming filters
        if ($filter !== 'all') {
            $now = Carbon::now();
            switch ($filter) {
                case '1day':
                    $query->whereBetween('checkin_datetime', [$now, $now->clone()->addDay()]);
                    break;
                case '3days':
                    $query->whereBetween('checkin_datetime', [$now, $now->clone()->addDays(3)]);
                    break;
                case 'week':
                    $query->whereBetween('checkin_datetime', [$now, $now->clone()->addWeek()]);
                    break;
            }
        }

        // Get results
        $reservations = $query->orderByDesc('created_at')->get();

        return view('admin.adminDashboard', [
            'reservations' => $reservations,
            'status' => $status,
            'type' => $type,
            'filter' => $filter,
        ]);
    }
}
