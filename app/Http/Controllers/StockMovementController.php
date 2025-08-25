<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('view_reports')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $query = StockMovement::with(['product', 'user', 'customer']);

        // Tarih filtresi
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Hareket tipi filtresi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Ürün filtresi
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Kullanıcı filtresi
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Müşteri filtresi
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                })->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['created_at', 'type', 'quantity'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $movements = $query->paginate(15)->appends($request->all());

        // Filtreleme için veriler
        $products = Product::active()->orderBy('name')->get();
        $users = User::active()->orderBy('name')->get();
        $customers = Customer::active()->orderBy('company_name')->get();

        return view('stock-movements.index', compact('movements', 'products', 'users', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'giriş');
        
        // Yetki kontrolü
        if (!$this->checkPermissionForType($type)) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $products = Product::active()->with('category')->orderBy('name')->get();
        $customers = Customer::active()->orderBy('company_name')->get();

        return view('stock-movements.create', compact('type', 'products', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = $request->type;
        
        // Yetki kontrolü
        if (!$this->checkPermissionForType($type)) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'type' => 'required|in:giriş,çıkış,iade',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id',
            'note' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($validatedData['product_id']);

        // Stok kontrolleri
        if ($type === 'çıkış' && $product->current_stock < $validatedData['quantity']) {
            return back()->withErrors(['quantity' => 'Yeterli stok bulunmamaktadır. Mevcut stok: ' . $product->current_stock]);
        }

        // Müşteri kontrolü (çıkış ve iade için)
        if (in_array($type, ['çıkış', 'iade']) && !$validatedData['customer_id']) {
            return back()->withErrors(['customer_id' => 'Bu işlem türü için müşteri seçimi zorunludur.']);
        }

        DB::transaction(function () use ($validatedData, $product, $type) {
            // Stok hareketi oluştur
            $movement = StockMovement::create([
                'product_id' => $validatedData['product_id'],
                'user_id' => Auth::id(),
                'customer_id' => $validatedData['customer_id'] ?? null,
                'type' => $type,
                'quantity' => $validatedData['quantity'],
                'previous_stock' => $product->current_stock,
                'new_stock' => $this->calculateNewStock($product->current_stock, $validatedData['quantity'], $type),
                'note' => $validatedData['note'] ?? null,
                'reference_number' => !empty($validatedData['reference_number']) ? $validatedData['reference_number'] : $this->generateReferenceNumber($type),
            ]);

            // Ürün stokunu güncelle
            $product->updateStock($validatedData['quantity'], $type, $validatedData['customer_id'] ?? null);
        });

        $messages = [
            'giriş' => 'Stok girişi başarıyla kaydedildi.',
            'çıkış' => 'Stok çıkışı başarıyla kaydedildi.',
            'iade' => 'Stok iadesi başarıyla kaydedildi.',
        ];

        // Teslimat elemanı için özel yönlendirme
        if (Auth::user()->role_id == 3) {
            return redirect()->route('dashboard')
                ->with('success', $messages[$type] . ' Dashboard\'a dönmek için ana sayfayı kullanabilirsiniz.');
        }

        return redirect()->route('stock-movements.index')
            ->with('success', $messages[$type]);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        if (!Gate::allows('view-reports')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $stockMovement->load(['product.category', 'user', 'customer']);

        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockMovement $stockMovement)
    {
        // Stok hareketleri genellikle düzenlenmez, sadece admin yetkisi
        if (!Gate::allows('admin')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $products = Product::active()->with('category')->orderBy('name')->get();
        $customers = Customer::active()->orderBy('company_name')->get();

        return view('stock-movements.edit', compact('stockMovement', 'products', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        // Sadece admin düzenleyebilir
        if (!Gate::allows('admin')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'note' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $stockMovement->update($validatedData);

        return redirect()->route('stock-movements.show', $stockMovement)
            ->with('success', 'Stok hareketi başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        // Sadece admin silebilir ve çok dikkatli olunmalı
        if (!Gate::allows('admin')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return back()->with('error', 'Stok hareketleri sistem bütünlüğü için silinemez.');
    }

    /**
     * Stok giriş sayfası
     */
    public function stockEntry()
    {
        if (!Gate::allows('stock_entry')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return redirect()->route('stock-movements.create', ['type' => 'giriş']);
    }

    /**
     * Stok çıkış sayfası
     */
    public function stockExit()
    {
        if (!Gate::allows('stock_exit')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Teslimat elemanı için özel sayfa
        if (Auth::user()->role_id == 3) {
            $customers = Customer::active()->get();
            return view('stock-movements.delivery-exit', compact('customers'));
        }

        return redirect()->route('stock-movements.create', ['type' => 'çıkış']);
    }

    /**
     * Stok iade sayfası
     */
    public function stockReturn()
    {
        if (!Gate::allows('stock_return')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Teslimat elemanı için özel sayfa
        if (Auth::user()->role_id == 3) {
            $customers = Customer::active()->get();
            return view('stock-movements.delivery-return', compact('customers'));
        }

        return redirect()->route('stock-movements.create', ['type' => 'iade']);
    }

    /**
     * Yetki kontrolü
     */
    private function checkPermissionForType($type)
    {
        $permissions = [
            'giriş' => 'stock_entry',
            'çıkış' => 'stock_exit',
            'iade' => 'stock_return',
        ];

        return Gate::allows($permissions[$type] ?? 'admin');
    }

    /**
     * Yeni stok hesaplama
     */
    private function calculateNewStock($currentStock, $quantity, $type)
    {
        switch ($type) {
            case 'giriş':
            case 'iade':
                return $currentStock + $quantity;
            case 'çıkış':
                return $currentStock - $quantity;
            default:
                return $currentStock;
        }
    }

    /**
     * Referans numarası oluştur
     */
    private function generateReferenceNumber($type)
    {
        $prefix = [
            'giriş' => 'SG',
            'çıkış' => 'SC',
            'iade' => 'SI',
        ];

        return ($prefix[$type] ?? 'SM') . date('Ymd') . sprintf('%04d', StockMovement::whereDate('created_at', today())->count() + 1);
    }
}
