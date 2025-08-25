<x-app-layout>
    <x-slot name="header">
        <i class="bi bi-tags me-2"></i>Kategoriler
    </x-slot>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kategori Listesi</h5>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Yeni Kategori
                    </a>
                </div>
                <div class="card-body">
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ad</th>
                                        <th>Açıklama</th>
                                        <th>Ürün Sayısı</th>
                                        <th>Durum</th>
                                        <th>Oluşturma Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                <strong>{{ $category->name }}</strong>
                                            </td>
                                            <td>
                                                {{ Str::limit($category->description, 50) ?: '-' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $category->products_count }} ürün</span>
                                            </td>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Pasif</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $category->created_at->format('d.m.Y H:i') }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('categories.show', $category) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('categories.edit', $category) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if($category->products_count == 0)
                                                        <form action="{{ route('categories.destroy', $category) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                disabled 
                                                                title="Bu kategoriye ait ürünler olduğu için silinemez">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-tags fs-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Henüz kategori bulunmuyor</h5>
                            <p class="text-muted">İlk kategorinizi oluşturmak için yukarıdaki "Yeni Kategori" butonuna tıklayın.</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>İlk Kategoriyi Oluştur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 