@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-key me-2"></i>Şifre Değiştir
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-info">
                                <i class="bi bi-person-circle me-1"></i>Profile Dön
                            </a>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil me-1"></i>Profil Düzenle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Güvenlik Uyarısı -->
                    <div class="alert alert-warning">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        <strong>Güvenlik:</strong> Şifrenizi değiştirdikten sonra tüm cihazlardan çıkış yapmanız önerilir.
                    </div>

                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('patch')

                        <!-- Mevcut Şifre -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mevcut Şifre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required autofocus>
                                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                    <i class="bi bi-eye" id="eyeIconCurrent"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Güvenlik için mevcut şifrenizi girin</div>
                        </div>

                        <!-- Yeni Şifre -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Yeni Şifre <span class="text-danger">*</span></label>
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
                            <div class="form-text">Şifre en az 8 karakter olmalıdır</div>
                        </div>

                        <!-- Yeni Şifre Doğrulama -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Yeni Şifre Doğrulama <span class="text-danger">*</span></label>
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
                            <div class="form-text">Yeni şifrenizi tekrar girin</div>
                        </div>

                        <!-- Şifre Gücü Göstergesi -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-shield-check me-2"></i>Şifre Güvenlik Kontrol
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="passwordStrength" class="mb-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="strengthText" class="text-muted">Şifre girin...</small>
                                </div>
                                
                                <div class="row small text-muted">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li id="length" class="text-muted">
                                                <i class="bi bi-circle me-1"></i>En az 8 karakter
                                            </li>
                                            <li id="uppercase" class="text-muted">
                                                <i class="bi bi-circle me-1"></i>Büyük harf (A-Z)
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li id="lowercase" class="text-muted">
                                                <i class="bi bi-circle me-1"></i>Küçük harf (a-z)
                                            </li>
                                            <li id="number" class="text-muted">
                                                <i class="bi bi-circle me-1"></i>Rakam (0-9)
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Şifreyi Değiştir
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Güvenlik İpuçları -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Güvenlik İpuçları
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">Güçlü Şifre İçin:</h6>
                            <ul class="small text-muted">
                                <li>En az 8-12 karakter kullanın</li>
                                <li>Büyük ve küçük harf karıştırın</li>
                                <li>Rakam ve özel karakterler ekleyin</li>
                                <li>Kişisel bilgilerinizi kullanmayın</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger">Kaçınmanız Gerekenler:</h6>
                            <ul class="small text-muted">
                                <li>Yaygın şifreler (123456, password)</li>
                                <li>Kişisel bilgiler (doğum tarihi, isim)</li>
                                <li>Aynı şifreyi her yerde kullanma</li>
                                <li>Şifrenizi başkalarıyla paylaşma</li>
                            </ul>
                        </div>
                    </div>
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
    
    togglePasswordVisibility('current_password', 'toggleCurrentPassword', 'eyeIconCurrent');
    togglePasswordVisibility('password', 'togglePassword', 'eyeIcon');
    togglePasswordVisibility('password_confirmation', 'togglePasswordConfirmation', 'eyeIconConfirmation');

    // Şifre gücü kontrolü
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const lengthCheck = document.getElementById('length');
    const uppercaseCheck = document.getElementById('uppercase');
    const lowercaseCheck = document.getElementById('lowercase');
    const numberCheck = document.getElementById('number');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let score = 0;
        let feedback = [];

        // Uzunluk kontrolü
        if (password.length >= 8) {
            lengthCheck.className = 'text-success';
            lengthCheck.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>En az 8 karakter';
            score++;
        } else {
            lengthCheck.className = 'text-muted';
            lengthCheck.innerHTML = '<i class="bi bi-circle me-1"></i>En az 8 karakter';
        }

        // Büyük harf kontrolü
        if (/[A-Z]/.test(password)) {
            uppercaseCheck.className = 'text-success';
            uppercaseCheck.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Büyük harf (A-Z)';
            score++;
        } else {
            uppercaseCheck.className = 'text-muted';
            uppercaseCheck.innerHTML = '<i class="bi bi-circle me-1"></i>Büyük harf (A-Z)';
        }

        // Küçük harf kontrolü
        if (/[a-z]/.test(password)) {
            lowercaseCheck.className = 'text-success';
            lowercaseCheck.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Küçük harf (a-z)';
            score++;
        } else {
            lowercaseCheck.className = 'text-muted';
            lowercaseCheck.innerHTML = '<i class="bi bi-circle me-1"></i>Küçük harf (a-z)';
        }

        // Rakam kontrolü
        if (/[0-9]/.test(password)) {
            numberCheck.className = 'text-success';
            numberCheck.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Rakam (0-9)';
            score++;
        } else {
            numberCheck.className = 'text-muted';
            numberCheck.innerHTML = '<i class="bi bi-circle me-1"></i>Rakam (0-9)';
        }

        // Puan hesaplama ve görsel güncelleme
        const percentage = (score / 4) * 100;
        strengthBar.style.width = percentage + '%';

        if (password.length === 0) {
            strengthBar.className = 'progress-bar';
            strengthText.textContent = 'Şifre girin...';
            strengthText.className = 'text-muted';
        } else if (score <= 1) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Zayıf şifre';
            strengthText.className = 'text-danger';
        } else if (score <= 2) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Orta güçlükte şifre';
            strengthText.className = 'text-warning';
        } else if (score <= 3) {
            strengthBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'İyi şifre';
            strengthText.className = 'text-info';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Güçlü şifre';
            strengthText.className = 'text-success';
        }
    });
});
</script>
@endsection 