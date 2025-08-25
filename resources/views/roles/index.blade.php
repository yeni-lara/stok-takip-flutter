@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-shield-check me-2"></i>Roller & Yetkiler
                </h1>
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Yeni Rol
                </a>
            </div>

            <div class="row">
                @foreach($roles as $role)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 {{ !$role->is_active ? 'border-secondary' : '' }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-{{ $role->name === 'admin' ? 'star-fill text-warning' : ($role->name === 'yardımcı' ? 'person-badge' : 'truck') }} me-2"></i>
                                    {{ $role->display_name }}
                                    @if(!$role->is_active)
                                        <span class="badge bg-secondary ms-2">Pasif</span>
                                    @endif
                                </h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('roles.show', $role) }}">
                                                <i class="bi bi-eye me-2"></i>Görüntüle
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('roles.edit', $role) }}">
                                                <i class="bi bi-pencil me-2"></i>Düzenle
                                            </a>
                                        </li>
                                        @if($role->name !== 'admin' && $role->users_count === 0)
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteRole('{{ $role->id }}')">
                                                    <i class="bi bi-trash me-2"></i>Sil
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">{{ $role->description }}</p>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="small">Yetkiler</strong>
                                        <span class="badge bg-primary">{{ count($role->permissions) }}</span>
                                    </div>
                                    <div class="permissions-preview">
                                        @if(count($role->permissions) > 0)
                                            @foreach(array_slice($role->permissions, 0, 3) as $permission)
                                                <span class="badge bg-light text-dark me-1 mb-1 small">
                                                    {{ $permission }}
                                                </span>
                                            @endforeach
                                            @if(count($role->permissions) > 3)
                                                <span class="badge bg-secondary small">+{{ count($role->permissions) - 3 }} daha</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">Yetki tanımlanmamış</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="border-top pt-3">
                                    <div class="row text-center small">
                                        <div class="col-6">
                                            <div class="text-muted">Kullanıcılar</div>
                                            <div class="h5 mb-0 {{ $role->users_count > 0 ? 'text-primary' : 'text-muted' }}">
                                                {{ $role->users_count }}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted">Durum</div>
                                            <div class="h6 mb-0">
                                                <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }}">
                                                    {{ $role->is_active ? 'Aktif' : 'Pasif' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Görüntüle
                                    </a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-pencil me-1"></i>Düzenle
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($roles->count() === 0)
                <div class="text-center py-5">
                    <i class="bi bi-shield-check display-1 text-muted"></i>
                    <h4 class="mt-3">Henüz rol tanımlanmamış</h4>
                    <p class="text-muted">Sistem yönetimi için roller oluşturun.</p>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>İlk Rolü Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Silme Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rolü Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bu rolü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
                <br><br>
                <div class="alert alert-warning">
                    <strong>Uyarı:</strong> Bu role sahip kullanıcılar varsa rol silinemez.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteRole(roleId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `{{ route('roles.index') }}/${roleId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush 