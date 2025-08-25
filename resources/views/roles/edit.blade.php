@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-shield-exclamation me-2"></i>Rol Düzenle: {{ $role->display_name }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>Görüntüle
                            </a>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Geri Dön
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($role->name === 'admin')
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Uyarı:</strong> Admin rolü sistem güvenliği için kısıtlı düzenlenebilir.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <!-- Rol Adı -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Rol Adı (Sistem) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $role->name) }}" required
                                   {{ $role->name === 'admin' ? 'readonly' : '' }}
                                   placeholder="Örnek: yonetici_asistani">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($role->name === 'admin')
                                <div class="form-text text-warning">Admin rolünün sistem adı değiştirilemez.</div>
                            @else
                                <div class="form-text">Sistem içinde kullanılacak benzersiz rol adı</div>
                            @endif
                        </div>

                        <!-- Görünen Ad -->
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Görünen Ad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   id="display_name" name="display_name" value="{{ old('display_name', $role->display_name) }}" required
                                   placeholder="Örnek: Yönetici Asistanı">
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Açıklama -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3"
                                      placeholder="Bu rolün sorumluluklarını ve yetkilerini açıklayın...">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Yetkiler -->
                        <div class="mb-3">
                            <label class="form-label">Yetkiler</label>
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Bu role verilecek yetkileri seçin</small>
                                        @if($role->name !== 'admin')
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                                    Tümünü Seç
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectNone">
                                                    Hiçbirini Seçme
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($role->name === 'admin')
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Admin rolünün yetkileri sistem güvenliği için değiştirilemez.
                                        </div>
                                    @endif
                                    
                                    <div class="row">
                                        @foreach($permissions as $key => $label)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                           id="permission_{{ $key }}" name="permissions[]" value="{{ $key }}"
                                                           {{ in_array($key, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }}
                                                           {{ $role->name === 'admin' ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @error('permissions')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Durum -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                                       {{ $role->name === 'admin' ? 'disabled' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktif Rol</strong>
                                    <small class="text-muted d-block">Bu rol kullanıcılara atanabilsin</small>
                                </label>
                                @if($role->name === 'admin')
                                    <div class="form-text text-warning">Admin rolü her zaman aktif olmalıdır.</div>
                                @endif
                            </div>
                        </div>

                        <!-- Rol Bilgileri -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Rol Bilgileri
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Oluşturulma:</small>
                                        <p>{{ $role->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Son Güncelleme:</small>
                                        <p>{{ $role->updated_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Kullanıcı Sayısı:</small>
                                        <p>{{ $role->users_count ?? $role->users()->count() }} kullanıcı</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($role->name !== 'admin')
        const selectAllBtn = document.getElementById('selectAll');
        const selectNoneBtn = document.getElementById('selectNone');
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    if (!checkbox.disabled) checkbox.checked = true;
                });
            });
        }
        
        if (selectNoneBtn) {
            selectNoneBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    if (!checkbox.disabled) checkbox.checked = false;
                });
            });
        }
    @endif

    @if($role->name === 'admin')
        // Admin rolü için hidden input'lar ekle
        const form = document.querySelector('form');
        
        // Admin rolünün tüm yetkilerini hidden olarak ekle
        @foreach($role->permissions ?? [] as $permission)
            const hiddenInput{{ $loop->index }} = document.createElement('input');
            hiddenInput{{ $loop->index }}.type = 'hidden';
            hiddenInput{{ $loop->index }}.name = 'permissions[]';
            hiddenInput{{ $loop->index }}.value = '{{ $permission }}';
            form.appendChild(hiddenInput{{ $loop->index }});
        @endforeach
        
        // is_active için de hidden input
        const hiddenActiveInput = document.createElement('input');
        hiddenActiveInput.type = 'hidden';
        hiddenActiveInput.name = 'is_active';
        hiddenActiveInput.value = '1';
        form.appendChild(hiddenActiveInput);
    @endif
});
</script>
@endsection 