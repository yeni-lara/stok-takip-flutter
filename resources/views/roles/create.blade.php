@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-shield-plus me-2"></i>Yeni Rol Ekle
                        </h4>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <!-- Rol Adı -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Rol Adı (Sistem) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="Örnek: yonetici_asistani">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Sistem içinde kullanılacak benzersiz rol adı (küçük harf, alt çizgi kullanın)</div>
                        </div>

                        <!-- Görünen Ad -->
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Görünen Ad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   id="display_name" name="display_name" value="{{ old('display_name') }}" required
                                   placeholder="Örnek: Yönetici Asistanı">
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kullanıcılara gösterilecek rol adı</div>
                        </div>

                        <!-- Açıklama -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3"
                                      placeholder="Bu rolün sorumluluklarını ve yetkilerini açıklayın...">{{ old('description') }}</textarea>
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
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                                Tümünü Seç
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectNone">
                                                Hiçbirini Seçme
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($permissions as $key => $label)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                           id="permission_{{ $key }}" name="permissions[]" value="{{ $key }}"
                                                           {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
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
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktif Rol</strong>
                                    <small class="text-muted d-block">Bu rol kullanıcılara atanabilsin</small>
                                </label>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Rol Oluştur
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
    const selectAllBtn = document.getElementById('selectAll');
    const selectNoneBtn = document.getElementById('selectNone');
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });
    
    selectNoneBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => checkbox.checked = false);
    });
    
    // Rol adı otomatik doldurma
    const displayNameInput = document.getElementById('display_name');
    const nameInput = document.getElementById('name');
    
    displayNameInput.addEventListener('input', function() {
        if (!nameInput.value || nameInput.value === nameInput.dataset.autoFilled) {
            const autoName = this.value
                .toLowerCase()
                .replace(/ğ/g, 'g')
                .replace(/ü/g, 'u')
                .replace(/ş/g, 's')
                .replace(/ı/g, 'i')
                .replace(/ö/g, 'o')
                .replace(/ç/g, 'c')
                .replace(/[^a-z0-9]/g, '_')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');
            
            nameInput.value = autoName;
            nameInput.dataset.autoFilled = autoName;
        }
    });
});
</script>
@endsection 