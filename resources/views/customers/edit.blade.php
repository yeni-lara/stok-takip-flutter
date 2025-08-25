@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    Müşteri Düzenle: {{ $customer->company_name }}
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
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('customers.update', $customer) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Firma Adı -->
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Firma Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}"
                                           placeholder="Örnek: ABC Market">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Durum -->
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

                <div class="col-lg-6">
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
                                <li><strong>Firma Adı:</strong> Müşteriyi tanımlayan ana bilgi</li>
                                <li><strong>Durum:</strong> Pasif müşteriler stok çıkışında görünmez</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Silme Modal -->
@if($customer->stockMovements()->count() === 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Müşteriyi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <strong>{{ $customer->company_name }}</strong> isimli müşteriyi silmek istediğinizden emin misiniz?
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
@endif
@endsection

@push('scripts')
<script>
function deleteCustomer() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush 