@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Profil Düzenle
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>Profili Gör
                            </a>
                            <a href="{{ route('profile.password') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-key me-1"></i>Şifre Değiştir
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <!-- Kullanıcı Avatar -->
                        <div class="text-center mb-4">
                            <div class="avatar mb-3">
                                <span class="badge bg-primary rounded-circle p-4 fs-1">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                            <h5>{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->role->display_name }}</p>
                        </div>

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
                            @if($user->email_verified_at)
                                <div class="form-text text-success">
                                    <i class="bi bi-check-circle me-1"></i>E-posta adresi doğrulanmış
                                </div>
                            @else
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>E-posta adresi henüz doğrulanmamış
                                </div>
                            @endif
                        </div>

                        <!-- Hesap Bilgileri (Sadece Gösterim) -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Hesap Bilgileri
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Rol:</small>
                                        <p class="mb-2">
                                            <span class="badge bg-secondary">{{ $user->role->display_name }}</span>
                                        </p>
                                        <small class="text-muted">Durum:</small>
                                        <p class="mb-0">
                                            @if($user->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Pasif</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Hesap Oluşturma:</small>
                                        <p class="mb-2">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                                        <small class="text-muted">Son Güncelleme:</small>
                                        <p class="mb-0">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-muted">
                                    <small>
                                        <i class="bi bi-info-circle me-1"></i>
                                        Rol ve durum bilgilerinizi sadece yönetici değiştirebilir.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Güvenlik Uyarısı -->
                        <div class="alert alert-info">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Güvenlik:</strong> E-posta adresinizi değiştirirseniz, yeni adresinizi doğrulamanız gerekebilir.
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Geri Dön
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-key me-1"></i>Şifre Değiştir
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Değişiklikleri Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Yardım Kartı -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>Yardım
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Profil Bilgilerini Güncelleme</h6>
                            <ul class="small text-muted">
                                <li>Ad ve soyadınızı istediğiniz zaman değiştirebilirsiniz</li>
                                <li>E-posta adresinizi değiştirirseniz doğrulama gerekebilir</li>
                                <li>Rol ve durum bilgileriniz yönetici tarafından belirlenir</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Güvenlik</h6>
                            <ul class="small text-muted">
                                <li>Şifrenizi düzenli olarak değiştirin</li>
                                <li>Güçlü bir şifre kullanın</li>
                                <li>Hesap bilgilerinizi kimseyle paylaşmayın</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
