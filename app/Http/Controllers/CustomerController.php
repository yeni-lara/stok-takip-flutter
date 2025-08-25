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
        if (!Gate::allows('user_management')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $query = Customer::query();

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Tip filtresi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Şehir filtresi
        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $allowedSorts = ['name', 'company_name', 'type', 'city', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'name') {
                $query->orderBy('name', $sortDir)->orderBy('surname', $sortDir);
            } else {
                $query->orderBy($sortBy, $sortDir);
            }
        }

        $customers = $query->paginate(10)->appends($request->all());

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('user_management')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('user_management')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $rules = [
            'type' => 'required|in:individual,corporate',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:20',
            'tax_office' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // Tip göre validasyon kuralları
        if ($request->type === 'individual') {
            $rules['name'] = 'required|string|max:255';
            $rules['surname'] = 'required|string|max:255';
        } else {
            $rules['company_name'] = 'required|string|max:255';
        }

        $validatedData = $request->validate($rules);

        Customer::create($validatedData);

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri başarıyla eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        if (!Gate::allows('user_management')) {
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
        if (!Gate::allows('user_management')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        if (!Gate::allows('user_management')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $rules = [
            'type' => 'required|in:individual,corporate',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:20',
            'tax_office' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // Tip göre validasyon kuralları
        if ($request->type === 'individual') {
            $rules['name'] = 'required|string|max:255';
            $rules['surname'] = 'required|string|max:255';
        } else {
            $rules['company_name'] = 'required|string|max:255';
        }

        $validatedData = $request->validate($rules);

        $customer->update($validatedData);

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri bilgileri başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if (!Gate::allows('user_management')) {
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
