@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Başlık ve Eylemler -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>Kullanıcı Detayı
                </h2>
                <div class="btn-group">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Düzenle
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Sol Kolon - Kullanıcı Bilgileri -->
                <div class="col-md-4">
                    <!-- Kullanıcı Profil Kartı -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="avatar mb-3">
                                <span class="badge bg-primary rounded-circle p-4 fs-1">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->email }}</p>
                            
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                <span class="badge bg-secondary fs-6">{{ $user->role->display_name }}</span>
                                @if($user->is_active)
                                    <span class="badge bg-success fs-6">Aktif</span>
                                @else
                                    <span class="badge bg-danger fs-6">Pasif</span>
                                @endif
                                @if($user->id === auth()->id())
                                    <span class="badge bg-info fs-6">Siz</span>
                                @endif
                            </div>

                            <div class="text-start">
                                <small class="text-muted">Kayıt Tarihi:</small>
                                <p class="mb-2">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                                
                                <small class="text-muted">Son Güncelleme:</small>
                                <p class="mb-0">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rol Bilgileri -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-check me-2"></i>Rol ve Yetkiler
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="text-primary">{{ $user->role->display_name }}</h6>
                            <p class="text-muted mb-3">{{ $user->role->description }}</p>
                            
                            @if($user->role->permissions && count($user->role->permissions) > 0)
                                <small class="text-muted">Yetkiler:</small>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    @foreach($user->role->permissions as $permission)
                                        <span class="badge bg-light text-dark">{{ $permission }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon - İstatistikler ve Hareketler -->
                <div class="col-md-8">
                    <!-- Stok Hareket İstatistikleri -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-activity text-primary fs-1"></i>
                                    <h5 class="card-title mt-2">Toplam Hareket</h5>
                                    <h3 class="text-primary">{{ $stats['total_movements'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-box-arrow-in-down text-success fs-1"></i>
                                    <h5 class="card-title mt-2">Stok Girişi</h5>
                                    <h3 class="text-success">{{ $stats['total_entries'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-box-arrow-up text-warning fs-1"></i>
                                    <h5 class="card-title mt-2">Stok Çıkışı</h5>
                                    <h3 class="text-warning">{{ $stats['total_exits'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-arrow-counterclockwise text-info fs-1"></i>
                                    <h5 class="card-title mt-2">İade</h5>
                                    <h3 class="text-info">{{ $stats['total_returns'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Son Stok Hareketleri -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock-history me-2"></i>Son Stok Hareketleri
                                </h5>
                                <a href="{{ route('stock-movements.index', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-list me-1"></i>Tümünü Gör
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($recentMovements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tarih</th>
                                                <th>Ürün</th>
                                                <th>İşlem</th>
                                                <th>Miktar</th>
                                                <th>Müşteri</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentMovements as $movement)
                                                <tr>
                                                    <td>{{ $movement->created_at->format('d.m H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('products.show', $movement->product) }}" class="text-decoration-none">
                                                            {{ Str::limit($movement->product->name, 30) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $movement->type_color }}">
                                                            <i class="{{ $movement->type_icon }} me-1"></i>{{ $movement->type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold">{{ $movement->quantity }}</span>
                                                    </td>
                                                    <td>
                                                        @if($movement->customer)
                                                            <a href="{{ route('customers.show', $movement->customer) }}" class="text-decoration-none">
                                                                {{ Str::limit($movement->customer->company_name, 20) }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox text-muted fs-1"></i>
                                    <p class="text-muted mt-2">Henüz stok hareketi bulunmamaktadır.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    @if($user->is_active)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-lightning me-2"></i>Hızlı İşlemler
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if($user->hasPermission('stock_entry'))
                                        <div class="col-md-4">
                                            <a href="{{ route('stock.entry') }}" class="btn btn-outline-success w-100">
                                                <i class="bi bi-box-arrow-in-down me-2"></i>
                                                Stok Girişi Yap
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if($user->hasPermission('stock_exit'))
                                        <div class="col-md-4">
                                            <a href="{{ route('stock.exit') }}" class="btn btn-outline-warning w-100">
                                                <i class="bi bi-box-arrow-up me-2"></i>
                                                Stok Çıkışı Yap
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if($user->hasPermission('stock_return'))
                                        <div class="col-md-4">
                                            <a href="{{ route('stock.return') }}" class="btn btn-outline-info w-100">
                                                <i class="bi bi-arrow-counterclockwise me-2"></i>
                                                İade İşlemi
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 