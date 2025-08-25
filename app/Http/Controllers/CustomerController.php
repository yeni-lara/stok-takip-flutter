<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('manage-customers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $query = Customer::query();

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('company_name', 'like', "%{$search}%");
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['company_name', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $customers = $query->paginate(10)->appends($request->all());

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('manage-customers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-customers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Customer::create($validatedData);

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri başarıyla eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        if (!Gate::allows('manage-customers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Son stok hareketleri
        $recentMovements = $customer->stockMovements()
            ->with(['product', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('customers.show', compact('customer', 'recentMovements'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        if (!Gate::allows('manage-customers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        if (!Gate::allows('manage-customers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $customer->update($validatedData);

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri bilgileri başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if (!Gate::allows('manage-customers')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Stok hareketi varsa silinmesine izin verme
        if ($customer->stockMovements()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Bu müşteriye ait stok hareketleri bulunduğu için silinemez.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri başarıyla silindi.');
    }
}
