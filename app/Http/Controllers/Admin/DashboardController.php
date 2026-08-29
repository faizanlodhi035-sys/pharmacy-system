<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\MedicineBatch;
use App\Models\Medicine;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Real Total Sales Revenue & Count
        $salesCount = Sale::count();
        $totalRevenue = (float) Sale::sum('total_amount');

        // Real Total Purchases (Check both Purchase & PurchaseInvoice)
        $purchasesFromInvoices = (float) PurchaseInvoice::sum('grand_total');
        $purchasesFromPurchases = (float) Purchase::sum('total_amount');
        $totalPurchases = max($purchasesFromInvoices, $purchasesFromPurchases);

        // Real Total Medicines
        $totalMedicines = Medicine::count();

        // Real Total Customers
        $totalCustomers = Customer::count();

        // Top Selling Medicines (Real DB Query)
        $topSelling = SaleItem::select('medicine_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('medicine')
            ->groupBy('medicine_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return (object)[
                    'name' => $item->medicine->name ?? 'Medicine',
                    'sold_qty' => (int) $item->total_qty,
                    'revenue' => (float) $item->total_revenue,
                ];
            });

        // Low Stock Medicines (Real DB Query)
        $lowStockList = Medicine::with(['batches'])
            ->get()
            ->map(function($medicine) {
                $stock = $medicine->batches->sum('quantity');
                return (object)[
                    'name' => $medicine->name,
                    'stock' => $stock,
                    'alert_qty' => $medicine->alert_quantity ?? 10,
                    'status' => $stock <= 0 ? 'Out of Stock' : 'Low'
                ];
            })
            ->filter(function($item) {
                return $item->stock <= $item->alert_qty;
            })
            ->take(5)
            ->values();

        // Recent Invoices (Real DB Query)
        $recentInvoices = Sale::with('customer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($s) {
                return (object)[
                    'invoice_number' => $s->invoice_number,
                    'customer_name' => $s->customer->name ?? 'Walk-in Customer',
                    'date' => $s->created_at ? $s->created_at->format('M d, Y') : date('M d, Y'),
                    'amount' => (float) $s->total_amount,
                    'status' => 'Paid'
                ];
            });

        // Expiry Alerts (Real DB Query)
        $expiryList = MedicineBatch::with('medicine')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', Carbon::now()->addDays(90))
            ->orderBy('expiry_date', 'asc')
            ->take(5)
            ->get()
            ->map(function($b) {
                $days = Carbon::today()->diffInDays(Carbon::parse($b->expiry_date), false);
                return (object)[
                    'name' => $b->medicine->name ?? 'Medicine',
                    'expiry_date' => Carbon::parse($b->expiry_date)->format('M d, Y'),
                    'days_left' => (int) $days,
                    'status' => $days <= 0 ? 'Expired' : ($days <= 30 ? 'Warning' : 'Normal')
                ];
            });

        // Real Sales Overview Chart data for past 7 days
        $salesChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailySum = Sale::whereDate('created_at', $date)->sum('total_amount');
            $salesChartData[] = (float) $dailySum;
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalPurchases',
            'totalMedicines',
            'totalCustomers',
            'topSelling',
            'lowStockList',
            'recentInvoices',
            'expiryList',
            'salesChartData'
        ));
    }

    public function expiryReport(Request $request)
    {
        return redirect()->route('reports.expiry');
    }
}