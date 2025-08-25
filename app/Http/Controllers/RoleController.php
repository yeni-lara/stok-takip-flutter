<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    // Tüm mevcut yetkiler listesi
    private $allPermissions = [
        'user_management' => 'Kullanıcı Yönetimi',
        'role_management' => 'Rol Yönetimi',
        'product_management' => 'Ürün Yönetimi',
        'category_management' => 'Kategori Yönetimi',
        'supplier_management' => 'Tedarikçi Yönetimi',
        'customer_management' => 'Müşteri Yönetimi',
        'stock_entry' => 'Stok Giriş',
        'stock_exit' => 'Stok Çıkış',
        'stock_return' => 'Stok İade',
        'stock_count' => 'Stok Sayım',
        'stock_movement_management' => 'Stok Hareket Yönetimi',
        'view_reports' => 'Rapor Görüntüleme',
        'export_reports' => 'Rapor Dışa Aktarma',
        'settings_management' => 'Sistem Ayarları',
        'admin' => 'Admin Yetkisi'
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Gate::allows('manage-roles')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $roles = Role::withCount('users')->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('manage-roles')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $permissions = $this->allPermissions;

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('manage-roles')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->allPermissions)),
            'is_active' => 'boolean',
        ]);

        $validatedData['permissions'] = $request->permissions ?? [];
        
        Role::create($validatedData);

        return redirect()->route('roles.index')
            ->with('success', 'Rol başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        if (!Gate::allows('manage-roles')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $role->load('users');
        $permissions = $this->allPermissions;

        return view('roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (!Gate::allows('manage-roles')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $permissions = $this->allPermissions;

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if (!Gate::allows('manage-roles')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->allPermissions)),
            'is_active' => 'boolean',
        ]);

        $validatedData['permissions'] = $request->permissions ?? [];
        
        $role->update($validatedData);

        return redirect()->route('roles.index')
            ->with('success', 'Rol başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!Gate::allows('manage-roles')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Admin rolü silinemez
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Admin rolü silinemez.');
        }

        // Kullanıcısı olan rol silinemez
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Bu role ait kullanıcılar bulunduğu için silinemez.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol başarıyla silindi.');
    }

    /**
     * Yetki listesini API olarak döndür
     */
    public function getPermissions()
    {
        return response()->json($this->allPermissions);
    }
}
