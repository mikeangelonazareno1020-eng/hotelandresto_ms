<?php

use App\Http\Controllers\AdministratorGpsController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ManagerRestoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\FrontdeskController;
use App\Http\Middleware\CashierActivityLogger;
use App\Http\Controllers\ManagerHotelController;
use App\Http\Controllers\ManagerHotelAmenitiesController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\AdministratorHotelController;
use App\Http\Controllers\AdministratorStaffController;

// ============================
// 🏠 PUBLIC ROUTES
// ============================

Route::get('/', fn() => view('auth.login'))->name('landing');
Route::get('/loginform', [AuthenticationController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
Route::view('/expired', 'errors.419')->name('expired');
Route::get('/orders/next-ids', [CashierController::class, 'nextIds'])->name('orders.nextIds');

// ============================
// 🧩 PROTECTED ROUTES (Admin Guard)
// ============================

Route::middleware(['auth:admin', 'prevent-back-history'])->group(function () {

    // ============================
    // Super Admin
    // ============================

    Route::middleware(['role:Super Admin'])->group(function () {
        Route::prefix('super')->group(function () {
            Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super.dashboard');
            Route::get('/admin-accounts', [SuperAdminController::class, 'accounts'])->name('super.accounts');
            Route::post('/admin-accounts', [SuperAdminController::class, 'accountStore'])->name('super.accounts.store');
            Route::put('/admin-accounts/{admin}', [SuperAdminController::class, 'accountUpdate'])->name('super.accounts.update');
            Route::delete('/admin-accounts/{admin}', [SuperAdminController::class, 'accountDestroy'])->name('super.accounts.destroy');
            Route::get('/customers', [SuperAdminController::class, 'customers'])->name('super.customers');
            Route::get('/gps', [SuperAdminController::class, 'gps'])->name('super.gps');
            Route::get('/logs', [SuperAdminController::class, 'logs'])->name('super.logs');
        });
    });

    // ============================
    // 🧑‍💻 ADMIN ROUTES
    // ============================

    Route::middleware(['role:Administrator'])->group(function () {

        Route::get('/admin/dashboard', [AdministratorController::class, 'dashboardIndex'])
            ->name(name: 'admin.dashboard');


        // ============================
        // 🧑‍💻 Hotel Side
        // ============================

        Route::prefix('hotel')->group(function () {
            Route::get(
                '/admin/booking',
                [AdministratorHotelController::class, 'bookingPage']
            )->name('admin.booking');

            Route::get(
                '/admin/booking/create',
                [AdministratorHotelController::class, 'bookingCreate']
            )->name('admin.booking.create');

            Route::post('/admin/booking/store', [AdministratorHotelController::class, 'bookingStore'])
                ->name('admin.booking.store');

            Route::get('/admin/booking/{reservation}/checkin', [AdministratorHotelController::class, 'checkinPage'])
                ->name('admin.booking.checkinpage');

            Route::post('/admin/booking/checkin', [AdministratorHotelController::class, 'bookingCheckIn'])
                ->name('admin.booking.checkin');

            Route::get('/admin/booking/{reservation}/checkout', [AdministratorHotelController::class, 'checkoutPage'])
                ->name('admin.booking.checkoutpage');

            Route::post('/admin/booking/checkout', [AdministratorHotelController::class, 'bookingCheckOut'])
                ->name('admin.booking.checkout');

            Route::get('/admin/booking/{reservation}/cancel', [AdministratorHotelController::class, 'cancelPage'])
                ->name('admin.booking.cancelpage');

            Route::post('/admin/booking/cancel-booking', [AdministratorHotelController::class, 'bookingCancel'])
                ->name('admin.booking.cancelbooking');

            Route::get('/admin/booking/{reservation}/details', [AdministratorHotelController::class, 'detailsPage'])
                ->name('admin.booking.detailspage');

            Route::post('/admin/booking/save-additions', [AdministratorHotelController::class, 'saveAdditions'])
                ->name('admin.booking.saveadditions');

            Route::post('/admin/booking/process-payment', [AdministratorHotelController::class, 'processPayment'])
                ->name('admin.booking.processPayment');

            Route::get(
                '/admin/rooms',
                [AdministratorHotelController::class, 'roomsIndex']
            )->name('admin.rooms');

            Route::post('/admin/rooms/{room_number}/status', [AdministratorHotelController::class, 'updateRoomStatus'])
                ->name('admin.rooms.updateStatus');
        });

        // ============================
        // 🧑‍💻 Staff Side
        // ============================

        Route::prefix('staff')->group(function () {
            Route::get('/admin/staffmanagement', [AdministratorStaffController::class, 'staffIndex'])
                ->name('admin.staff');

            Route::get('/admin/staff/add', function () {
                return view('admin.staff_side.adminStaffAdd');
            })->name('admin.staff.add');

            Route::post('/staff', [AdministratorStaffController::class, 'store'])
                ->name('admin.staff.store');

            // Address Selector
            Route::get('/api/regions', [AddressController::class, 'getRegions']);
            Route::get('/api/provinces/{regionCode}', [AddressController::class, 'getProvinces']);
            Route::get('/api/cities/{provinceCode}', [AddressController::class, 'getCities']);
            Route::get('/api/barangays/{cityCode}', [AddressController::class, 'getBarangays']);

            // Department-Role Selector
            Route::get('/api/roles/{department}', [RoleController::class, 'getRoles']);

            // Edit Staff
            Route::get('/staff/{id}/edit', [AdministratorStaffController::class, 'edit'])
                ->name('admin.staff.edit');
            Route::put('/staff/{id}', [AdministratorStaffController::class, 'update'])
                ->name('admin.staff.update');

            // Delete Staff
            Route::delete('/staff/{id}', [AdministratorStaffController::class, 'destroy'])
                ->name('admin.staff.destroy');
        });

        // ============================
        // 🧑‍💻 Gps Tracking
        // ============================

        Route::prefix('gps')->group(function () {
            Route::get('/admin/locations', [AdministratorGpsController::class, 'locations'])->name('admin.gps.locations');
            Route::get('/admin/services', [AdministratorGpsController::class, 'services'])->name('admin.gps.services');
            Route::get('/admin/services/create', [AdministratorGpsController::class, 'serviceCreate'])->name('admin.gps.services.create');
            Route::post('/admin/services', [AdministratorGpsController::class, 'serviceStore'])->name('admin.gps.services.store');
            Route::get('/admin/vehicles', [AdministratorGpsController::class, 'vehicles'])->name('admin.gps.vehicles');
            Route::get('/admin/trips', [AdministratorGpsController::class, 'trips'])->name('admin.gps.trips');
            Route::post('/admin/vehicles', [AdministratorGpsController::class, 'vehiclesStore'])->name('admin.gps.vehicles.store');
            Route::put('/admin/vehicles/{vehicle}', [AdministratorGpsController::class, 'vehiclesUpdate'])->name('admin.gps.vehicles.update');
            Route::delete('/admin/vehicles/{vehicle}', [AdministratorGpsController::class, 'vehiclesDestroy'])->name('admin.gps.vehicles.destroy');
            Route::post('/admin/vehicles/{vehicle}/save-path', [AdministratorGpsController::class, 'vehiclesSavePath'])->name('admin.gps.vehicles.savePath');
            Route::post('/admin/tracker/vehicles/{vehicle}/save-path', [AdministratorGpsController::class, 'trackerSavePath'])->name('admin.gps.tracker.vehicles.savePath');
        });

    });


    // ============================
    // 🏨 HOTEL MANAGER
    // ============================

    Route::middleware(['role:Hotel Manager'])->group(function () {

        Route::get('/hotel/dashboard', [ManagerHotelController::class, 'dashboardIndex'])
            ->name(name: 'hotelmanager.dashboard');

        Route::get(
            '/hotel/booking',
            [ManagerHotelController::class, 'bookingPage']
        )->name('hotelmanager.booking');

        // Online bookings listing
        Route::get(
            '/hotel/online-bookings',
            [ManagerHotelController::class, 'onlineBookingPage']
        )->name('hotelmanager.onlinebookings');

        Route::get(
            '/hotel/booking/create',
            [ManagerHotelController::class, 'bookingCreate']
        )->name('hotelmanager.booking.create');

        Route::post('/hotel/booking/store', [ManagerHotelController::class, 'bookingStore'])
            ->name('hotelmanager.booking.store');

        Route::get('/hotel/booking/{reservation}/checkin', [ManagerHotelController::class, 'checkinPage'])
            ->name('hotelmanager.booking.checkinpage');

        Route::post('/hotel/booking/checkin', [ManagerHotelController::class, 'bookingCheckIn'])
            ->name('hotelmanager.booking.checkin');

        Route::get('/hotel/booking/{reservation}/checkout', [ManagerHotelController::class, 'checkoutPage'])
            ->name('hotelmanager.booking.checkoutpage');

        Route::post('/hotel/booking/checkout', [ManagerHotelController::class, 'bookingCheckOut'])
            ->name('hotelmanager.booking.checkout');

        Route::get('/hotel/booking/{reservation}/cancel', [ManagerHotelController::class, 'cancelPage'])
            ->name('hotelmanager.booking.cancelpage');

        Route::post('/hotel/booking/cancel-booking', [ManagerHotelController::class, 'bookingCancel'])
            ->name('hotelmanager.booking.cancelbooking');

        Route::get('/hotel/booking/{reservation}/details', [ManagerHotelController::class, 'detailsPage'])
            ->name('hotelmanager.booking.detailspage');

        Route::post('/hotel/booking/save-additions', [ManagerHotelController::class, 'saveAdditions'])
            ->name('hotelmanager.booking.saveadditions');

        Route::post('/hotel/booking/process-payment', [ManagerHotelController::class, 'processPayment'])
            ->name('hotelmanager.booking.processPayment');

        Route::get(
            '/hotel/rooms',
            [ManagerHotelController::class, 'roomsIndex']
        )->name('hotelmanager.rooms');

        Route::post('/hotel/rooms/{room_number}/status', [ManagerHotelController::class, 'updateRoomStatus'])
            ->name('hotelmanager.rooms.updateStatus');

        // Room edit + update
        Route::get('/hotel/rooms/{room_number}/edit', [ManagerHotelController::class, 'roomsEdit'])
            ->name('hotelmanager.rooms.edit');
        Route::put('/hotel/rooms/{room_number}', [ManagerHotelController::class, 'roomsUpdate'])
            ->name('hotelmanager.rooms.update');

        // Amenities & Extras management
        Route::get('/hotel/amenities', [ManagerHotelAmenitiesController::class, 'index'])
            ->name('hotelmanager.amenities');
        Route::post('/hotel/amenities', [ManagerHotelAmenitiesController::class, 'store'])
            ->name('hotelmanager.amenities.store');
        Route::put('/hotel/amenities/{amenity}', [ManagerHotelAmenitiesController::class, 'update'])
            ->name('hotelmanager.amenities.update');
        Route::delete('/hotel/amenities/{amenity}', [ManagerHotelAmenitiesController::class, 'destroy'])
            ->name('hotelmanager.amenities.destroy');

        // Reports page (Hotel Manager)
        Route::get('/hotel/reports', [ManagerHotelController::class, 'reportsPage'])
            ->name('hotelmanager.reports');
    });



    // ============================
    // 💁‍♀️ FRONTDESK STAFF
    // ============================

    Route::middleware(['role:Hotel Frontdesk'])->group(function () {

        Route::get(
            '/frontdesk/booking',
            [FrontdeskController::class, 'bookingIndex']
        )->name('frontdesk.booking');

        Route::get(
            '/frontdesk/booking/create',
            [FrontdeskController::class, 'bookingCreate']
        )->name('frontdesk.booking.create');

        Route::post('/frontdesk/booking/store', [FrontdeskController::class, 'bookingStore'])
            ->name('frontdesk.booking.store');

        Route::get('/frontdesk/booking/{reservation}/checkin', [FrontdeskController::class, 'checkinPage'])
            ->name('frontdesk.booking.checkinpage');

        Route::post('/frontdesk/booking/checkin', [FrontdeskController::class, 'bookingCheckIn'])
            ->name('frontdesk.booking.checkin');

        Route::get('/frontdesk/booking/{reservation}/checkout', [FrontdeskController::class, 'checkoutPage'])
            ->name('frontdesk.booking.checkoutpage');

        Route::post('/frontdesk/booking/checkout', [FrontdeskController::class, 'bookingCheckOut'])
            ->name('frontdesk.booking.checkout');

        Route::get('/frontdesk/booking/{reservation}/cancel', [FrontdeskController::class, 'cancelPage'])
            ->name('frontdesk.booking.cancelpage');

        Route::post('/frontdesk/booking/cancel-booking', [FrontdeskController::class, 'bookingCancel'])
            ->name('frontdesk.booking.cancelbooking');

        Route::get('/frontdesk/booking/{reservation}/details', [FrontdeskController::class, 'detailsPage'])
            ->name('frontdesk.booking.detailspage');

        Route::post('/frontdesk/booking/save-additions', [FrontdeskController::class, 'saveAdditions'])
            ->name('frontdesk.booking.saveadditions');

        Route::post('/frontdesk/booking/process-payment', [FrontdeskController::class, 'processPayment'])
            ->name('frontdesk.booking.processPayment');

        Route::get(
            '/frontdesk/rooms',
            [FrontdeskController::class, 'roomsIndex']
        )->name('frontdesk.rooms');

        Route::post('/frontdesk/rooms/{room_number}/status', [ManagerHotelController::class, 'updateRoomStatus'])
            ->name('frontdesk.rooms.updateStatus');

        // Frontdesk Reports
        Route::get('/frontdesk/reports', [FrontdeskController::class, 'reports'])
            ->name('frontdesk.reports');

        // Frontdesk Transport Services (view + create)
        Route::get('/frontdesk/transport', [FrontdeskController::class, 'transportServices'])
            ->name('frontdesk.transport.index');
        Route::get('/frontdesk/transport/create', [FrontdeskController::class, 'transportServiceCreate'])
            ->name('frontdesk.transport.create');
        Route::post('/frontdesk/transport', [FrontdeskController::class, 'transportServiceStore'])
            ->name('frontdesk.transport.store');

        // Frontdesk: search reservations by ID (for transport form autocomplete)
        Route::get('/frontdesk/reservations/search', [FrontdeskController::class, 'searchReservations'])
            ->name('frontdesk.reservations.search');
    });



    // ============================
    // 👨‍🍳 RESTAURANT MANAGER
    // ============================

    Route::middleware(['role:Restaurant Manager'])->group(function () {

        Route::get('/resto/dashboard', [ManagerRestoController::class, 'dashboardIndex'])
            ->name('restomanager.dashboard');

        Route::get('/resto/products', [ManagerRestoController::class, 'products'])
            ->name('restomanager.products');

        Route::get('/resto/products/{menu}/edit', [ManagerRestoController::class, 'edit'])
            ->name('restomanager.products.edit');
        Route::put('/resto/products/{menu}', [ManagerRestoController::class, 'update'])
            ->name('restomanager.products.update');
        Route::delete('/resto/products/{menu}', [ManagerRestoController::class, 'destroy'])
            ->name('restomanager.products.destroy');

        Route::get('/resto/menu', [ManagerRestoController::class, 'menu'])
            ->name('restomanager.menu');

        Route::post('/resto/menu/{menu}/stock', [ManagerRestoController::class, 'addStock'])
            ->name('restomanager.menu.stock');
        Route::post('/resto/menu/{menu}/remove', [ManagerRestoController::class, 'removeStock'])
            ->name('restomanager.menu.remove');
        Route::post('/resto/menu/{menu}/toggle', [ManagerRestoController::class, 'toggleAvailability'])
            ->name('restomanager.menu.toggle');

        Route::get('/resto/reports', [ManagerRestoController::class, 'reports'])
            ->name('restomanager.reports');

        Route::get('/resto/logs', [ManagerRestoController::class, 'logs'])
            ->name('restomanager.logs');

    });



    // ============================
    // 💵 RESTAURANT CASHIER
    // ============================

    Route::middleware(['role:Restaurant Cashier', CashierActivityLogger::class])->group(function () {
        Route::get('/cashier', [CashierController::class, 'menu'])->name('cashier.menu');
        Route::post('/cashier', [CashierController::class, 'orderStore'])->name('cashier.order.store');

        Route::get('/cashier/orders', [CashierController::class, 'orders'])->name('cashier.orders');
        Route::post('/cashier/orders/{order}/start', [CashierController::class, 'startPreparing'])->name('cashier.orders.start');
        Route::post('/cashier/orders/{order}/serve', [CashierController::class, 'markServed'])->name('cashier.orders.serve');
        Route::post('/cashier/orders/{order}/cancel', [CashierController::class, 'cancel'])->name('cashier.orders.cancel');

        Route::get('/cashier/reports', [CashierController::class, 'reports'])->name('cashier.reports');
        Route::get('/cashier/report', [CashierController::class, 'reportDashboard'])->name('cashier.report');
        Route::get('/cashier/logs', [CashierController::class, 'logs'])->name('cashier.logs');
    });


    // ============================
    // 🚪 LOGOUT
    // ============================

    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
});
