@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>Raporlar ve Analiz
                </h2>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i>Hızlı Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('reports.export.stock-status.excel', request()->all()) }}">
                            <i class="bi bi-file-earmark-excel me-2"></i>Stok Durumu (Excel)
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.export.movements.excel', request()->all()) }}">
                            <i class="bi bi-file-earmark-excel me-2"></i>Stok Hareketleri (Excel)
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('reports.export.stock-status.pdf', request()->all()) }}">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Stok Durumu (PDF)
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.export.movements.pdf', request()->all()) }}">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Stok Hareketleri (PDF)
                        </a></li>
                    </ul>
                </div>
            </div>

            <!-- Genel İstatistikler -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center border-primary">
                        <div class="card-body">
                            <i class="bi bi-box-seam text-primary fs-1"></i>
                            <h5 class="card-title mt-2">Toplam Ürün</h5>
                            <h3 class="text-primary">{{ number_format($stats['total_products']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <i class="bi bi-activity text-success fs-1"></i>
                            <h5 class="card-title mt-2">Toplam Hareket</h5>
                            <h3 class="text-success">{{ number_format($stats['total_movements']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <i class="bi bi-currency-exchange text-warning fs-1"></i>
                            <h5 class="card-title mt-2">Toplam Değer</h5>
                            <h3 class="text-warning">{{ number_format($stats['total_value'], 2) }} TL</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-danger">
                        <div class="card-body">
                            <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                            <h5 class="card-title mt-2">Düşük Stok</h5>
                            <h3 class="text-danger">{{ number_format($stats['low_stock_count']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hızlı Erişim Kartları -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clipboard-data text-primary fs-2"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1">Stok Durum Raporu</h5>
                                    <p class="card-text text-muted">Ürünlerin anlık stok durumları</p>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.stock-status') }}" class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i>Raporu Görüntüle
                                </a>
                                <div class="btn-group">
                                    <a href="{{ route('reports.export.stock-status.excel') }}" class="btn btn-outline-success">
                                        <i class="bi bi-download me-1"></i>Excel
                                    </a>
                                    <a href="{{ route('reports.export.stock-status.pdf') }}" class="btn btn-outline-danger">
                                        <i class="bi bi-download me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-arrow-left-right text-success fs-2"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1">Stok Hareket Raporu</h5>
                                    <p class="card-text text-muted">Giriş, çıkış ve iade hareketleri</p>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.stock-movements') }}" class="btn btn-success">
                                    <i class="bi bi-eye me-1"></i>Raporu Görüntüle
                                </a>
                                <div class="btn-group">
                                    <a href="{{ route('reports.export.movements.excel') }}" class="btn btn-outline-success">
                                        <i class="bi bi-download me-1"></i>Excel
                                    </a>
                                    <a href="{{ route('reports.export.movements.pdf') }}" class="btn btn-outline-danger">
                                        <i class="bi bi-download me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-pie-chart text-info fs-2"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1">Değer Analizi</h5>
                                    <p class="card-text text-muted">Kategori bazlı değer dağılımı</p>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('reports.value-analysis') }}" class="btn btn-info">
                                    <i class="bi bi-eye me-1"></i>Analizi Görüntüle
                                </a>
                                <small class="text-muted text-center">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Stok değerleri ve oranları
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bu Ay Özeti -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-month me-2"></i>Bu Ay Özeti
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4 class="text-primary">{{ number_format($stats['this_month_movements']) }}</h4>
                            <small class="text-muted">Bu Ay Hareket</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-success">
                                {{ $stats['total_movements'] > 0 ? number_format(($stats['this_month_movements'] / $stats['total_movements']) * 100, 1) : 0 }}%
                            </h4>
                            <small class="text-muted">Toplam Oranı</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-warning">{{ number_format($stats['low_stock_count']) }}</h4>
                            <small class="text-muted">Uyarı Gereken</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-info">{{ number_format($stats['total_value'], 0) }} TL</h4>
                            <small class="text-muted">Toplam Stok Değeri</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Son 7 Gün Hareket Grafiği -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Son 7 Gün Stok Hareketleri
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" width="400" height="100"></canvas>
                </div>
            </div>

            <!-- Kategori Dağılımı -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Kategori Bazlı Ürün Dağılımı
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryStats as $category)
                            <div class="col-md-3 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ $category->name }}</h6>
                                        <h4 class="text-primary">{{ $category->products_count }}</h4>
                                        <small class="text-muted">Ürün Sayısı</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Haftalık hareket grafiği
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    
    // Grafik verilerini hazırla
    const weeklyData = @json($weeklyMovements);
    const dates = [...new Set(weeklyData.map(item => item.date))].sort();
    
    const datasets = [
        {
            label: 'Giriş',
            data: dates.map(date => {
                const entry = weeklyData.find(item => item.date === date && item.type === 'giriş');
                return entry ? entry.count : 0;
            }),
            backgroundColor: 'rgba(25, 135, 84, 0.2)',
            borderColor: 'rgba(25, 135, 84, 1)',
            borderWidth: 2,
            fill: true
        },
        {
            label: 'Çıkış',
            data: dates.map(date => {
                const exit = weeklyData.find(item => item.date === date && item.type === 'çıkış');
                return exit ? exit.count : 0;
            }),
            backgroundColor: 'rgba(220, 53, 69, 0.2)',
            borderColor: 'rgba(220, 53, 69, 1)',
            borderWidth: 2,
            fill: true
        },
        {
            label: 'İade',
            data: dates.map(date => {
                const return_ = weeklyData.find(item => item.date === date && item.type === 'iade');
                return return_ ? return_.count : 0;
            }),
            backgroundColor: 'rgba(13, 202, 240, 0.2)',
            borderColor: 'rgba(13, 202, 240, 1)',
            borderWidth: 2,
            fill: true
        }
    ];
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => new Date(date).toLocaleDateString('tr-TR')),
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Günlük Stok Hareket Sayıları'
                },
                legend: {
                    position: 'top',
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