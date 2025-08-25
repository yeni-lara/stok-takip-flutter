<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
Route::get('/dashboard', function () {
    return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Ürün yönetimi
    Route::resource('products', ProductController::class);
    
    // Kategori yönetimi  
    Route::resource('categories', CategoryController::class);
    
    // Tedarikçi yönetimi
    Route::resource('suppliers', SupplierController::class);
    
    // Müşteri yönetimi
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    
    // Kullanıcı yönetimi
    Route::resource('users', App\Http\Controllers\UserController::class);
    
    // Rol yönetimi
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    
    // Stok hareketleri
    Route::resource('stock-movements', StockMovementController::class);
    Route::get('/stock/entry', [StockMovementController::class, 'stockEntry'])->name('stock.entry');
    Route::get('/stock/exit', [StockMovementController::class, 'stockExit'])->name('stock.exit');
    Route::get('/stock/return', [StockMovementController::class, 'stockReturn'])->name('stock.return');
    
    // Raporlar
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/stock-status', [ReportController::class, 'stockStatus'])->name('reports.stock-status');
    Route::get('/reports/stock-movements', [ReportController::class, 'stockMovements'])->name('reports.stock-movements');
    Route::get('/reports/stock-value', [ReportController::class, 'stockValue'])->name('reports.stock-value');
    
    // Export işlemleri
    Route::get('/reports/export/stock-status/{format}', [ReportController::class, 'exportStockStatus'])->name('reports.export.stock-status');
    Route::get('/reports/export/stock-movements/{format}', [ReportController::class, 'exportStockMovements'])->name('reports.export.stock-movements');
    Route::get('/reports/export/stock-value/{format}', [ReportController::class, 'exportStockValue'])->name('reports.export.stock-value');
    
    // Kullanıcı yönetimi (sadece admin)
    Route::resource('users', App\Http\Controllers\UserController::class);
    
    // API routes for mobile/barcode scanning
    Route::prefix('api')->group(function () {
        Route::get('/products/search', [ProductController::class, 'search'])->name('api.products.search');
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('api.products.barcode');
    });
});

require __DIR__.'/auth.php';
