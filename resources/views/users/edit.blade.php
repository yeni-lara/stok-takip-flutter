@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-person-gear me-2"></i>Kullanıcı Düzenle: {{ $user->name }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>Görüntüle
                            </a>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Geri Dön
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Ad Soyad -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- E-posta -->
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta Adresi <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Rol -->
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                <option value="">Rol Seçiniz</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($user->id === 1)
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Ana admin kullanıcısının rolü değiştirilemez.
                                </div>
                            @endif
                        </div>

                        <!-- Şifre Değiştirme Bölümü -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-key me-2"></i>Şifre Değiştir (Opsiyonel)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Şifre alanlarını boş bırakırsanız mevcut şifre değişmeyecektir.
                                </div>

                                <!-- Yeni Şifre -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Yeni Şifre</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Şifre en az 8 karakter olmalıdır.</div>
                                </div>

                                <!-- Şifre Doğrulama -->
                                <div class="mb-0">
                                    <label for="password_confirmation" class="form-label">Yeni Şifre Doğrulama</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                               id="password_confirmation" name="password_confirmation">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                            <i class="bi bi-eye" id="eyeIconConfirmation"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Durum -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktif Kullanıcı</strong>
                                    <small class="text-muted d-block">Bu kullanıcı sisteme giriş yapabilsin</small>
                                </label>
                                @if($user->id === auth()->id())
                                    <div class="form-text text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Kendi hesabınızı pasif yapamazsınız.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Kullanıcı Bilgileri -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Kullanıcı Bilgileri
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Kayıt Tarihi:</small>
                                        <p>{{ $user->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Son Güncelleme:</small>
                                        <p>{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
    // Admin kullanıcısının rolünü değiştirmeyi engelle
    @if($user->id === 1)
        document.getElementById('role_id').disabled = true;
    @endif

    // Kendi hesabının durumunu değiştirmeyi engelle
    @if($user->id === auth()->id())
        // Hidden input ekle ki form submit olduğunda is_active değeri gönderilsin
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'is_active';
        hiddenInput.value = '1';
        document.querySelector('form').appendChild(hiddenInput);
    @endif

    // Şifre görünürlük toggle
    function togglePasswordVisibility(inputId, buttonId, iconId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        const icon = document.getElementById(iconId);
        
        if (input && button && icon) {
            button.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'bi bi-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'bi bi-eye';
                }
            });
        }
    }
    
    togglePasswordVisibility('password', 'togglePassword', 'eyeIcon');
    togglePasswordVisibility('password_confirmation', 'togglePasswordConfirmation', 'eyeIconConfirmation');
});
</script>
@endsection 