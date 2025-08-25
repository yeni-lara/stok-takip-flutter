@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Yeni Müşteri Ekle</h1>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Geri Dön
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('customers.store') }}" method="POST">
                                @csrf

                                <!-- Müşteri Tipi -->
                                <div class="mb-4">
                                    <label class="form-label">Müşteri Tipi <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type" id="individual" 
                                                       value="individual" {{ old('type', 'individual') === 'individual' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="individual">
                                                    <i class="bi bi-person me-2"></i>Bireysel Müşteri
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type" id="corporate" 
                                                       value="corporate" {{ old('type') === 'corporate' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="corporate">
                                                    <i class="bi bi-building me-2"></i>Kurumsal Müşteri
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bireysel Müşteri Bilgileri -->
                                <div id="individualFields" class="customer-fields">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Ad <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="surname" class="form-label">Soyad <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                                                       id="surname" name="surname" value="{{ old('surname') }}">
                                                @error('surname')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kurumsal Müşteri Bilgileri -->
                                <div id="corporateFields" class="customer-fields" style="display: none;">
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">Firma Adı <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                               id="company_name" name="company_name" value="{{ old('company_name') }}">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tax_number" class="form-label">Vergi Numarası</label>
                                                <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                                       id="tax_number" name="tax_number" value="{{ old('tax_number') }}">
                                                @error('tax_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tax_office" class="form-label">Vergi Dairesi</label>
                                                <input type="text" class="form-control @error('tax_office') is-invalid @enderror" 
                                                       id="tax_office" name="tax_office" value="{{ old('tax_office') }}">
                                                @error('tax_office')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
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
                                                   placeholder="0532 123 45 67">
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
                                                   placeholder="ornek@email.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Adres Bilgileri -->
                                <h5 class="border-bottom pb-2 mb-3">Adres Bilgileri</h5>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Adres</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" 
                                              placeholder="Mahalle, Sokak, No...">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">Şehir</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city') }}" 
                                           placeholder="İstanbul">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Diğer Bilgiler -->
                                <h5 class="border-bottom pb-2 mb-3">Diğer Bilgiler</h5>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notlar</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Müşteri ile ilgili notlar...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Müşteri aktif
                                    </label>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Müşteri Ekle
                                    </button>
                                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
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
                                    <li><strong>Bireysel Müşteri:</strong> Ad ve soyad zorunludur</li>
                                    <li><strong>Kurumsal Müşteri:</strong> Firma adı zorunludur</li>
                                    <li>Telefon ve e-posta iletişim için önemlidir</li>
                                    <li>Vergi bilgileri kurumsal müşteriler için gereklidir</li>
                                    <li>Notlar alanına özel bilgiler ekleyebilirsiniz</li>
                                </ul>
                            </div>

                            <div class="border-top pt-3">
                                <h6>Müşteri İstatistikleri</h6>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Toplam Müşteri:</span>
                                        <span>{{ \App\Models\Customer::count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Bireysel:</span>
                                        <span>{{ \App\Models\Customer::individual()->count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Kurumsal:</span>
                                        <span>{{ \App\Models\Customer::corporate()->count() }}</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const individualRadio = document.getElementById('individual');
    const corporateRadio = document.getElementById('corporate');
    const individualFields = document.getElementById('individualFields');
    const corporateFields = document.getElementById('corporateFields');

    function toggleFields() {
        if (individualRadio.checked) {
            individualFields.style.display = 'block';
            corporateFields.style.display = 'none';
        } else {
            individualFields.style.display = 'none';
            corporateFields.style.display = 'block';
        }
    }

    individualRadio.addEventListener('change', toggleFields);
    corporateRadio.addEventListener('change', toggleFields);

    // Sayfa yüklendiğinde doğru alanları göster
    toggleFields();
});
</script>
@endpush 