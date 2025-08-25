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
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('customers.store') }}" method="POST">
                                @csrf

                                <!-- Firma Adı -->
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Firma Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" value="{{ old('company_name') }}"
                                           placeholder="Örnek: ABC Market">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Durum -->
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

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Bilgilendirme
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-lightbulb me-2"></i>Öneriler</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Firma Adı:</strong> Zorunludur, müşterinizi tanımlar</li>
                                    <li><strong>Durum:</strong> Aktif müşteriler stok çıkışında görünür</li>
                                    
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
                                        <span>Aktif Müşteri:</span>
                                        <span>{{ \App\Models\Customer::where('is_active', true)->count() }}</span>
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