@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    Müşteri Düzenle: {{ $customer->full_name }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Görüntüle
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('customers.update', $customer) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Müşteri Tipi -->
                                <div class="mb-4">
                                    <label class="form-label">Müşteri Tipi <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type" id="individual" 
                                                       value="individual" {{ old('type', $customer->type) === 'individual' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="individual">
                                                    <i class="bi bi-person me-2"></i>Bireysel Müşteri
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type" id="corporate" 
                                                       value="corporate" {{ old('type', $customer->type) === 'corporate' ? 'checked' : '' }}>
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
                                                       id="name" name="name" value="{{ old('name', $customer->name) }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="surname" class="form-label">Soyad <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                                                       id="surname" name="surname" value="{{ old('surname', $customer->surname) }}">
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
                                               id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tax_number" class="form-label">Vergi Numarası</label>
                                                <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                                       id="tax_number" name="tax_number" value="{{ old('tax_number', $customer->tax_number) }}">
                                                @error('tax_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tax_office" class="form-label">Vergi Dairesi</label>
                                                <input type="text" class="form-control @error('tax_office') is-invalid @enderror" 
                                                       id="tax_office" name="tax_office" value="{{ old('tax_office', $customer->tax_office) }}">
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
                                                   id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" 
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
                                                   id="email" name="email" value="{{ old('email', $customer->email) }}" 
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
                                              placeholder="Mahalle, Sokak, No...">{{ old('address', $customer->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">Şehir</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $customer->city) }}" 
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
                                              placeholder="Müşteri ile ilgili notlar...">{{ old('notes', $customer->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Müşteri aktif
                                    </label>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Değişiklikleri Kaydet
                                    </button>
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>İptal
                                    </a>
                                    @if($customer->stockMovements()->count() === 0)
                                        <button type="button" class="btn btn-danger ms-auto" onclick="deleteCustomer()">
                                            <i class="bi bi-trash me-1"></i>Müşteriyi Sil
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Müşteri Bilgileri
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 text-sm">
                                <div class="col-6">
                                    <strong>Kayıt Tarihi:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $customer->created_at->format('d.m.Y H:i') }}
                                </div>
                                <div class="col-6">
                                    <strong>Son Güncelleme:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $customer->updated_at->format('d.m.Y H:i') }}
                                </div>
                                <div class="col-6">
                                    <strong>Toplam Çıkış:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $customer->total_stock_out }} adet
                                </div>
                                <div class="col-6">
                                    <strong>Toplam İade:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $customer->total_stock_return }} adet
                                </div>
                                @if($customer->last_transaction_date)
                                    <div class="col-6">
                                        <strong>Son İşlem:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ $customer->last_transaction_date->diffForHumans() }}
                                    </div>
                                @endif
                            </div>

                            @if($customer->stockMovements()->count() > 0)
                                <div class="alert alert-warning mt-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <small>Bu müşteriye ait {{ $customer->stockMovements()->count() }} stok hareketi bulunduğu için müşteri silinemez.</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightbulb me-2"></i>İpuçları
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="small mb-0">
                                <li><strong>Tip Değişikliği:</strong> Müşteri tipini değiştirirken ilgili alanları doldurmayı unutmayın</li>
                                <li><strong>İletişim:</strong> Güncel telefon ve e-posta bilgileri tutun</li>
                                <li><strong>Adres:</strong> Teslimat için doğru adres bilgisi önemli</li>
                                <li><strong>Durum:</strong> Pasif müşteriler listede gri görünür</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Silme Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Müşteriyi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <strong>{{ $customer->full_name }}</strong> isimli müşteriyi silmek istediğinizden emin misiniz?
                <br><br>
                Bu işlem geri alınamaz ve müşteriye ait tüm veriler silinir.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Evet, Sil</button>
                </form>
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

function deleteCustomer() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush 