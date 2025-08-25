@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Stok Değer Analizi
                </h2>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Raporlara Dön
                </a>
            </div>

            <!-- Genel Toplam Değerler -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center border-primary">
                        <div class="card-body">
                            <i class="bi bi-currency-exchange text-primary fs-1"></i>
                            <h5 class="card-title mt-2">Toplam Stok Değeri</h5>
                            <h3 class="text-primary">{{ number_format($totalStats['total_value'], 2) }} TL</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <i class="bi bi-calculator text-success fs-1"></i>
                            <h5 class="card-title mt-2">KDV Dahil Değer</h5>
                            <h3 class="text-success">{{ number_format($totalStats['total_value_with_tax'], 2) }} TL</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <i class="bi bi-box-seam text-info fs-1"></i>
                            <h5 class="card-title mt-2">Toplam Ürün Sayısı</h5>
                            <h3 class="text-info">{{ number_format($totalStats['total_products']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Bazlı Değer Dağılımı -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Kategori Bazlı Değer Dağılımı
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Ürün Sayısı</th>
                                    <th>Toplam Değer (TL)</th>
                                    <th>KDV Dahil Değer (TL)</th>
                                    <th>Toplam Oranı (%)</th>
                                    <th>Görsel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryValues as $category)
                                    @php
                                        $percentage = $totalStats['total_value'] > 0 ? ($category->total_value / $totalStats['total_value']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->description)
                                                <br><small class="text-muted">{{ $category->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ number_format($category->product_count) }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($category->total_value, 2) }} TL</strong>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($category->total_value_with_tax, 2) }} TL</strong>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($percentage, 1) }}%</strong>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: {{ $percentage }}%"
                                                     title="{{ number_format($percentage, 1) }}%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>TOPLAM</th>
                                    <th>{{ number_format($totalStats['total_products']) }}</th>
                                    <th>{{ number_format($totalStats['total_value'], 2) }} TL</th>
                                    <th>{{ number_format($totalStats['total_value_with_tax'], 2) }} TL</th>
                                    <th>100%</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- En Değerli Ürünler -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>En Değerli 10 Ürün (Stok Değeri Bazında)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sıra</th>
                                    <th>Resim</th>
                                    <th>Ürün</th>
                                    <th>Kategori</th>
                                    <th>Mevcut Stok</th>
                                    <th>Birim Fiyat</th>
                                    <th>Toplam Değer</th>
                                    <th>KDV Dahil Değer</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                    @php
                                        $totalValue = $product->current_stock * $product->unit_price;
                                        $totalValueWithTax = $totalValue * (1 + $product->tax_rate / 100);
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <span class="badge {{ $index == 0 ? 'bg-warning' : ($index == 1 ? 'bg-secondary' : 'bg-danger') }}">
                                                    {{ $index + 1 }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->hasImage())
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; border-radius: 0.375rem;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->barcode)
                                                <br><small class="text-muted">{{ $product->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $product->category->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($product->current_stock) }}</span>
                                        </td>
                                        <td>
                                            {{ number_format($product->unit_price, 2) }} TL
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ number_format($totalValue, 2) }} TL</strong>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ number_format($totalValueWithTax, 2) }} TL</strong>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info" title="Ürün Detayı">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()->hasPermission('stock_entry'))
                                                    <a href="{{ route('stock.entry', ['product_id' => $product->id]) }}" class="btn btn-outline-success" title="Stok Girişi">
                                                        <i class="bi bi-plus-circle"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted fs-1"></i>
                                            <p class="text-muted mt-2">Henüz stoklu ürün bulunmamaktadır</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js için pasta grafiği -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Değer Dağılımı Grafiği
                </h5>
            </div>
            <div class="card-body">
                <canvas id="valueChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Ürün Sayısı Dağılımı
                </h5>
            </div>
            <div class="card-body">
                <canvas id="productChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryData = @json($categoryValues);
    
    // Değer dağılımı pasta grafiği
    const valueCtx = document.getElementById('valueChart').getContext('2d');
    new Chart(valueCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.name),
            datasets: [{
                data: categoryData.map(item => item.total_value),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#FF6384',
                    '#C9CBCF'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = new Intl.NumberFormat('tr-TR', {
                                style: 'currency',
                                currency: 'TRY'
                            }).format(context.parsed);
                            return context.label + ': ' + value;
                        }
                    }
                }
            }
        }
    });
    
    // Ürün sayısı dağılımı
    const productCtx = document.getElementById('productChart').getContext('2d');
    new Chart(productCtx, {
        type: 'bar',
        data: {
            labels: categoryData.map(item => item.name),
            datasets: [{
                label: 'Ürün Sayısı',
                data: categoryData.map(item => item.product_count),
                backgroundColor: '#36A2EB',
                borderColor: '#36A2EB',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endsection 