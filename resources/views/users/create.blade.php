@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-person-plus me-2"></i>Yeni Kullanıcı Ekle
                        </h4>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <!-- Ad Soyad -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- E-posta -->
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta Adresi <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
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
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Şifre -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
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
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Şifre Doğrulama <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="bi bi-eye" id="eyeIconConfirmation"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Durum -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktif Kullanıcı</strong>
                                    <small class="text-muted d-block">Bu kullanıcı sisteme giriş yapabilsin</small>
                                </label>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Kullanıcı Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Rol Bilgileri Kartı -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Rol Açıklamaları
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($roles as $role)
                        <div class="mb-3">
                            <h6 class="text-primary">{{ $role->display_name }}</h6>
                            <p class="text-muted mb-2">{{ $role->description }}</p>
                            @if($role->permissions && count($role->permissions) > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-light text-dark">{{ $permission }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Şifre görünürlük toggle
    function togglePasswordVisibility(inputId, buttonId, iconId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        const icon = document.getElementById(iconId);
        
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
    
    togglePasswordVisibility('password', 'togglePassword', 'eyeIcon');
    togglePasswordVisibility('password_confirmation', 'togglePasswordConfirmation', 'eyeIconConfirmation');
});
</script>
@endsection 