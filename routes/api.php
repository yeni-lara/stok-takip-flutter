<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Barkod ile ürün getir
Route::middleware('auth:sanctum')->get('/products/by-barcode/{barcode}', function ($barcode) {
    $product = Product::with('category')
        ->where('barcode', $barcode)
        ->where('is_active', true)
        ->first();
    
    if (!$product) {
        return response()->json(['message' => 'Ürün bulunamadı'], 404);
    }
    
    return response()->json([
        'id' => $product->id,
        'name' => $product->name,
        'barcode' => $product->barcode,
        'current_stock' => $product->current_stock,
        'category' => $product->category->name ?? null,
        'unit_price' => $product->unit_price
    ]);
}); 