<?php

namespace App\Http\Controllers;

use App\Models\RestoMenu;
use App\Models\RestoOrder;
use App\Models\Receipt;
use App\Models\LogsReport;
use App\Models\LogsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    // Show the POS menu with items from DB grouped by category

    public function menu()
    {
        $menu = RestoMenu::query()
            ->select(['id', 'menu_id', 'name', 'description', 'price', 'category', 'is_available', 'image_url', 'stock_quantity'])
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category')
            ->map(function ($items) {
                return $items->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'menu_id' => $m->menu_id,
                        'name' => $m->name,
                        'description' => $m->description,
                        'price' => (float) $m->price,
                        'category' => $m->category,
                        'is_available' => (bool) $m->is_available,
                        'image' => $m->image_url,
                        'stock' => (int) $m->stock_quantity,
                    ];
                })->values();
            })
            ->toArray();

        return view('cashier.cashierMenu', compact('menu'));
    }

    public function nextIds()
    {
        $today = now('Asia/Manila')->toDateString();
        $latestNumber = DB::table('resto_orders')
            ->whereDate('ordered_at', $today)
            ->max('daily_order_number');
        $nextDaily = $latestNumber ? $latestNumber + 1 : 1;

        $nextOrderId = 'OR-' . now('Asia/Manila')->format('Ymd') . '-' . str_pad($nextDaily, 4, '0', STR_PAD_LEFT);
        $nextTransactionId = 'TXN-' . now('Asia/Manila')->format('YmdHis') . '-' . strtoupper(Str::random(5));

        return response()->json([
            'order_id' => $nextOrderId,
            'transaction_id' => $nextTransactionId,
            'daily_order_number' => str_pad($nextDaily, 4, '0', STR_PAD_LEFT),
        ]);
    }

    // Store POS order securely
    public function orderStore(Request $request)
    {
        $request->validate([
            'payload' => 'required|string',
        ]);

        $data = json_decode($request->input('payload'), true);
        if (!is_array($data)) {
            return back()->with('error', 'Invalid submission data.');
        }

        $items = $data['items'] ?? [];
        $paymentStatus = $data['payment_status'] ?? ($request->input('payment_status') ?? 'Paid');
        $providedTxn = isset($data['transaction_id']) ? (string) $data['transaction_id'] : null;
        $method = $data['payment_method'] ?? ($request->input('payment_method') ?? 'Cash');

        if (!is_array($items) || count($items) < 1) {
            return back()->with('error', 'Order is empty. Please add at least one item.');
        }

        // Normalize to [ id => qty ]
        $normalized = [];
        foreach ($items as $it) {
            $id = (int) ($it['id'] ?? 0);
            $qty = (int) ($it['qty'] ?? 0);
            if ($id > 0 && $qty > 0) {
                $normalized[$id] = ($normalized[$id] ?? 0) + $qty;
            }
        }
        if (empty($normalized)) {
            return back()->with('error', 'Order is empty. Please add at least one item.');
        }

        $menus = RestoMenu::whereIn('id', array_keys($normalized))
            ->get(['id', 'menu_id', 'name', 'price', 'category', 'is_available'])
            ->keyBy('id');

        $subtotal = 0.0;
        $byCategory = [
            'main_dish' => [],
            'dessert' => [],
            'drinks' => [],
            'rice' => [],
            'appetizer' => [],
            'combo' => [],
        ];

        foreach ($normalized as $id => $qty) {
            $menu = $menus->get($id);
            if (!$menu) {
                return back()->with('error', 'One or more items are no longer available.');
            }
            if (!$menu->is_available) {
                return back()->with('error', "Item '{$menu->name}' is currently unavailable.");
            }
            $line = [
                'menu_id' => $menu->menu_id,
                'name' => $menu->name,
                'price' => (float) $menu->price,
                'qty' => $qty,
                'subtotal' => round((float) $menu->price * $qty, 2),
            ];
            $subtotal += $line['subtotal'];

            $cat = strtolower((string) $menu->category);
            $key = match ($cat) {
                'main dish', 'main_dish', 'main' => 'main_dish',
                'dessert', 'desserts' => 'dessert',
                'drink', 'drinks', 'beverage' => 'drinks',
                'rice' => 'rice',
                'appetizer', 'appetizers' => 'appetizer',
                'combo', 'combos' => 'combo',
                default => 'main_dish',
            };
            $byCategory[$key][] = $line;
        }

        $total = round($subtotal, 2);
        $totalStr = number_format($total, 2, '.', '');

        $admin = Auth::user();

        DB::beginTransaction();
        try {
            $order = new RestoOrder();
            $order->admin_id = $admin?->id ?? 0;
            $order->cashier_name = $admin?->name;
            $order->order_type = 'Dine In';
            // Do not set non-existent columns on orders; keep payment in Receipt only
            $method = in_array($method, ['Cash', 'Card', 'E-Wallet', 'Mixed']) ? $method : 'Cash';

            // Server-side validation per method
            $amountTendered = null;
            $changeDue = null;
            $cardReference = null;
            $cardAmount = null;
            $ewalletProvider = null;
            $ewalletReference = null;
            $ewalletAmount = null;
            $mixedProvider = null;
            $mixedReference = null;
            $mixedDigital = null;
            $mixedCash = null;

            if ($method === 'Cash') {
                $amt = (float) ($data['amount_tendered'] ?? 0);
                if ($amt <= 0 || $amt < $total) {
                    return back()->with('error', 'Insufficient cash amount tendered.');
                }
                $amountTendered = round($amt, 2);
                $changeDue = round(max(0, $amt - $total), 2);
            } elseif ($method === 'Card') {
                $ref = trim((string) ($data['card_reference'] ?? ''));
                $camt = (float) ($data['card_amount'] ?? 0);
                if ($ref === '' || $camt <= 0) {
                    return back()->with('error', 'Card reference and amount are required.');
                }
                $cardReference = $ref;
                $cardAmount = round($camt, 2);
                if ($camt < $total) {
                    return back()->with('error', 'Card amount must cover total.');
                }
            } elseif ($method === 'E-Wallet') {
                $prov = trim((string) ($data['ewallet_provider'] ?? ''));
                $eref = trim((string) ($data['ewallet_reference'] ?? ''));
                $eamt = (float) ($data['ewallet_amount'] ?? 0);
                if ($prov === '' || $eref === '' || $eamt <= 0) {
                    return back()->with('error', 'E-wallet provider, reference, and amount are required.');
                }
                $ewalletProvider = $prov;
                $ewalletReference = $eref;
                $ewalletAmount = round($eamt, 2);
                if ($eamt < $total) {
                    return back()->with('error', 'E-wallet amount must cover total.');
                }
            } else { // Mixed
                $cash = (float) ($data['amount_tendered'] ?? 0);
                $prov = trim((string) ($data['mixed_provider'] ?? ''));
                $ref = trim((string) ($data['mixed_reference'] ?? ''));
                $damt = (float) ($data['mixed_digital'] ?? 0);
                if ($prov === '' || $ref === '' || $damt <= 0 || $cash < 0) {
                    return back()->with('error', 'Mixed payment requires provider, reference, and positive digital amount.');
                }
                if ($cash + $damt < $total) {
                    return back()->with('error', 'Combined cash + digital must cover total.');
                }
                $mixedCash = round($cash, 2);
                $mixedProvider = $prov;
                $mixedReference = $ref;
                $mixedDigital = round($damt, 2);
                $changeDue = round(max(0, $cash + $damt - $total), 2);
            }

            foreach ($byCategory as $col => $val) {
                if (!empty($val)) {
                    $order->{$col} = $val;
                }
            }

            $order->total_amount = $totalStr;
            $order->status = 'Pending';

            // Decrement stock immediately upon saving the order
            foreach ($normalized as $id => $qty) {
                $menu = RestoMenu::where('id', $id)->lockForUpdate()->first();
                if ($menu) {
                    $menu->stock_quantity = max(0, (int) $menu->stock_quantity - (int) $qty);
                    $menu->save();
                }
            }

            $order->save();

            // Log report entry (payment/receipt) and write action log
            $admin_id = optional($admin)->admin_id ?? (optional($admin)->id ? ('ADM-' . optional($admin)->id) : null);
            $cashPart = 0.0;
            $cardPart = 0.0;
            if ($method === 'Cash') {
                $cashPart = (float) $total;
            } elseif ($method === 'Card' || $method === 'E-Wallet') {
                $cardPart = (float) $total;
            } else {
                $cashPart = (float) ($data['amount_tendered'] ?? 0);
                $cardPart = (float) ($data['mixed_digital'] ?? 0);
            }
            LogsReport::create([
                'admin_id' => $admin_id,
                'admin_name' => optional($admin)->name,
                'role' => (string) ($admin->role ?? 'Restaurant Cashier'),
                'type' => 'Restaurant',
                'report_type' => 'Receipt',
                'reference_id' => $nextOrderId ?? null,
                'amount' => (float) $total,
                'payment_method' => $method,
                'transaction_status' => 'Paid',
                'description' => 'POS order created',
                'ip_address' => $request->ip(),
                'device' => 'Web',
                'browser' => (string) $request->header('User-Agent'),
                'logged_at' => now('Asia/Manila'),
            ]);

            // Create receipt
            $receiptItems = [];
            foreach ($byCategory as $list) {
                foreach ($list as $ln) {
                    $receiptItems[] = [
                        'menu_id' => $ln['menu_id'],
                        'name' => $ln['name'],
                        'price' => (float) $ln['price'],
                        'qty' => (int) $ln['qty'],
                        'subtotal' => (float) $ln['subtotal'],
                    ];
                }
            }

            $paymentDetails = null;
            if ($method === 'Cash') {
                $paymentDetails = [
                    'method' => 'Cash',
                    'amount' => (float) ($amountTendered ?? $total),
                    'change_due' => (float) ($changeDue ?? 0),
                ];
            } elseif ($method === 'Card') {
                $paymentDetails = [
                    'method' => 'Card',
                    'reference_number' => (string) ($cardReference ?? ''),
                    'amount' => (float) ($cardAmount ?? $total),
                ];
            } elseif ($method === 'E-Wallet') {
                $paymentDetails = [
                    'method' => 'E-Wallet',
                    'provider' => (string) ($ewalletProvider ?? ''),
                    'reference_number' => (string) ($ewalletReference ?? ''),
                    'amount' => (float) ($ewalletAmount ?? $total),
                ];
            } else { // Mixed
                $paymentDetails = [
                    'method' => 'Mixed',
                    'provider' => (string) ($mixedProvider ?? ''),
                    'reference_number' => (string) ($mixedReference ?? ''),
                    'cash_amount' => (float) ($mixedCash ?? 0),
                    'digital_amount' => (float) ($mixedDigital ?? 0),
                ];
            }

            Receipt::create([
                'reference_id' => $order->order_id,
                'type' => 'Restaurant',
                'customer_id' => null,
                'customer_name' => null,
                'email' => null,
                'phone' => null,
                'items' => $receiptItems,
                'subtotal' => $total,
                'total' => $total,
                'amount' => $total,
                'amount_tendered' => (float) ($amountTendered ?? $cardAmount ?? $ewalletAmount ?? $mixedCash ?? $total),
                'change_due' => (float) ($changeDue ?? 0),
                'payment_method' => $method,
                'payment_details' => $paymentDetails,
                'issued_by' => optional($admin)->name,
                'admin_id' => optional($admin)->id,
                'issued_at' => now('Asia/Manila'),
            ]);

            $this->logAction($request, 'Create Order', $order->order_id, 'Created order from POS');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save order. Please try again.');
        }

        return redirect()->route('cashier.menu')->with('success', 'Order saved successfully.');
    }

    // Show orders grouped by status
    public function orders()
    {
        $admin = Auth::user();
        $pending = \App\Models\RestoOrder::where('cashier_name', $admin->name)
            ->where('status', 'Pending')->orderByDesc('ordered_at')->limit(50)->get();
        $preparing = \App\Models\RestoOrder::where('cashier_name', $admin->name)
            ->where('status', 'Preparing')->orderByDesc('ordered_at')->limit(50)->get();
        $served = \App\Models\RestoOrder::where('cashier_name', $admin->name)
            ->where('status', 'Served')->orderByDesc('ordered_at')->limit(50)->get();
        $cancelled = \App\Models\RestoOrder::where('cashier_name', $admin->name)
            ->where('status', 'Cancelled')->orderByDesc('ordered_at')->limit(50)->get();

        return view('cashier.cashierOrders', compact('pending', 'preparing', 'served', 'cancelled'));
    }

    // Transition: Pending -> Preparing
    public function startPreparing(Request $request, \App\Models\RestoOrder $order)
    {
        if ($order->status !== 'Pending') {
            return back()->with('error', 'Only pending orders can be started.');
        }
        $order->status = 'Preparing';
        $order->save();
        $this->logAction($request, 'Start Preparing', $order->order_id, 'Order moved to preparing');
        return back()->with('success', 'Order moved to Preparing.');
    }

    // Transition: Preparing -> Served (stock already adjusted on save)
    public function markServed(Request $request, \App\Models\RestoOrder $order)
    {
        if (!in_array($order->status, ['Preparing', 'Pending'])) {
            return back()->with('error', 'Only preparing or pending orders can be served.');
        }

        $order->status = 'Served';
        $order->save();
        $this->logAction($request, 'Mark Served', $order->order_id, 'Order marked as served');
        return back()->with('success', 'Order marked as Served.');
    }

    // Cancel order (only from Pending)
    public function cancel(Request $request, \App\Models\RestoOrder $order)
    {
        if ($order->status !== 'Pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }
        DB::beginTransaction();
        try {
            // Return stock quantities to menu items
            foreach ($this->extractItems($order) as $line) {
                $menuId = $line['menu_id'] ?? null;
                $qty = (int) ($line['qty'] ?? 0);
                if (!$menuId || $qty <= 0)
                    continue;
                $menu = RestoMenu::where('menu_id', $menuId)->lockForUpdate()->first();
                if ($menu) {
                    $menu->stock_quantity = (int) $menu->stock_quantity + $qty;
                    $menu->save();
                }
            }

            $order->status = 'Cancelled';
            $order->cancelled_at = now('Asia/Manila');
            $order->save();

            // Update daily report: treat as refund of total
            $admin = Auth::user();
            $admin_id = optional($admin)->admin_id ?? (optional($admin)->id ? ('ADM-' . optional($admin)->id) : null);
            $this->updateDailyReport(
                $admin_id,
                optional($admin)->name,
                now('Asia/Manila')->toDateString(),
                0,
                0.0,
                0.0,
                0.0,
                (float) ($order->total_amount ?? 0),
                0.0,
                0.0 - (float) ($order->total_amount ?? 0),
            );

            $this->logAction($request, 'Cancel Order', $order->order_id, 'Order cancelled');
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order. Try again.');
        }
        return back()->with('success', 'Order cancelled and stock returned.');
    }

    // Helper: flatten all per-category JSON items
    private function extractItems(\App\Models\RestoOrder $order): array
    {
        $cats = ['main_dish', 'dessert', 'drinks', 'rice', 'appetizer', 'combo'];
        $out = [];
        foreach ($cats as $c) {
            $arr = $order->{$c};
            if (is_array($arr)) {
                foreach ($arr as $line) {
                    if (is_array($line))
                        $out[] = $line;
                }
            }
        }
        return $out;
    }

    private function updateDailyReport(?string $admin_id, ?string $cashierName, string $reportDate, int $deltaOrders, float $deltaSales, float $deltaCash, float $deltaCard, float $deltaRefund, float $deltaDiscount, float $deltaNet): void
    {
        // Legacy aggregation removed. Record a summary row into logs_reports instead.
        LogsReport::create([
            'admin_id' => $admin_id,
            'admin_name' => $cashierName ?? 'Unknown',
            'role' => 'Restaurant Cashier',
            'type' => 'Restaurant',
            'report_type' => 'Adjustment',
            'reference_id' => null,
            'amount' => (float)$deltaNet,
            'payment_method' => null,
            'transaction_status' => 'Aggregated',
            'description' => sprintf('Delta orders:%d sales:%.2f cash:%.2f card:%.2f refund:%.2f discount:%.2f net:%.2f on %s', $deltaOrders, $deltaSales, $deltaCash, $deltaCard, $deltaRefund, $deltaDiscount, $deltaNet, $reportDate),
            'logged_at' => now('Asia/Manila'),
        ]);
    }

    private function logAction(Request $request, string $action, ?string $referenceId = null, ?string $description = null): void
    {
        $admin = Auth::user();
        LogsAdmin::create([
            'admin_id' => optional($admin)->admin_id ?? (optional($admin)->id ? ('ADM-' . optional($admin)->id) : null),
            'admin_name' => optional($admin)->name ?? 'Unknown',
            'role' => (string) ($admin->role ?? 'Restaurant Cashier'),
            'type' => 'Restaurant',
            'action_type' => $action,
            'reference_id' => $referenceId,
            'description' => $description,
            'log_type' => 'Activity',
            'ip_address' => $request->ip(),
            'device' => $request->header('Sec-CH-UA-Platform') ?? 'web',
            'browser' => $request->header('User-Agent'),
            'logged_at' => now('Asia/Manila'),
        ]);
    }

    // Simple reports: KPIs + recent orders, grouped counts
    public function reports(Request $request)
    {
        $admin = Auth::user();
        $from = $request->query('from');
        $to = $request->query('to');
        $cashier = trim((string) $request->query('cashier', ''));

        $tzNow = now('Asia/Manila');
        $today = $tzNow->toDateString();

        // Build KPIs from logs_reports (Restaurant)
        $todaySales = (float) LogsReport::where('type', 'Restaurant')->whereDate('logged_at', $today)
            ->whereIn('report_type', ['Receipt','Payment'])->sum('amount');
        $todayRefund = (float) LogsReport::where('type', 'Restaurant')->whereDate('logged_at', $today)
            ->where('report_type', 'Refund')->sum('amount');
        $monthSales = (float) LogsReport::where('type', 'Restaurant')
            ->whereYear('logged_at', $tzNow->year)->whereMonth('logged_at', $tzNow->month)
            ->whereIn('report_type', ['Receipt','Payment'])->sum('amount');

        $kpi = [
            'today_orders' => 0,
            'today_sales' => $todaySales,
            'today_cash' => 0,
            'today_card' => 0,
            'today_refund' => $todayRefund,
            'today_discount' => 0,
            'today_net' => $todaySales - $todayRefund,
            'month_orders' => 0,
            'month_sales' => $monthSales,
        ];

        $reportsQuery = LogsReport::query()->where('type', 'Restaurant')
            ->where('admin_name', $admin->name);
        if ($from) {
            $reportsQuery->whereDate('logged_at', '>=', $from);
        }
        if ($to) {
            $reportsQuery->whereDate('logged_at', '<=', $to);
        }
        $recent = $reportsQuery->orderByDesc('logged_at')->orderByDesc('created_at')->limit(100)->get();

        // Orders list (filtered): show only amount per order for current cashier
        $ordersQuery = RestoOrder::query()->where('cashier_name', $admin->name);
        if ($from) {
            $ordersQuery->whereDate('ordered_at', '>=', $from);
        }
        if ($to) {
            $ordersQuery->whereDate('ordered_at', '<=', $to);
        }
        if ($cashier !== '') {
            $ordersQuery->where('cashier_name', 'like', "%$cashier%");
        }
        // Use correct column name and alias for blade compatibility
        $orders = $ordersQuery
            ->select(['order_id', DB::raw('total_amount as total'), 'ordered_at'])
            ->orderByDesc('ordered_at')
            ->limit(200)
            ->get();

        $filters = ['from' => $from, 'to' => $to, 'cashier' => $cashier];
        return view('cashier.cashierReports', compact('kpi', 'recent', 'orders', 'filters'));
    }

    // Analytics dashboard with charts and receipts
    public function reportDashboard(Request $request)
    {
        $admin = Auth::user();
        $from = $request->query('from');
        $to = $request->query('to');

        $tz = 'Asia/Manila';
        $now = now($tz);

        // Default to last 14 days if no filter
        $startDate = $from ? \Carbon\Carbon::parse($from, $tz) : $now->copy()->subDays(13)->startOfDay();
        $endDate = $to ? \Carbon\Carbon::parse($to, $tz)->endOfDay() : $now->copy()->endOfDay();

        // KPIs from logs_reports (Restaurant)
        $today = $now->toDateString();
        $todaySales = (float) LogsReport::where('type', 'Restaurant')->whereDate('logged_at', $today)
            ->whereIn('report_type', ['Receipt','Payment'])->sum('amount');
        $todayRefund = (float) LogsReport::where('type', 'Restaurant')->whereDate('logged_at', $today)
            ->where('report_type', 'Refund')->sum('amount');
        $kpi = [
            'today_orders' => 0,
            'today_sales' => $todaySales,
            'today_cash' => 0,
            'today_card' => 0,
            'today_refund' => $todayRefund,
            'today_discount' => 0,
            'today_net' => $todaySales - $todayRefund,
        ];

        // Sales over time (last 14 days or filter range)
        $labels = [];
        $values = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $labels[] = $cursor->format('M d');
            $sum = RestoOrder::query()
                ->where('cashier_name', $admin->name)
                ->whereDate('ordered_at', $cursor->toDateString())
                ->sum('total_amount');
            $values[] = (float) $sum;
            $cursor->addDay();
        }
        $salesSeries = ['labels' => $labels, 'values' => $values];

        // Payment method breakdown from receipts (Restaurant only)
        $paymentRows = \App\Models\Receipt::query()
            ->where('type', 'Restaurant')
            ->where(function ($q) use ($admin) {
                $q->where('admin_id', optional($admin)->id)
                    ->orWhere('issued_by', optional($admin)->name);
            })
            ->whereBetween('issued_at', [$startDate, $endDate])
            ->selectRaw('payment_method, SUM(total) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();
        $paymentBreakdown = [
            'labels' => array_keys($paymentRows),
            'values' => array_map('floatval', array_values($paymentRows)),
        ];

        // Recent orders and receipts for current cashier
        $orders = RestoOrder::query()
            ->where('cashier_name', $admin->name)
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->orderByDesc('ordered_at')
            ->limit(25)
            ->get(['order_id', 'total_amount', 'status', 'ordered_at']);

        $receipts = \App\Models\Receipt::query()
            ->where('type', 'Restaurant')
            ->where(function ($q) use ($admin) {
                $q->where('admin_id', optional($admin)->id)
                    ->orWhere('issued_by', optional($admin)->name);
            })
            ->whereBetween('issued_at', [$startDate, $endDate])
            ->orderByDesc('issued_at')
            ->limit(25)
            ->get(['receipt_id', 'total', 'payment_method', 'issued_at']);

        $filters = ['from' => $from, 'to' => $to];
        return view('cashier.cashierReport', compact('kpi', 'salesSeries', 'paymentBreakdown', 'orders', 'receipts', 'filters'));
    }

    // Logs: recent order events (coarse)
    public function logs(Request $request)
    {
        $admin = Auth::user();
        $from = $request->query('from');
        $to = $request->query('to');
        $cashier = trim((string) $request->query('cashier', ''));
        $action = trim((string) $request->query('action', ''));

        $q = LogsAdmin::query()->where('admin_name', $admin->name)->where('type', 'Restaurant');
        if ($from) {
            $q->whereDate('logged_at', '>=', $from);
        }
        if ($to) {
            $q->whereDate('logged_at', '<=', $to);
        }
        if ($cashier !== '') {
            $q->where('cashier_name', 'like', "%$cashier%");
        }
        if ($action !== '') {
            $q->where('action_type', 'like', "%$action%");
        }

        $logs = $q->orderByDesc('logged_at')->orderByDesc('created_at')->limit(200)->get();
        $filters = ['from' => $from, 'to' => $to, 'cashier' => $cashier, 'action' => $action];
        return view('cashier.cashierLogs', compact('logs', 'filters'));
    }
}
