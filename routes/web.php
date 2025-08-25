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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Ürün yönetimi
    Route::resource('products', ProductController::class);
    
    // Kategori yönetimi  
    Route::resource('categories', CategoryController::class);
    
    // Tedarikçi yönetimi
    Route::resource('suppliers', SupplierController::class);
    
    // Stok hareketleri
    Route::resource('stock-movements', StockMovementController::class);
    
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
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
    });
    
    // API routes for mobile/barcode scanning
    Route::prefix('api')->group(function () {
        Route::get('/products/search', [ProductController::class, 'search'])->name('api.products.search');
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('api.products.barcode');
    });
});

require __DIR__.'/auth.php';
