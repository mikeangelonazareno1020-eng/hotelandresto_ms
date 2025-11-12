<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Receipt;
use App\Models\RoomInfo;
use App\Models\Amenity;
use Illuminate\Http\Request;
use App\Models\RoomReservation;
use Illuminate\Support\Facades\DB;

class ManagerHotelController extends Controller
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

        return view('manager_hotel.hotelDashboard', [
            'reservations' => $reservations,
            'status' => $status,
            'type' => $type,
            'filter' => $filter,
        ]);
    }

    public function bookingPage(Request $request)
    {
        $query = RoomReservation::with('room');

        // ✅ Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('reservation_id', 'like', "%{$search}%");
            });
        }

        // ✅ Optional status filter
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('reservation_status', $request->status);
        }

        // ✅ Sort by latest updated first
        $reservations = $query->orderByDesc('updated_at')->get();

        return view('manager_hotel.hotelBooking', [
            'reservations' => $reservations,
            'search' => $request->input('search', ''),
            'status' => $request->input('status', 'All'),
        ]);
    }

    public function onlineBookingPage(Request $request)
    {
        $query = RoomReservation::with('room')
            ->where('reservation_process', 'Online');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('reservation_id', 'like', "%{$search}%");
            });
        }

        // Map UI status to underlying reservation_status
        $status = $request->input('status', 'All');
        if ($status !== 'All') {
            switch ($status) {
                case 'Pending':
                    $query->where('reservation_status', 'Pending');
                    break;
                case 'Confirmed':
                    $query->whereIn('reservation_status', ['Booked', 'Checked In']);
                    break;
                case 'Rejected':
                    $query->whereIn('reservation_status', ['Cancelled', 'No Show']);
                    break;
                case 'Completed':
                    $query->where('reservation_status', 'Checked Out');
                    break;
            }
        }

        $reservations = $query->orderByDesc('updated_at')->get();

        return view('manager_hotel.hotelOnlineBooking', [
            'reservations' => $reservations,
            'search' => $request->input('search', ''),
            'status' => $status,
        ]);
    }

    public function bookingCreate()
    {
        // Get all rooms that are Vacant
        $rooms = RoomInfo::all();

        // Pass to view
        return view('manager_hotel.hotelBookingForm', compact('rooms'));
    }

    public function bookingStore(Request $request)
    {
        try {
            DB::beginTransaction();

            // ✅ Generate Reservation ID
            $nextAutoId = DB::selectOne("
            SELECT AUTO_INCREMENT 
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'room_reservations'
        ")->AUTO_INCREMENT ?? 1;

            $reservationId = 'HRES' . str_pad((100000 + $nextAutoId), 6, '0', STR_PAD_LEFT);

            // ✅ Generate Receipt ID
            $nextReceiptId = DB::selectOne("
            SELECT AUTO_INCREMENT 
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'receipts'
        ")->AUTO_INCREMENT ?? 1;

            $receiptId = 'RCPT' . str_pad((100000 + $nextReceiptId), 6, '0', STR_PAD_LEFT);


            // ✅ Decode payload
            $payload = json_decode($request->input('payload'), true);

            // dd($payload);
            $roomNumber = $payload['room_number'] ?? $payload['selectedRoom'] ?? null;
            $reservationStatus = $payload['reservation_status'] ?? 'Booked';

            // ✅ Save reservation
            $reservation = RoomReservation::create([
                'reservation_id' => $reservationId,
                'first_name' => $payload['first_name'] ?? null,
                'middle_name' => $payload['middle_name'] ?? null,
                'last_name' => $payload['last_name'] ?? null,
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'address' => $payload['address'] ?? null,
                'room_number' => $payload['selected_room_number'],
                'checkin_date' => $payload['checkin_date'] ?? null,
                'checkout_date' => $payload['checkout_date'] ?? null,
                'guest_quantity' => $payload['guest_quantity'] ?? 1,
                'total_days' => $payload['total_days'] ?? 1,
                'room_added_amenities' => $payload['selected_amenities'] ?? [],
                'amenities_charge' => $payload['added_amenities_fee'] ?? 0,
                'room_charge' => $payload['reservation_fee'] ?? 0,
                'total_amount' => $payload['total_amount'] ?? 0,
                'net_amount' => $payload['total_due'] ?? 0,
                'payment_status' =>
                    ($payload['total_due'] ?? 0) <= 0
                    ? 'Full Paid'
                    : 'Not Full Paid',
                'downpayment' => $payload['downpayment'] ?? 0,
                'special_request' => $payload['special_request'] ?? null,
                'reservation_status' => $reservationStatus,
                'reservation_process' => 'Walk In',
                'payment_method' => $payload['payment_method'] ?? null,
            ]);


            // ✅ Save Receipt
            Receipt::create([
                'receipt_id' => $receiptId,
                'reference_id' => $reservationId,
                'type' => 'Hotel', // ✅ matches ENUM('Hotel', 'Restaurant')

                // 🧍 Customer Details
                'customer_id' => null,
                'customer_name' => trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? '')),
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,

                // 💵 Financial Details
                'items' => json_encode([
                    'room_number' => $payload['room_number'] ?? $roomNumber,
                    'nights' => $payload['total_days'] ?? 1,
                    'amenities' => $payload['room_added_amenities'] ?? [],
                ]),
                'subtotal' => (float) ($payload['reservation_fee'] ?? 0),
                'total' => (float) ($payload['total_amount'] ?? 0),
                'amount' => (float) ($payload['payment_amount'] ?? 0),
                'amount_tendered' => (float) ($payload['amount_tendered'] ?? 0),
                'change_due' => (float) ($payload['change_due'] ?? 0),

                // 💳 Payment Info
                'payment_method' => match (strtolower($payload['payment_method'] ?? '')) {
                    'cash' => 'Cash',
                    'gcash', 'paymaya', 'ewallet' => 'E-Wallet',
                    'card', 'credit card', 'debit card' => 'Card',
                    'bank', 'bank transfer' => 'Bank Transfer',
                    default => null,
                },
                'payment_details' => json_encode($payload['payment_details'] ?? []),

                // 🧾 Staff / Admin
                'issued_by' => $payload['issued_by'] ?? session('user_name') ?? 'Hotel Manager',
                'adminId' => $payload['admin_id'] ?? session('user_id') ?? null,

                // 📅 Timing
                'issued_at' => now('Asia/Manila'),
            ]);

            $roomNumber = $payload['selected_room_number'];

            // ✅ Update room_info
            $roomInfo = DB::table('room_info')->where('room_number', $roomNumber)->lockForUpdate()->first();
            $existingReservations = json_decode($roomInfo->room_reservations ?? '[]', true);

            $existingReservations[] = [
                'id' => $reservationId,
                'status' => $reservationStatus,
                'checkin_date' => $payload['checkin_date'] ?? $payload['checkinDate'] ?? null,
                'checkout_date' => $payload['checkout_date'] ?? $payload['checkoutDate'] ?? null,
            ];

            if ($roomInfo) {
                $today = \Carbon\Carbon::today('Asia/Manila')->format('Y-m-d');

                if (($payload['checkin_date'] ?? $payload['checkinDate']) <= $today) {
                    DB::table('room_info')
                        ->where('room_number', $roomNumber)
                        ->update([
                            'room_status' => 'Booked',
                            'reservation_id' => $reservationId,
                            'room_reservations' => json_encode($existingReservations),
                        ]);
                } else {
                    DB::table('room_info')
                        ->where('room_number', $roomNumber)
                        ->update([
                            'room_reservations' => json_encode($existingReservations),
                        ]);
                }
            }

            DB::commit();

            return redirect()->route('hotelmanager.booking')->with('alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Reservation and Receipt created successfully.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }

    public function checkinPage($reservationId)
    {
        $reservation = RoomReservation::with('room')->where('reservation_id', $reservationId)->firstOrFail();

        // --- Receipts & standard info ---
        $receipts = Receipt::where('reference_id', $reservation->reservation_id)
            ->where('type', 'Hotel')
            ->orderBy('issued_at', 'desc')
            ->get();

        // 🛏️ Decode added amenities (safe decode)
        $addedAmenities = $reservation->room_added_amenities;
        if (!is_array($addedAmenities)) {
            $addedAmenities = json_decode($addedAmenities ?? '[]', true) ?? [];
        }
        $addedAmenityNames = array_map(fn($item) => $item['name'] ?? '', $addedAmenities);


        $downpayment = json_decode($reservation->downpayment ?? '[]', true);
        $downpaymentAmount = $downpayment['amount'] ?? 0;

        $paymentLabel = match (strtolower($reservation->payment_status ?? '')) {
            'paid' => 'Paid',
            'downpayment' => 'Unpaid (Partial)',
            default => 'Unpaid',
        };

        $now = \Carbon\Carbon::now('Asia/Manila')->startOfDay();
        $checkinDate = \Carbon\Carbon::parse($reservation->checkin_date)->startOfDay();
        $isCheckinToday = $now->equalTo($checkinDate);
        $roomStatus = $reservation->room?->status ?? 'Unknown';

        // --- Decode room_reservations JSON from room_info ---
        $roomReservations = $reservation->room?->room_reservations ?? [];
        if (is_string($roomReservations)) {
            $roomReservations = json_decode($roomReservations, true) ?? [];
        }

        // --- Detect conflicts ---
        $isUnavailable = false;
        $hasEarlyCheckin = false;
        $alertMessage = null;
        $alertType = null; // 'error' or 'warning'
        $extraData = [];

        foreach ($roomReservations as $r) {
            // 🧠 Skip self
            if (($r['id'] ?? null) === $reservation->reservation_id) {
                continue;
            }

            if (in_array($r['status'], ['Booked', 'Checked In'])) {
                $existingStart = \Carbon\Carbon::parse($r['checkin_date']);
                $existingEnd = \Carbon\Carbon::parse($r['checkout_date']);
                $newStart = \Carbon\Carbon::parse($reservation->checkin_date);
                $newEnd = \Carbon\Carbon::parse($reservation->checkout_date);

                // 🔍 Overlap check (ignore itself)
                if (
                    $newStart->between($existingStart, $existingEnd) ||
                    $existingStart->between($newStart, $newEnd)
                ) {
                    $isUnavailable = true;
                    $alertType = 'error';
                    $alertMessage = "The room ({$reservation->room_number}) is currently unavailable for check-in ({$r['status']}).";
                    break;
                }
            }
        }


        // --- Early Check-in logic ---
        if (!$isUnavailable && $now->lessThan($checkinDate)) {
            // Compute early days difference
            $earlyDays = $checkinDate->diffInDays($now);
            $hasEarlyCheckin = true;
            $alertType = 'warning';
            $alertMessage = "This is an early check-in request ({$earlyDays} day/s early). Ensure no conflicts before proceeding.";

            // Add early check-in data
            $extraData[] = [
                'name' => 'Early Check-In',
                'amount' => $reservation->room?->room_rate ?? 0,
                'qty' => abs($earlyDays),
                'total' => abs(($reservation->room?->room_rate ?? 0) * $earlyDays),
            ];
        }

        return view('manager_hotel.hotelBookingCheckinPage', compact(
            'reservation',
            'receipts',
            'addedAmenityNames',
            'downpaymentAmount',
            'paymentLabel',
            'isCheckinToday',
            'isUnavailable',
            'roomStatus',
            'alertMessage',
            'alertType',
            'extraData'
        ));
    }

    public function bookingCheckIn()
    {
        DB::beginTransaction();

        try {
            // ✅ Get POST values directly
            $roomNumber = request('room_number'); // same as $request->room_number
            $extraDataJson = request('extra_data'); // same as $request->extra_data

            // ✅ Find the reservation currently Booked
            $reservation = RoomReservation::where('room_number', $roomNumber)
                ->where('reservation_status', 'Booked')
                ->firstOrFail();

            // ✅ Decode extras JSON (from hidden field)
            $extras = [];
            $extraChargeTotal = 0;

            if (!empty($extraDataJson)) {
                $decoded = json_decode($extraDataJson, true);
                if (is_array($decoded)) {
                    $extras = $decoded;
                    $extraChargeTotal = collect($extras)->sum('total');
                }
            }

            // ✅ Merge with existing extras if any
            $existingExtras = $reservation->extras ?? [];
            if (!is_array($existingExtras)) {
                $existingExtras = json_decode($existingExtras, true) ?? [];
            }

            $mergedExtras = array_merge($existingExtras, $extras);
            $mergedTotal = abs(collect($mergedExtras)->sum('total'));

            // ✅ Update reservation financials
            $reservation->reservation_status = 'Checked In';
            $reservation->actual_checkin = Carbon::now('Asia/Manila');
            $reservation->extras = $mergedExtras;
            $reservation->extra_charge = $mergedTotal;

            // 🧮 Add extra charges to totals
            $originalTotal = $reservation->total_amount ?? 0;
            $originalNet = $reservation->net_amount ?? 0;

            // Update total and net amounts
            $reservation->total_amount = $originalTotal + $mergedTotal;
            $reservation->net_amount = $originalNet + $mergedTotal;

            // 💳 If there’s still a balance (net_amount > 0), mark as not fully paid
            if ($reservation->net_amount > 0) {
                $reservation->payment_status = 'Not Full Paid';
            }

            $reservation->save();

            // dd($mergedTotal);
            // ✅ Update room info
            $room = RoomInfo::where('room_number', $roomNumber)->firstOrFail();
            $room->room_status = 'Checked In';
            $room->reservation_id = $reservation->reservation_id;

            // ✅ Update room reservations JSON
            $roomReservations = $room->room_reservations ?? [];
            if (is_string($roomReservations)) {
                $roomReservations = json_decode($roomReservations, true) ?? [];
            }

            foreach ($roomReservations as &$res) {
                if (($res['id'] ?? null) === $reservation->reservation_id) {
                    $res['status'] = 'Checked In';
                }
            }

            $room->room_reservations = $roomReservations;
            $room->save();

            DB::commit();

            return redirect()->route('hotelmanager.booking')->with('alert', [
                'type' => 'success',
                'title' => 'Check-In Successful',
                'message' => 'Guest has been checked in successfully. Extra charges applied: ₱' . number_format($mergedTotal, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }

    public function checkoutPage($reservationId)
    {
        $nowManila = Carbon::now('Asia/Manila');

        // 🔍 Fetch reservation with related room info
        $reservation = RoomReservation::with('room')->where('reservation_id', $reservationId)->firstOrFail();

        // 🧺 Decode added amenities
        $addedAmenities = $reservation->room_added_amenities;
        if (!is_array($addedAmenities)) {
            $addedAmenities = json_decode($addedAmenities ?? '[]', true) ?? [];
        }
        $addedAmenityNames = array_map(fn($item) => $item['name'] ?? '', $addedAmenities);

        // 💳 Payment label
        $paymentStatusLower = strtolower($reservation->payment_status ?? '');
        $paymentLabel = match ($paymentStatusLower) {
            'full paid' => 'Full Paid',
            'not full paid' => 'Not Full Paid',
            default => 'Unpaid',
        };

        // 💰 Downpayment decode
        $downpayment = $reservation->downpayment;
        if (is_string($downpayment)) {
            $downpayment = json_decode($downpayment, true) ?? [];
        }
        $downpaymentAmount = $downpayment['amount'] ?? 0;

        // 🚪 Room status & availability
        $roomStatus = $reservation->room->room_status ?? 'Unknown';
        $roomReservationId = $reservation->room->reservation_id ?? null;
        $unavailableStatuses = ['Booked', 'Checked In', 'Out of Service'];
        $isUnavailable = in_array($roomStatus, $unavailableStatuses) && $roomReservationId !== $reservation->reservation_id;

        // 🕓 Check if checkout date exceeded
        $checkinDate = $reservation->checkin_date ? Carbon::parse($reservation->checkin_date)->startOfDay() : null;
        $checkoutDate = $reservation->checkout_date ? Carbon::parse($reservation->checkout_date)->startOfDay() : null;

        $checkoutExceeded = false;
        $nightsExceeded = 0;
        $extraCharge = 0;

        if ($checkoutDate && $checkoutDate->lt($nowManila->startOfDay())) {
            $checkoutExceeded = true;
            $nightsExceeded = abs($checkoutDate->diffInDays($nowManila));
            $roomRate = $reservation->room_rate ?? 0;
            $extraRate = $roomRate * 1.1;
            $extraCharge = $extraRate * $nightsExceeded;
        }

        // ✅ Can only checkout if paid (not blocked by date exceed)
        $canCheckout = $paymentStatusLower === 'full paid';

        // 🔔 Alert setup
        $alertMessage = null;
        $alertType = null;

        if ($paymentStatusLower !== 'full paid') {
            $alertMessage = 'Guest cannot check out because payment is incomplete. Please settle the remaining balance first.';
            $alertType = 'error';
        } elseif ($checkoutExceeded) {
            $alertMessage = "Checkout date has passed by {$nightsExceeded} night(s). Extra charge of ₱" . number_format($extraCharge, 2) . " may apply.";
            $alertType = 'warning';
        }

        // 🧾 Placeholders for consistent structure
        $receipts = [];
        $isCheckinToday = false;
        $extraData = null;

        return view('manager_hotel.hotelBookingCheckoutPage', compact(
            'reservation',
            'receipts',
            'addedAmenityNames',
            'downpaymentAmount',
            'paymentLabel',
            'isCheckinToday',
            'isUnavailable',
            'roomStatus',
            'alertMessage',
            'alertType',
            'extraData',
            'checkoutExceeded',
            'nightsExceeded',
            'extraCharge',
            'canCheckout'
        ));
    }

    public function bookingCheckOut()
    {
        $nowManila = Carbon::now('Asia/Manila');

        DB::beginTransaction();

        try {
            // 1️⃣ Get reservation data
            $reservationId = request('reservation_id');

            $reservation = DB::table('room_reservations')
                ->where('reservation_id', $reservationId)
                ->first();

            if (!$reservation) {
                throw new \Exception("Reservation not found.");
            }

            // 2️⃣ Fetch the related room
            $room = DB::table('room_info')
                ->where('room_number', $reservation->room_number)
                ->first();

            if (!$room) {
                throw new \Exception("Room not found for this reservation.");
            }

            // Decode room_reservations JSON
            $roomReservationsJson = json_decode($room->room_reservations ?? '[]', true);

            // 3️⃣ Check if checkout exceeded
            $checkoutDate = Carbon::parse($reservation->checkout_date)->startOfDay();
            $exceededDays = $nowManila->greaterThan($checkoutDate)
                ? $checkoutDate->diffInDays($nowManila)
                : 0;

            $extraCharges = json_decode($reservation->extras ?? '[]', true);

            if ($exceededDays > 0) {
                $roomRate = $room->room_rate ?? 0;
                $amountPerDay = $roomRate + ($roomRate * 0.25);
                $totalExtra = $amountPerDay * $exceededDays;

                $extraCharges[] = [
                    'name' => 'Checkout date exceeded',
                    'amount' => round($amountPerDay, 2),
                    'qty' => $exceededDays,
                    'total' => round($totalExtra, 2),
                ];

                // Update reservation extras + extra_charge
                DB::table('room_reservations')
                    ->where('reservation_id', $reservationId)
                    ->update([
                        'extras' => json_encode($extraCharges),
                        'extra_charge' => DB::raw('COALESCE(extra_charge, 0) + ' . $totalExtra),
                        'updated_at' => $nowManila
                    ]);
            }

            // 4️⃣ Update room_info JSON + status
            foreach ($roomReservationsJson as &$res) {
                if ($res['id'] === $reservationId) {
                    $res['status'] = 'Checked Out';
                }
            }
            unset($res);

            DB::table('room_info')
                ->where('room_number', $room->room_number)
                ->update([
                    'room_status' => 'Checked Out',
                    'reservation_id' => null,
                    'room_reservations' => json_encode($roomReservationsJson),
                    'updated_at' => $nowManila
                ]);

            // 5️⃣ Update reservation status
            DB::table('room_reservations')
                ->where('reservation_id', $reservationId)
                ->update([
                    'reservation_status' => 'Checked Out',
                    'actual_checkout' => $nowManila,
                    'updated_at' => $nowManila
                ]);

            DB::commit();

            return redirect()->route('hotelmanager.booking')->with('alert', [
                'type' => 'success',
                'title' => 'Check-Out Successful',
                'message' => $exceededDays > 0
                    ? "Guest checked out successfully. Note: Checkout exceeded by {$exceededDays} day(s). Extra charge added."
                    : 'Guest has been checked out successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }

    public function cancelPage($reservationId)
    {
        $nowManila = Carbon::now('Asia/Manila');
        $reservation = RoomReservation::with('room')->where('reservation_id', $reservationId)->firstOrFail();

        // 🧺 Decode added amenities
        $addedAmenities = $reservation->room_added_amenities;
        if (!is_array($addedAmenities)) {
            $addedAmenities = json_decode($addedAmenities ?? '[]', true) ?? [];
        }
        $addedAmenityNames = array_map(fn($item) => $item['name'] ?? '', $addedAmenities);

        // 💳 Payment label
        $paymentLabel = match (strtolower($reservation->payment_status ?? '')) {
            'paid' => 'Paid',
            'downpayment' => 'Unpaid (Partial)',
            default => 'Unpaid',
        };

        // 💰 Decode downpayment
        $downpayment = $reservation->downpayment;
        if (is_string($downpayment)) {
            $downpayment = json_decode($downpayment, true) ?? [];
        }
        $downpaymentAmount = $downpayment['amount'] ?? 0;

        // 🚪 Room status & availability
        $roomStatus = $reservation->room->room_status ?? 'Unknown';
        $roomReservationId = $reservation->room->reservation_id ?? null;
        $unavailableStatuses = ['Booked', 'Checked In', 'Out of Service'];
        $isUnavailable = in_array($roomStatus, $unavailableStatuses) && $roomReservationId !== $reservation->reservation_id;

        // ✅ Other optional variables (mirroring Check-In)
        $receipts = [];         // placeholder if you link receipts to reservation later
        $isCheckinToday = false; // keep same naming convention for consistency
        $alertMessage = null;    // optional alerts
        $alertType = null;
        $extraData = null;

        // ✅ Return same structure & naming as Check-In
        return view('manager_hotel.hotelBookingCancelPage', compact(
            'reservation',
            'receipts',
            'addedAmenityNames',
            'downpaymentAmount',
            'paymentLabel',
            'isCheckinToday',
            'isUnavailable',
            'roomStatus',
            'alertMessage',
            'alertType',
            'extraData'
        ));
    }

    public function bookingCancel(Request $request)
    {
        $nowManila = Carbon::now('Asia/Manila');

        $request->validate([
            'reservation_id' => 'required|exists:room_reservations,reservation_id',
            'cancel_list' => 'nullable|array',
            'cancel_reason' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $request->validate([
                'reservation_id' => 'required|string',
            ]);

            $reservation = RoomReservation::where('reservation_id', $request->reservation_id)->firstOrFail();

            // 📝 Build cancel reason array (from checkboxes)
            $cancelList = $request->input('cancel_list', []); // array from checkboxes

            // 🗒️ Additional reason from textarea
            $cancelReason = $request->input('cancel_reason', '');

            // 🕓 Update reservation details
            $reservation->update([
                'reservation_status' => 'Cancelled',
                'cancel_date' => $nowManila,
                'cancel_list' => $cancelList, // this should be a JSON column in DB
                'cancel_reason' => $cancelReason,
                'updated_at' => $nowManila,
            ]);

            // Update room info
            $room = $reservation->room;
            if ($room) {
                $roomReservations = $room->room_reservations;
                if (!$roomReservations || !is_array($roomReservations)) {
                    $roomReservations = json_decode($roomReservations, true) ?? [];
                }

                foreach ($roomReservations as &$r) {
                    if (($r['id'] ?? null) === $reservation->reservation_id) {
                        $r['status'] = 'Cancelled';
                    }
                }

                $room->room_reservations = $roomReservations; // Eloquent casts array to JSON
                $room->room_status = 'Vacant';
                $room->reservation_id = null;
                $room->save();
            }

            DB::commit();

            // ✅ Redirect with flash message
            return redirect()->route(route: 'hotelmanager.booking')->with('alert', [
                'type' => 'success',
                'title' => 'Reservation Cancelled',
                'message' => 'The reservation has been successfully cancelled and payment marked as Refund.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
            ;
        }
    }

    public function detailsPage($reservationId)
    {
        $nowManila = Carbon::now('Asia/Manila');
        $reservation = RoomReservation::with('room')->where('reservation_id', $reservationId)->firstOrFail();

        // 🧺 Decode added amenities
        $addedAmenities = $reservation->room_added_amenities;
        if (!is_array($addedAmenities)) {
            $addedAmenities = json_decode($addedAmenities ?? '[]', true) ?? [];
        }
        $addedAmenityNames = array_map(fn($item) => $item['name'] ?? '', $addedAmenities);

        // 💳 Payment label
        $paymentLabel = match (strtolower($reservation->payment_status ?? '')) {
            'paid' => 'Paid',
            'downpayment' => 'Unpaid (Partial)',
            default => 'Unpaid',
        };

        // 💰 Decode downpayment
        $downpayment = $reservation->downpayment;
        if (is_string($downpayment)) {
            $downpayment = json_decode($downpayment, true) ?? [];
        }
        $downpaymentAmount = $downpayment['amount'] ?? 0;

        // 🚪 Room status & availability
        $roomStatus = $reservation->room->room_status ?? 'Unknown';
        $roomReservationId = $reservation->room->reservation_id ?? null;
        $unavailableStatuses = ['Booked', 'Checked In', 'Out of Service'];
        $isUnavailable = in_array($roomStatus, $unavailableStatuses) && $roomReservationId !== $reservation->reservation_id;

        // ✅ Other optional variables (mirroring Check-In)
        $receipts = [];         // placeholder if you link receipts to reservation later
        $isCheckinToday = false; // keep same naming convention for consistency
        $alertMessage = null;    // optional alerts
        $alertType = null;
        $extraData = null;

        // ✅ Return same structure & naming as Check-In
        return view('manager_hotel.hotelBookingDetailsPage', compact(
            'reservation',
            'receipts',
            'addedAmenityNames',
            'downpaymentAmount',
            'paymentLabel',
            'isCheckinToday',
            'isUnavailable',
            'roomStatus',
            'alertMessage',
            'alertType',
            'extraData'
        ));
    }

    public function saveAdditions(Request $request)
    {
        $reservation = RoomReservation::where('reservation_id', $request->reservation_id)->firstOrFail();

        $now = Carbon::now('Asia/Manila');

        /**
         * 🧺 STEP 1: Decode structured JSON from modal
         * The modal sends `amenities_json` and `extras_json`
         * Example:
         *  [{"name":"Extra Bed","price":300},{"name":"Breakfast Buffet","price":200}]
         *  [{"name":"Early Check-In","amount":1513,"qty":4,"total":6052}]
         */
        $newAmenities = json_decode($request->input('amenities_json', '[]'), true) ?? [];
        $newExtras = json_decode($request->input('extras_json', '[]'), true) ?? [];

        /**
         * 🧾 STEP 2: Retrieve existing data
         * (Already cast to array in Eloquent)
         */
        $existingAmenities = $reservation->room_added_amenities ?? [];
        $existingExtras = $reservation->extras ?? [];

        // Make sure they're arrays (in case null or object)
        if (!is_array($existingAmenities))
            $existingAmenities = [];
        if (!is_array($existingExtras))
            $existingExtras = [];

        /**
         * 🔗 STEP 3: Append new items, avoiding duplicates by name
         */
        $mergedAmenities = collect($existingAmenities)
            ->merge($newAmenities)
            ->unique('name')
            ->values()
            ->toArray();

        $mergedExtras = collect($existingExtras)
            ->merge($newExtras)
            ->unique('name')
            ->values()
            ->toArray();

        /**
         * 💰 STEP 4: Recalculate totals safely
         */
        $amenitiesCharge = collect($mergedAmenities)->sum(fn($a) => max(0, $a['price'] ?? 0));
        $extraCharge = collect($mergedExtras)->sum(fn($e) => max(0, $e['total'] ?? 0));
        $roomCharge = max(0, $reservation->room_charge ?? 0);
        $transportCharge = max(0, $reservation->transport_charge ?? 0);

        $totalAmount = max(0, $roomCharge + $amenitiesCharge + $extraCharge + $transportCharge);
        $netAmount = max(0, $totalAmount);

        /**
         * 💳 STEP 5: Auto update payment status
         */
        $paymentStatus = $netAmount > 0 ? 'Not Full Paid' : 'Paid';

        /**
         * 🧾 STEP 6: Update Reservation
         */
        $reservation->update([
            'room_added_amenities' => $mergedAmenities,
            'extras' => $mergedExtras,
            'amenities_charge' => $amenitiesCharge,
            'extra_charge' => $extraCharge,
            'total_amount' => $totalAmount,
            'net_amount' => $netAmount,
            'payment_status' => $paymentStatus,
            'updated_at' => $now,
        ]);

        /**
         * ✅ STEP 7: Return response
         */
        return back()->with('alert', [
            'type' => 'success',
            'title' => 'Additions Updated',
            'message' => 'Amenities and extras were successfully added, and totals updated.',
        ]);
    }

    public function processPayment(Request $request)
    {
        $reservation = RoomReservation::where('reservation_id', $request->reservation_id)->firstOrFail();

        $amount = (float) $request->input('payment_amount', 0);
        $totalDue = $reservation->net_amount ?? 0;

        $newBalance = max($totalDue - $amount, 0);
        $paymentStatus = $newBalance <= 0 ? 'Paid' : 'Not Full Paid';

        $reservation->update([
            'amount_tendered' => $amount,
            'change_due' => max(0, $amount - $totalDue),
            'payment_method' => $request->input('payment_method'),
            'payment_status' => $paymentStatus,
            'net_amount' => $newBalance,
            'updated_at' => now('Asia/Manila'),
        ]);

        return back()->with('alert', [
            'type' => 'success',
            'title' => 'Payment Recorded',
            'message' => 'Payment has been processed successfully.',
        ]);
    }

    public function roomsIndex()
    {
        $rooms = RoomInfo::with('reservations')->get();

        $rooms->transform(function ($room) {
            $reservations = $room->reservations ?? collect();

            $current = $reservations
                ->whereIn('status', ['Checked In', 'Booked'])
                ->where('checkin_date', '<=', now())
                ->where('checkout_date', '>=', now())
                ->first();

            $next = $reservations
                ->where('checkin_date', '>', now())
                ->sortBy('checkin_date')
                ->first();

            $room->current_reservation = $current;
            $room->next_reservation = $next;
            return $room;
        });

        return view('manager_hotel.hotelRooms', compact('rooms'));
    }

    public function updateRoomStatus(Request $request, $room_number)
    {
        $room = RoomInfo::where('room_number', $room_number)->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => "Room {$room_number} not found."
            ], 404);
        }

        $validated = $request->validate([
            'room_status' => 'required|string'
        ]);

        $room->room_status = $validated['room_status'];
        $room->save();

        return response()->json([
            'success' => true,
            'message' => "Room {$room_number} marked as {$validated['room_status']}."
        ]);
    }

    public function reportsPage(\Illuminate\Http\Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $reservations = RoomReservation::query();
        if ($from) $reservations->whereDate('created_at', '>=', $from);
        if ($to) $reservations->whereDate('created_at', '<=', $to);

        $summary = [
            'booked' => (clone $reservations)->where('reservation_status', 'Booked')->count(),
            'checked_in' => (clone $reservations)->where('reservation_status', 'Checked In')->count(),
            'checked_out' => (clone $reservations)->where('reservation_status', 'Checked Out')->count(),
            'cancelled' => (clone $reservations)->where('reservation_status', 'Cancelled')->count(),
        ];

        $hotelReceipts = \App\Models\Receipt::query()->where('type', 'Hotel');
        if ($from) $hotelReceipts->whereDate('issued_at', '>=', $from);
        if ($to) $hotelReceipts->whereDate('issued_at', '<=', $to);
        $totals = [
            'receipts' => (clone $hotelReceipts)->count(),
            'revenue' => (clone $hotelReceipts)->sum('total'),
        ];

        $filters = ['from' => $from, 'to' => $to];
        return view('manager_hotel.hotelReports', compact('summary', 'totals', 'filters'));
    }
    public function roomsEdit(string $room_number)
    {
        $room = RoomInfo::where('room_number', $room_number)->firstOrFail()->load('amenities');
        $amenities = Amenity::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('manager_hotel.hotelRoomsEdit', compact('room', 'amenities'));
    }

    public function roomsUpdate(Request $request, string $room_number)
    {
        $room = RoomInfo::where('room_number', $room_number)->firstOrFail();

        $validated = $request->validate([
            'room_type' => 'required|string|in:Standard,Matrimonial,Fammily Room',
            'room_floor' => 'required|integer|min:0',
            'room_description' => 'nullable|string',
            'room_status' => 'required|string|in:Vacant,Checked In,Checked Out,Maintenance,Booked,Out of Service',
            'bed_type_type' => 'required|string',
            'bed_type_quantity' => 'required|integer|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'room_amenities' => 'nullable|string', // legacy comma-separated (optional)
            'amenity_ids' => 'nullable|array',
            'amenity_ids.*' => 'integer|exists:room_amenities_extras,id',
            'room_rate' => 'required|integer|min:0',
        ]);

        $bedType = [
            'type' => $validated['bed_type_type'],
            'quantity' => (int) $validated['bed_type_quantity'],
        ];

        // Derive amenity names: prefer amenity_ids (pivot), fallback to legacy CSV
        $selectedAmenityNames = [];
        if (!empty($validated['amenity_ids'])) {
            $selectedAmenityNames = Amenity::whereIn('id', $validated['amenity_ids'])->pluck('name')->all();
        } else {
            $selectedAmenityNames = array_values(array_filter(array_map('trim', explode(',', (string) ($validated['room_amenities'] ?? '')))));
        }

        $room->update([
            'room_type' => $validated['room_type'],
            'room_floor' => $validated['room_floor'],
            'room_description' => $validated['room_description'] ?? null,
            'room_status' => $validated['room_status'],
            'bed_type' => $bedType,
            'max_occupancy' => $validated['max_occupancy'],
            'room_amenities' => $selectedAmenityNames,
            'room_rate' => $validated['room_rate'],
        ]);

        // Sync pivot when amenity_ids provided
        if ($request->filled('amenity_ids')) {
            $room->amenities()->sync($validated['amenity_ids']);
        }

        return redirect()
            ->route('hotelmanager.rooms')
            ->with('success', 'Room information updated successfully.');
    }
}
