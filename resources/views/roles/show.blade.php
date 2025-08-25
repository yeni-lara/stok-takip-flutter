@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Başlık ve Eylemler -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>Rol Detayı
                </h2>
                <div class="btn-group">
                    @if($role->name !== 'admin')
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Düzenle
                        </a>
                    @endif
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Sol Kolon - Rol Bilgileri -->
                <div class="col-md-4">
                    <!-- Rol Profil Kartı -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="avatar mb-3">
                                <span class="badge bg-primary rounded-circle p-4 fs-1">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                            </div>
                            <h4>{{ $role->display_name }}</h4>
                            <p class="text-muted">{{ $role->description }}</p>
                            
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                <span class="badge bg-secondary fs-6">{{ $role->name }}</span>
                                @if($role->is_active)
                                    <span class="badge bg-success fs-6">Aktif</span>
                                @else
                                    <span class="badge bg-danger fs-6">Pasif</span>
                                @endif
                                @if($role->name === 'admin')
                                    <span class="badge bg-warning fs-6">Sistem Rolü</span>
                                @endif
                            </div>

                            <div class="text-start">
                                <small class="text-muted">Oluşturulma:</small>
                                <p class="mb-2">{{ $role->created_at->format('d.m.Y H:i') }}</p>
                                
                                <small class="text-muted">Son Güncelleme:</small>
                                <p class="mb-2">{{ $role->updated_at->format('d.m.Y H:i') }}</p>
                                
                                <small class="text-muted">Kullanıcı Sayısı:</small>
                                <p class="mb-0">{{ $role->users->count() }} kullanıcı</p>
                            </div>
                        </div>
                    </div>

                    <!-- Yetkiler Kartı -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-key me-2"></i>Yetkiler
                                <span class="badge bg-primary ms-2">{{ count($role->permissions ?? []) }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($role->permissions && count($role->permissions) > 0)
                                <div class="row">
                                    @foreach($role->permissions as $permission)
                                        <div class="col-12 mb-2">
                                            <span class="badge bg-light text-dark w-100 text-start">
                                                <i class="bi bi-check-circle text-success me-2"></i>
                                                {{ $permissions[$permission] ?? $permission }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-shield-x fs-3"></i>
                                    <p class="mt-2">Bu rolde henüz yetki tanımlanmamış</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon - Kullanıcılar -->
                <div class="col-md-8">
                    <!-- Bu Role Sahip Kullanıcılar -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-people me-2"></i>Bu Role Sahip Kullanıcılar
                                    <span class="badge bg-primary ms-2">{{ $role->users->count() }}</span>
                                </h5>
                                <a href="{{ route('users.index', ['role_id' => $role->id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-list me-1"></i>Tümünü Gör
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($role->users->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kullanıcı</th>
                                                <th>E-posta</th>
                                                <th>Durum</th>
                                                <th>Kayıt Tarihi</th>
                                                <th class="text-end">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($role->users as $user)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar me-2">
                                                                <span class="badge bg-primary rounded-circle p-2">
                                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <strong>{{ $user->name }}</strong>
                                                                @if($user->id === auth()->id())
                                                                    <span class="badge bg-info ms-1">Siz</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @if($user->is_active)
                                                            <span class="badge bg-success">Aktif</span>
                                                        @else
                                                            <span class="badge bg-danger">Pasif</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info" title="Görüntüle">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning" title="Düzenle">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-person-x text-muted fs-1"></i>
                                    <p class="text-muted mt-2">Bu role henüz kullanıcı atanmamış.</p>
                                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                                        <i class="bi bi-person-plus me-1"></i>İlk Kullanıcıyı Ekle
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>Hızlı İşlemler
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="{{ route('users.create') }}" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-person-plus me-2"></i>
                                        Yeni Kullanıcı Ekle
                                    </a>
                                </div>
                                
                                @if($role->name !== 'admin')
                                    <div class="col-md-4">
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-warning w-100">
                                            <i class="bi bi-pencil me-2"></i>
                                            Rolü Düzenle
                                        </a>
                                    </div>
                                @endif
                                
                                <div class="col-md-4">
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Rol Listesi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İstatistikler -->
                    @if($role->users->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-bar-chart me-2"></i>İstatistikler
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <h3 class="text-success">{{ $role->users->where('is_active', true)->count() }}</h3>
                                        <small class="text-muted">Aktif Kullanıcı</small>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h3 class="text-danger">{{ $role->users->where('is_active', false)->count() }}</h3>
                                        <small class="text-muted">Pasif Kullanıcı</small>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h3 class="text-info">{{ count($role->permissions ?? []) }}</h3>
                                        <small class="text-muted">Toplam Yetki</small>
                                    </div>
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