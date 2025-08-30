<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

// Login API - Token oluştur
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Girilen bilgiler hatalı.'],
        ]);
    }

    $token = $user->createToken('mobile-app')->plainTextToken;

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'token' => $token,
        'message' => 'Giriş başarılı'
    ]);
});

// Logout API - Token sil
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    
    return response()->json(['message' => 'Çıkış başarılı']);
});

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