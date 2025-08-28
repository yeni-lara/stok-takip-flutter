<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-products')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $query = Product::with(['category', 'supplier']);

        // Filtreleme
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        if ($request->filter === 'low_stock') {
            $query->lowStock();
        } elseif ($request->filter === 'active') {
            $query->active();
        } elseif ($request->filter === 'inactive') {
            $query->where('is_active', false);
        }

        // Sıralama
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $products = $query->paginate(15)->withQueryString();
        
        // Filter seçenekleri için
        $categories = Category::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-products')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $categories = Category::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        
        // URL'den kategori seçimi
        $selectedCategory = $request->get('category');

        return view('products.create', compact('categories', 'suppliers', 'selectedCategory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-products')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        // Eğer barkod boşsa, otomatik oluştur
        $barcode = $request->barcode;
        if (empty($barcode)) {
            do {
                $barcode = $this->generateBarcode();
            } while (Product::where('barcode', $barcode)->exists());
        }

        // Resim yükleme
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'barcode' => $barcode,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'unit_price' => $request->unit_price,
            'tax_rate' => $request->tax_rate,
            'current_stock' => $request->current_stock,
            'min_stock' => $request->min_stock,
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-products')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $product->load(['category', 'supplier', 'stockMovements' => function($query) {
            $query->with('user')->latest()->take(10);
        }]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-products')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $categories = Category::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-products')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'barcode' => $request->barcode,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'unit_price' => $request->unit_price,
            'tax_rate' => $request->tax_rate,
            'min_stock' => $request->min_stock,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        // Yeni resim yüklenmişse
        if ($request->hasFile('image')) {
            // Eski resmi sil
            if ($product->image_path) {
                $oldImagePath = public_path($product->image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $data['image_path'] = $this->uploadImage($request->file('image'));
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Yetki kontrolü
        if (!Gate::allows('manage-products')) {
            abort(403, 'Bu işlemi gerçekleştirme yetkiniz yok.');
        }

        // Stok hareketi var mı kontrol et
        if ($product->stockMovements()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Bu ürüne ait stok hareketleri olduğu için silinemez.');
        }

        // Resmi sil
        if ($product->image_path) {
            $oldImagePath = public_path($product->image_path);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Ürün başarıyla silindi.');
    }

    /**
     * Barkod ile ürün ara (API)
     */
    public function findByBarcode($barcode)
    {
        $product = Product::with(['category', 'supplier'])
            ->where('barcode', $barcode)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }

        return response()->json($product);
    }

    /**
     * Ürün arama (API)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->with(['category'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    /**
     * Tüm ürünleri listele (API)
     */
    public function api(Request $request)
    {
        $products = Product::active()
            ->with(['category'])
            ->select(['id', 'name', 'barcode', 'current_stock', 'category_id'])
            ->orderBy('name')
            ->limit(50) // Performans için limit
            ->get();

        return response()->json($products);
    }

    /**
     * Barkod oluştur
     */
    private function generateBarcode()
    {
        // EAN-13 benzeri 13 haneli barkod oluştur
        return '999' . str_pad(rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    /**
     * Resim yükle ve boyutlandır
     */
    private function uploadImage($file)
    {
        $manager = new ImageManager(new Driver());
        
        // Benzersiz dosya adı oluştur
        $filename = time() . '_' . uniqid() . '.jpg';
        
        // Public klasöründe uploads/products dizinini oluştur
        $uploadPath = public_path('uploads/products');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Resmi yeniden boyutlandır ve sıkıştır
        $image = $manager->read($file);
        $image->scale(width: 800); // Max genişlik 800px
        
        // Public klasöre direkt kaydet
        $fullPath = $uploadPath . '/' . $filename;
        $image->save($fullPath);
        
        // Veritabanında saklayacağımız relative path
        return 'uploads/products/' . $filename;
    }
}
