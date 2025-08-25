@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-arrow-left-right me-2"></i>Stok Hareket Raporu
                </h2>
                <div class="btn-group">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Raporlara Dön
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reports.export.movements.excel', request()->all()) }}">
                                <i class="bi bi-file-earmark-excel me-2"></i>Excel İndir
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.export.movements.pdf', request()->all()) }}">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF İndir
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- İstatistikler -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Toplam Hareket</h5>
                            <h3 class="text-primary">{{ number_format($stats['total_movements']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <h5 class="card-title">Stok Girişi</h5>
                            <h3 class="text-success">{{ number_format($stats['total_entries']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <h5 class="card-title">Stok Çıkışı</h5>
                            <h3 class="text-warning">{{ number_format($stats['total_exits']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <h5 class="card-title">Stok İadesi</h5>
                            <h3 class="text-info">{{ number_format($stats['total_returns']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtreler -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock-movements') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="date_range" class="form-label">Hazır Tarih Aralıkları</label>
                            <select class="form-select" id="date_range" name="date_range">
                                <option value="">Özel tarih seç</option>
                                <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Bugün</option>
                                <option value="yesterday" {{ request('date_range') === 'yesterday' ? 'selected' : '' }}>Dün</option>
                                <option value="this_week" {{ request('date_range') === 'this_week' ? 'selected' : '' }}>Bu Hafta</option>
                                <option value="last_week" {{ request('date_range') === 'last_week' ? 'selected' : '' }}>Geçen Hafta</option>
                                <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>Bu Ay</option>
                                <option value="last_month" {{ request('date_range') === 'last_month' ? 'selected' : '' }}>Geçen Ay</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">İşlem Tipi</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tüm İşlemler</option>
                                <option value="giriş" {{ request('type') === 'giriş' ? 'selected' : '' }}>Stok Girişi</option>
                                <option value="çıkış" {{ request('type') === 'çıkış' ? 'selected' : '' }}>Stok Çıkışı</option>
                                <option value="iade" {{ request('type') === 'iade' ? 'selected' : '' }}>Stok İadesi</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="product_search" class="form-label">Ürün Ara</label>
                            <input type="text" class="form-control" id="product_search" name="product_search" 
                                   value="{{ request('product_search') }}" placeholder="Ürün adı veya barkod...">
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">Kullanıcı</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Tüm Kullanıcılar</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="customer_id" class="form-label">Müşteri</label>
                            <select class="form-select" id="customer_id" name="customer_id">
                                <option value="">Tüm Müşteriler</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Filtrele
                                </button>
                                <a href="{{ route('reports.stock-movements') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Temizle
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hareket Tablosu -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tarih</th>
                                    <th>Ref. No</th>
                                    <th>İşlem</th>
                                    <th>Ürün</th>
                                    <th>Miktar</th>
                                    <th>Önceki Stok</th>
                                    <th>Yeni Stok</th>
                                    <th>Kullanıcı</th>
                                    <th>Müşteri</th>
                                    <th>Not</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $movement->created_at->format('d.m.Y') }}</span>
                                            <br><small class="text-muted">{{ $movement->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($movement->reference_number)
                                                <span class="badge bg-secondary">{{ $movement->reference_number }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $movement->type_color }}">
                                                <i class="{{ $movement->type_icon }} me-1"></i>{{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $movement->product->name ?? 'Ürün Silinmiş' }}</strong>
                                            @if($movement->product && $movement->product->barcode)
                                                <br><small class="text-muted">{{ $movement->product->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($movement->quantity) }}</span>
                                        </td>
                                        <td>{{ number_format($movement->previous_stock) }}</td>
                                        <td>{{ number_format($movement->new_stock) }}</td>
                                        <td>
                                            <i class="bi bi-person me-1"></i>{{ $movement->user->name ?? 'Bilinmiyor' }}
                                        </td>
                                        <td>
                                            @if($movement->customer)
                                                <i class="bi bi-building me-1"></i>{{ $movement->customer->company_name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($movement->note)
                                                <span class="text-truncate" style="max-width: 150px;" title="{{ $movement->note }}">
                                                    {{ Str::limit($movement->note, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-outline-info" title="Görüntüle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($movement->product)
                                                    <a href="{{ route('products.show', $movement->product) }}" class="btn btn-outline-primary" title="Ürün Detayı">
                                                        <i class="bi bi-box-seam"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted fs-1"></i>
                                            <p class="text-muted mt-2">Belirtilen kriterlere uygun hareket bulunamadı</p>
                                            <a href="{{ route('reports.stock-movements') }}" class="btn btn-primary">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Filtreleri Temizle
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($movements->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $movements->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('date_range');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    
    // Hazır tarih aralığı seçildiğinde özel tarihleri temizle
    dateRangeSelect.addEventListener('change', function() {
        if (this.value) {
            dateFromInput.value = '';
            dateToInput.value = '';
        }
    });
    
    // Özel tarih seçildiğinde hazır aralığı temizle
    [dateFromInput, dateToInput].forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                dateRangeSelect.value = '';
            }
        });
    });
});
</script>
@endsection 