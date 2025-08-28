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
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/stock-status', [ReportController::class, 'stockStatus'])->name('stock-status');
        Route::get('/stock-movements', [ReportController::class, 'stockMovements'])->name('stock-movements');
        Route::get('/value-analysis', [ReportController::class, 'valueAnalysis'])->name('value-analysis');
        
        // Export routes
        Route::get('/export/stock-status/excel', [ReportController::class, 'exportStockStatusExcel'])->name('export.stock-status.excel');
        Route::get('/export/stock-status/pdf', [ReportController::class, 'exportStockStatusPdf'])->name('export.stock-status.pdf');
        Route::get('/export/movements/excel', [ReportController::class, 'exportMovementsExcel'])->name('export.movements.excel');
        Route::get('/export/movements/pdf', [ReportController::class, 'exportMovementsPdf'])->name('export.movements.pdf');
    });
    
    // Kullanıcı yönetimi (sadece admin)
    Route::resource('users', App\Http\Controllers\UserController::class);
    
    // Profil yönetimi
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [App\Http\Controllers\ProfileController::class, 'editPassword'])->name('profile.password');
    Route::patch('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    // API routes for mobile/barcode scanning
    Route::prefix('api')->group(function () {
        Route::get('/products/search', [ProductController::class, 'search'])->name('api.products.search');
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('api.products.barcode');
        Route::get('/products', [ProductController::class, 'api'])->name('api.products');
    });
});

// Barkod ile ürün getir (geçici test route)
Route::get('/test-barcode/{barcode}', function ($barcode) {
    $product = \App\Models\Product::with('category')
        ->where('barcode', $barcode)
        ->where('is_active', true)
        ->first();
    
    if (!$product) {
        return response()->json(['message' => 'Ürün bulunamadı', 'barcode' => $barcode], 404);
    }
    
    return response()->json([
        'id' => $product->id,
        'name' => $product->name,
        'barcode' => $product->barcode,
        'current_stock' => $product->current_stock,
        'category' => $product->category->name ?? null,
        'unit_price' => $product->unit_price
    ]);
})->middleware('auth');

require __DIR__.'/auth.php';
