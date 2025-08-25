<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-categories')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $categories = Category::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-categories')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-categories')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-categories')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $category->load(['products' => function($query) {
            $query->active()->latest()->take(10);
        }]);

        $productCount = $category->products()->active()->count();
        $totalStock = $category->products()->active()->sum('current_stock');
        $totalValue = $category->products()->active()->get()->sum('total_value');

        return view('categories.show', compact('category', 'productCount', 'totalStock', 'totalValue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-categories')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-categories')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-categories')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        // Kategoriye ait ürün var mı kontrol et
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Bu kategoriye ait ürünler olduğu için silinemez.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori başarıyla silindi.');
    }
}
