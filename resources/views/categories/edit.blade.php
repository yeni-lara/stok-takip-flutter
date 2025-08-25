<x-app-layout>
    <x-slot name="header">
        <i class="bi bi-tags me-2"></i>Kategori Düzenle: {{ $category->name }}
    </x-slot>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kategori Bilgilerini Düzenle</h5>
                    <div>
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-info me-2">
                            <i class="bi bi-eye me-2"></i>Görüntüle
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Kategori Adı <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $category->name) }}" 
                                   placeholder="Kategori adını giriniz"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Kategori adı benzersiz olmalıdır.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Kategori açıklaması (opsiyonel)">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktif Kategori</strong>
                                </label>
                                <div class="form-text">
                                    Aktif kategoriler sistemde kullanılabilir.
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>Kategoriyi Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kategori İstatistikleri -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Kategori İstatistikleri</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $category->products()->count() }}</h4>
                                <small class="text-muted">Toplam Ürün</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <h4 class="text-success">{{ $category->products()->active()->count() }}</h4>
                                <small class="text-muted">Aktif Ürün</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4 class="text-info">{{ $category->created_at->format('d.m.Y') }}</h4>
                            <small class="text-muted">Oluşturma Tarihi</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uyarı -->
            @if($category->products()->count() > 0)
                <div class="alert alert-warning mt-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Dikkat:</strong> Bu kategoriye ait {{ $category->products()->count() }} ürün bulunmaktadır. 
                    Kategoriyi pasif hale getirirseniz, bu ürünler yeni ürün eklerken görünmeyecektir.
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 