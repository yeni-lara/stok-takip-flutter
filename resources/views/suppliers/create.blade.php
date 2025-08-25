@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Yeni Tedarikçi Ekle</h1>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Geri Dön
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('suppliers.store') }}" method="POST">
                                @csrf

                                <!-- Firma Bilgileri -->
                                <h5 class="border-bottom pb-2 mb-3">Firma Bilgileri</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Firma Adı <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}"
                                                   placeholder="Örnek: ABC Tedarik Ltd.">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_person" class="form-label">Yetkili Kişi</label>
                                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                                   id="contact_person" name="contact_person" value="{{ old('contact_person') }}"
                                                   placeholder="Örnek: Ahmet Yılmaz">
                                            @error('contact_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- İletişim Bilgileri -->
                                <h5 class="border-bottom pb-2 mb-3">İletişim Bilgileri</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Telefon</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                                   placeholder="0212 123 45 67">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">E-posta</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" 
                                                   placeholder="info@ornek.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Adres ve Diğer Bilgiler -->
                                <h5 class="border-bottom pb-2 mb-3">Diğer Bilgiler</h5>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Adres</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" 
                                              placeholder="Firma adresi...">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tax_number" class="form-label">Vergi Numarası</label>
                                            <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                                   id="tax_number" name="tax_number" value="{{ old('tax_number') }}"
                                                   placeholder="1234567890">
                                            @error('tax_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notlar</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="2" 
                                                      placeholder="Tedarikçi ile ilgili notlar...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Durum -->
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Tedarikçi aktif
                                    </label>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Tedarikçi Ekle
                                    </button>
                                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>İptal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Bilgilendirme
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-lightbulb me-2"></i>İpuçları</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Firma Adı:</strong> Zorunlu alan, tedarikçiyi tanımlar</li>
                                    <li><strong>Yetkili Kişi:</strong> İletişim kurulacak kişi</li>
                                    <li><strong>İletişim:</strong> Sipariş ve iletişim için önemli</li>
                                    <li><strong>Vergi No:</strong> Resmi işlemler için gerekli</li>
                                    <li><strong>Durum:</strong> Pasif tedarikçiler ürün eklerken görünmez</li>
                                </ul>
                            </div>

                            <div class="border-top pt-3">
                                <h6>Tedarikçi İstatistikleri</h6>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Toplam Tedarikçi:</span>
                                        <span>{{ \App\Models\Supplier::count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Aktif Tedarikçi:</span>
                                        <span>{{ \App\Models\Supplier::where('is_active', true)->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 