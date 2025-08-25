@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-people me-2"></i>Kullanıcı Yönetimi
                </h2>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Yeni Kullanıcı
                </a>
            </div>

            <!-- Filtreler -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Ad veya e-posta ile ara...">
                        </div>
                        <div class="col-md-3">
                            <label for="role_id" class="form-label">Rol</label>
                            <select class="form-select" id="role_id" name="role_id">
                                <option value="">Tüm Roller</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tüm Durumlar</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- İstatistik Kartları -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-people text-primary fs-1"></i>
                            <h5 class="card-title mt-2">Toplam Kullanıcı</h5>
                            <h3 class="text-primary">{{ $users->total() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-person-check text-success fs-1"></i>
                            <h5 class="card-title mt-2">Aktif Kullanıcı</h5>
                            <h3 class="text-success">{{ App\Models\User::where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-shield-check text-info fs-1"></i>
                            <h5 class="card-title mt-2">Admin</h5>
                            <h3 class="text-info">{{ App\Models\User::whereHas('role', function($q) { $q->where('name', 'admin'); })->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-person-x text-danger fs-1"></i>
                            <h5 class="card-title mt-2">Pasif Kullanıcı</h5>
                            <h3 class="text-danger">{{ App\Models\User::where('is_active', false)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kullanıcılar Tablosu -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Ad Soyad</th>
                                    <th>E-posta</th>
                                    <th>Rol</th>
                                    <th>Durum</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>Son Aktivite</th>
                                    <th class="text-end">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
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
                                            <span class="badge bg-secondary">{{ $user->role->display_name }}</span>
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            @if($user->updated_at->diffInDays() < 7)
                                                <span class="text-success">{{ $user->updated_at->diffForHumans() }}</span>
                                            @else
                                                {{ $user->updated_at->format('d.m.Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info" title="Görüntüle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning" title="Düzenle">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if($user->id !== 1 && $user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" 
                                                          onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Sil">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted fs-1"></i>
                                            <p class="text-muted mt-2">Kullanıcı bulunamadı</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 