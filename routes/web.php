<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\MedicineController;
use App\Livewire\Pos\PosCounter;
use App\Livewire\Admin\AddMedicine;
use App\Livewire\Admin\BulkAddMedicine;
use App\Livewire\Admin\PurchaseCreate;
use App\Livewire\Admin\HoldInvoiceList;
use App\Http\Controllers\Admin\ReturnController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportController;

// ============================================================
// Public Routes
// ============================================================

Route::get('/', function () {
    return redirect('/dashboard');
});

// ============================================================
// Secure Admin Migration Routes
// ============================================================

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::get('/admin/migration', [\App\Http\Controllers\Admin\MigrationController::class, 'index'])
            ->name('admin.migration.index');

        Route::post('/admin/migration/dry-run', [\App\Http\Controllers\Admin\MigrationController::class, 'dryRun'])
            ->name('admin.migration.dry_run');

        Route::post('/admin/migration/real-transfer', [\App\Http\Controllers\Admin\MigrationController::class, 'realTransfer'])
            ->name('admin.migration.real_transfer');
    });
});

// ============================================================
// Auth Routes
// ============================================================

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/firebase', [AuthController::class, 'firebaseLogin'])->name('login.firebase');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'processForgotPassword'])->name('password.email');
Route::get('/reset-password/{email?}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'processResetPassword'])->name('password.update');
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// ============================================================
// Protected Routes
// ============================================================

Route::middleware(['auth'])->group(function () {

    Route::get('/auth/user-role-status', [AuthController::class, 'userRoleStatus']);

    // 1. ADMIN ONLY ROUTES (User Management & System Settings)
    Route::prefix('settings')
        ->name('admin.settings.')
        ->middleware(['role:admin'])
        ->group(function () {
            Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
            Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
            Route::post('/users/{id}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
            Route::delete('/users/{id}/force', [UserManagementController::class, 'forceDelete'])->name('users.forceDelete');
        });

    // 2. ADMIN & PHARMACIST ROUTES (Dashboard, Medicines, Purchases, Suppliers, Reports)
    Route::middleware(['role:admin,pharmacist'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Medicines
        Route::get('/medicines', AddMedicine::class);
        Route::get('/medicines/bulk-add', BulkAddMedicine::class)->name('medicines.bulk-add');
        Route::post('/medicines/store', [MedicineController::class, 'store']);

        // Purchases
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', PurchaseCreate::class)->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
        Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
        Route::get('/purchases/{id}/pdf', [PurchaseController::class, 'pdf'])->name('purchases.pdf');
        Route::get('/purchases/{id}/print', [PurchaseController::class, 'printInvoice'])->name('purchases.print');

        // Suppliers
        Route::get('/suppliers', [SupplierController::class, 'index']);
        Route::get('/suppliers/create', [SupplierController::class, 'create']);
        Route::post('/suppliers', [SupplierController::class, 'store']);
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit']);
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);
        Route::get('/suppliers-payable-report', [SupplierController::class, 'payableReport']);

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
            Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
            Route::get('/expiry', [ReportController::class, 'expiry'])->name('expiry');
            Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/suppliers', [ReportController::class, 'suppliers'])->name('suppliers');
            Route::get('/best-selling', [ReportController::class, 'bestSelling'])->name('best-selling');
            Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
            Route::get('/discounts', [ReportController::class, 'discounts'])->name('discounts');
        });
    });

    // 3. ADMIN, PHARMACIST & CASHIER ROUTES (POS, Sales, Returns, Expiry Alerts)
    Route::middleware(['role:admin,pharmacist,cashier'])->group(function () {
        Route::get('/pos', PosCounter::class);
        Route::get('/hold-invoices', HoldInvoiceList::class)->name('hold-invoices.index');
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{id}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/expiry-alerts', [DashboardController::class, 'expiryReport']);

        // Returns
        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('/', [ReturnController::class, 'index'])->name('index');
            Route::get('/sales/return/{id}', [ReturnController::class, 'salesShow'])->name('sales.show');
            Route::get('/purchases/return/{id}', [ReturnController::class, 'purchaseShow'])->name('purchase.show');
            Route::get('/sales/create', [ReturnController::class, 'salesCreate'])->name('sales.create');
            Route::get('/sales/{id}', [ReturnController::class, 'salesInvoice'])->name('sales.invoice');
            Route::post('/sales', [ReturnController::class, 'storeSalesReturn'])->name('sales.store');
            Route::get('/purchases/create', [ReturnController::class, 'purchaseCreate'])->name('purchase.create');
            Route::get('/purchases/{id}', [ReturnController::class, 'purchaseInvoice'])->name('purchase.invoice');
            Route::post('/purchases', [ReturnController::class, 'storePurchaseReturn'])->name('purchase.store');
        });
    });
});