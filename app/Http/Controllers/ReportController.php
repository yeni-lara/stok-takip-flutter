<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        if (!Gate::allows('view_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Genel istatistikler
        $stats = [
            'total_products' => Product::count(),
            'total_movements' => StockMovement::count(),
            'total_value' => Product::sum(DB::raw('current_stock * unit_price')),
            'low_stock_count' => Product::whereRaw('current_stock <= min_stock')->count(),
            'this_month_movements' => StockMovement::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Son 7 günlük hareket grafiği için veri
        $weeklyMovements = StockMovement::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                'type'
            )
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        // Kategori bazlı stok dağılımı
        $categoryStats = Category::withCount('products')
            ->with(['products' => function($query) {
                $query->select('category_id', DB::raw('SUM(current_stock * unit_price) as total_value'))
                    ->groupBy('category_id');
            }])
            ->get();

        return view('reports.index', compact('stats', 'weeklyMovements', 'categoryStats'));
    }

    /**
     * Stok durum raporu
     */
    public function stockStatus(Request $request)
    {
        if (!Gate::allows('view_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $query = Product::with(['category', 'supplier']);

        // Filtreler
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereRaw('current_stock <= min_stock');
                    break;
                case 'zero':
                    $query->where('current_stock', 0);
                    break;
                case 'normal':
                    $query->whereRaw('current_stock > min_stock');
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(50)->withQueryString();
        $categories = Category::active()->get();
        $suppliers = Supplier::active()->get();

        return view('reports.stock-status', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Stok hareket raporu
     */
    public function stockMovements(Request $request)
    {
        if (!Gate::allows('view_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $query = StockMovement::with(['product', 'user', 'customer']);

        // Tarih filtresi
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
            }
        }

        // Diğer filtreler
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('product_search')) {
            $search = $request->product_search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();
        $users = User::active()->get();
        $customers = Customer::active()->get();

        // İstatistikler
        $stats = [
            'total_movements' => $query->count(),
            'total_entries' => (clone $query)->where('type', 'giriş')->count(),
            'total_exits' => (clone $query)->where('type', 'çıkış')->count(),
            'total_returns' => (clone $query)->where('type', 'iade')->count(),
        ];

        return view('reports.stock-movements', compact('movements', 'users', 'customers', 'stats'));
    }

    /**
     * Excel export - Stok durumu
     */
    public function exportStockStatusExcel(Request $request)
    {
        if (!Gate::allows('export_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        return Excel::download(new \App\Exports\StockStatusExport($request->all()), 
            'stok-durumu-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * PDF export - Stok durumu
     */
    public function exportStockStatusPdf(Request $request)
    {
        if (!Gate::allows('export_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $query = Product::with(['category', 'supplier']);

        // Aynı filtreleri uygula
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereRaw('current_stock <= min_stock');
                    break;
                case 'zero':
                    $query->where('current_stock', 0);
                    break;
                case 'normal':
                    $query->whereRaw('current_stock > min_stock');
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('reports.exports.stock-status-pdf', compact('products'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('stok-durumu-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Excel export - Stok hareketleri
     */
    public function exportMovementsExcel(Request $request)
    {
        if (!Gate::allows('export_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        return Excel::download(new \App\Exports\StockMovementsExport($request->all()), 
            'stok-hareketleri-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * PDF export - Stok hareketleri
     */
    public function exportMovementsPdf(Request $request)
    {
        if (!Gate::allows('export_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $query = StockMovement::with(['product', 'user', 'customer']);

        // Tüm filtreleri uygula
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('product_search')) {
            $search = $request->product_search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('reports.exports.stock-movements-pdf', compact('movements'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('stok-hareketleri-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Değer analizi raporu
     */
    public function valueAnalysis()
    {
        if (!Gate::allows('view_reports')) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Kategori bazlı değer analizi
        $categoryValues = Category::select('categories.*')
            ->selectRaw('COALESCE(SUM(products.current_stock * products.unit_price), 0) as total_value')
            ->selectRaw('COALESCE(SUM(products.current_stock * products.unit_price * (1 + products.tax_rate/100)), 0) as total_value_with_tax')
            ->selectRaw('COUNT(products.id) as product_count')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->where('categories.is_active', true)
            ->groupBy('categories.id', 'categories.name', 'categories.description', 'categories.is_active', 'categories.created_at', 'categories.updated_at')
            ->orderBy('total_value', 'desc')
            ->get();

        // Toplam değerler
        $totalStats = [
            'total_value' => $categoryValues->sum('total_value'),
            'total_value_with_tax' => $categoryValues->sum('total_value_with_tax'),
            'total_products' => $categoryValues->sum('product_count'),
        ];

        // En değerli ürünler
        $topProducts = Product::select('products.*')
            ->selectRaw('(current_stock * unit_price) as total_value')
            ->where('current_stock', '>', 0)
            ->orderBy('total_value', 'desc')
            ->take(10)
            ->get();

        return view('reports.value-analysis', compact('categoryValues', 'totalStats', 'topProducts'));
    }
}
