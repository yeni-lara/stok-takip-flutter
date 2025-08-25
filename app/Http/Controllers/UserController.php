<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('manage_users')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $query = User::with('role')
            ->orderBy('created_at', 'desc');

        // Arama filtresi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Rol filtresi
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::active()->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('manage_users')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $roles = Role::active()->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('manage_users')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean']
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (!Gate::allows('manage_users')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $user->load('role', 'stockMovements.product');
        
        // Son stok hareketleri
        $recentMovements = $user->stockMovements()
            ->with(['product', 'customer'])
            ->latest()
            ->take(10)
            ->get();

        // İstatistikler
        $stats = [
            'total_movements' => $user->stockMovements()->count(),
            'total_entries' => $user->stockMovements()->where('type', 'giriş')->count(),
            'total_exits' => $user->stockMovements()->where('type', 'çıkış')->count(),
            'total_returns' => $user->stockMovements()->where('type', 'iade')->count(),
        ];

        return view('users.show', compact('user', 'recentMovements', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!Gate::allows('manage_users')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $roles = Role::active()->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!Gate::allows('manage_users')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean']
        ]);

        $validated['is_active'] = $request->has('is_active');

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!Gate::allows('manage_users')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Admin kullanıcısını silmeyi engelle
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'Ana admin kullanıcısı silinemez.');
        }

        // Kendini silmeyi engelle
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        // Stok hareketleri varsa silmeyi engelle
        if ($user->stockMovements()->count() > 0) {
            return redirect()->route('users.index')->with('error', 'Bu kullanıcının stok hareketleri bulunmaktadır. Kullanıcı silinemez.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
