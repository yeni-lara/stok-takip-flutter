<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('manage-suppliers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $query = Supplier::query();

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['name', 'contact_person', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $suppliers = $query->withCount('products')->paginate(10)->appends($request->all());

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('manage-suppliers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-suppliers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Supplier::create($validatedData);

        return redirect()->route('suppliers.index')
            ->with('success', 'Tedarikçi başarıyla eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        if (!Gate::allows('manage-suppliers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Tedarikçiye ait ürünleri getir
        $products = $supplier->products()
            ->with(['category', 'stockMovements'])
            ->active()
            ->orderBy('name')
            ->get();

        return view('suppliers.show', compact('supplier', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        if (!Gate::allows('manage-suppliers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        if (!Gate::allows('manage-suppliers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validatedData);

        return redirect()->route('suppliers.index')
            ->with('success', 'Tedarikçi bilgileri başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if (!Gate::allows('manage-suppliers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Ürünü olan tedarikçi silinemez
        if ($supplier->products()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Bu tedarikçiye ait ürünler bulunduğu için silinemez.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Tedarikçi başarıyla silindi.');
    }
}
