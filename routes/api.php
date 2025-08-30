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

// Barkod ile ürün getir (QR tarama için)
Route::get('/products/by-barcode/{barcode}', function ($barcode) {
    // Detaylı logging ekle
    \Log::info('Barkod sorgulama API çağrıldı', [
        'barkod' => $barcode,
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'timestamp' => now()
    ]);

    try {
        $product = Product::with('category')
            ->where('barcode', $barcode)
            ->where('is_active', true)
            ->first();
        
        if (!$product) {
            \Log::warning('Ürün bulunamadı', ['barkod' => $barcode]);
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
                'barcode' => $barcode
            ], 404);
        }

        \Log::info('Ürün bulundu', [
            'barkod' => $barcode,
            'product_id' => $product->id,
            'product_name' => $product->name
        ]);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'current_stock' => $product->current_stock,
                'category' => $product->category->name ?? null,
                'unit_price' => $product->unit_price,
                'description' => $product->description ?? null,
                'image_path' => $product->image_path ?? null
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Barkod sorgulama hatası', [
            'barkod' => $barcode,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Sunucu hatası oluştu',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Müşteri listesi getir
Route::get('/customers', function () {
    // Detaylı logging ekle
    \Log::info('Müşteri listesi API çağrıldı', [
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'timestamp' => now()
    ]);

    try {
        // Müşteri tablosundan aktif müşterileri getir
        $customers = \DB::table('customers')
            ->where('is_active', true)
            ->select('id', 'company_name')
            ->orderBy('company_name', 'asc')
            ->get();

        \Log::info('Müşteri listesi başarıyla getirildi', [
            'customer_count' => $customers->count()
        ]);

        return response()->json([
            'success' => true,
            'customers' => $customers
        ]);

    } catch (\Exception $e) {
        \Log::error('Müşteri listesi hatası', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Müşteri listesi alınamadı',
            'error' => $e->getMessage()
        ], 500);
    }
}); 