@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Başlık ve Eylemler -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>Profilim
                </h2>
                <div class="btn-group">
                    <a href="{{ route('profile.edit') }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Düzenle
                    </a>
                    <a href="{{ route('profile.password') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-key me-1"></i>Şifre Değiştir
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
                            </div>

                            <div class="text-start">
                                <small class="text-muted">Hesap Oluşturma:</small>
                                <p class="mb-2">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                                
                                <small class="text-muted">Son Güncelleme:</small>
                                <p class="mb-0">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>Hızlı İşlemler
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($user->hasPermission('stock_entry'))
                                    <a href="{{ route('stock.entry') }}" class="btn btn-outline-success">
                                        <i class="bi bi-box-arrow-in-down me-2"></i>
                                        Stok Girişi
                                    </a>
                                @endif
                                
                                @if($user->hasPermission('stock_exit'))
                                    <a href="{{ route('stock.exit') }}" class="btn btn-outline-warning">
                                        <i class="bi bi-box-arrow-up me-2"></i>
                                        Stok Çıkışı
                                    </a>
                                @endif
                                
                                @if($user->hasPermission('stock_return'))
                                    <a href="{{ route('stock.return') }}" class="btn btn-outline-info">
                                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                                        İade İşlemi
                                    </a>
                                @endif
                                
                                @if($user->hasPermission('view_reports'))
                                    <a href="{{ route('stock-movements.index', ['user_id' => $user->id]) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-clock-history me-2"></i>
                                        Hareketlerimi Gör
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Hesap Ayarları -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-gear me-2"></i>Hesap Ayarları
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-pencil me-2"></i>
                                    Profil Düzenle
                                </a>
                                <a href="{{ route('profile.password') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-key me-2"></i>
                                    Şifre Değiştir
                                </a>
                                @if($user->id !== 1)
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                        <i class="bi bi-trash me-2"></i>
                                        Hesabı Sil
                                    </button>
                                @endif
                            </div>
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

                    <!-- Bu Ay İstatistikleri -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-month me-2"></i>Bu Ay Performansı
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col">
                                    <h3 class="text-primary">{{ $stats['this_month_movements'] }}</h3>
                                    <small class="text-muted">Bu Ay İşlem</small>
                                </div>
                                <div class="col">
                                    <h3 class="text-success">
                                        {{ $stats['total_movements'] > 0 ? round(($stats['this_month_movements'] / $stats['total_movements']) * 100, 1) : 0 }}%
                                    </h3>
                                    <small class="text-muted">Toplam Oranı</small>
                                </div>
                                <div class="col">
                                    <h3 class="text-info">{{ now()->diffInDays($user->created_at) }}</h3>
                                    <small class="text-muted">Gün Deneyim</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Son Stok Hareketleri -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock-history me-2"></i>Son Stok Hareketlerim
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
                                    @if($user->hasPermission('stock_entry'))
                                        <a href="{{ route('stock.entry') }}" class="btn btn-primary">
                                            <i class="bi bi-box-arrow-in-down me-1"></i>İlk İşlemini Yap
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hesap Silme Modal -->
@if($user->id !== 1)
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Hesabı Sil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Uyarı:</strong> Bu işlem geri alınamaz! Hesabınız kalıcı olarak silinecektir.
                </div>
                <p>Hesabınızı silmek istediğinizden emin misiniz? Bu işlem:</p>
                <ul>
                    <li>Hesabınızı kalıcı olarak silecek</li>
                    <li>Tüm verilerinizi kaldıracak</li>
                    <li>Sisteme erişiminizi sonlandıracak</li>
                </ul>
                
                @if($stats['total_movements'] > 0)
                    <div class="alert alert-warning">
                        <strong>Not:</strong> {{ $stats['total_movements'] }} adet stok hareket kaydınız bulunmaktadır. 
                        Bu durumda hesabınız silinemez. Lütfen yöneticiyle iletişime geçin.
                    </div>
                @else
                    <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                        @csrf
                        @method('delete')
                        <div class="mb-3">
                            <label for="password" class="form-label">İşlemi onaylamak için şifrenizi girin:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </form>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                @if($stats['total_movements'] === 0)
                    <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Hesabı Sil
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection 