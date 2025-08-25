@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Müşteriler</h1>
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Yeni Müşteri
                </a>
            </div>

            <!-- Filtreleme -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Firma Adı Ara</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Firma adı...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tümü</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                    Pasif
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort_by" class="form-label">Sıralama</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>
                                    Kayıt Tarihi
                                </option>
                                <option value="company_name" {{ request('sort_by') === 'company_name' ? 'selected' : '' }}>
                                    Firma Adı
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="sort_dir" class="form-label">Yön</label>
                            <select class="form-select" id="sort_dir" name="sort_dir">
                                <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>↓</option>
                                <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>↑</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filtrele
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Temizle
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Müşteri Listesi -->
            <div class="card">
                <div class="card-body">
                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Firma Adı</th>
                                        <th>Durum</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>Son İşlem</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span class="badge bg-primary rounded-pill">
                                                            <i class="bi bi-building"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $customer->company_name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                                                    {{ $customer->is_active ? 'Aktif' : 'Pasif' }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $customer->created_at->format('d.m.Y') }}</small>
                                            </td>
                                            <td>
                                                @if($customer->last_transaction_date)
                                                    <small class="text-muted">
                                                        {{ $customer->last_transaction_date->diffForHumans() }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('customers.show', $customer) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Görüntüle">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('customers.edit', $customer) }}" 
                                                       class="btn btn-sm btn-outline-secondary" title="Düzenle">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if($customer->stockMovements()->count() === 0)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                title="Sil" onclick="deleteCustomer('{{ $customer->id }}')">
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
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Toplam {{ $customers->total() }} müşteri ({{ $customers->firstItem() }}-{{ $customers->lastItem() }})
                            </div>
                            {{ $customers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-building display-1 text-muted"></i>
                            <h4 class="mt-3">Müşteri bulunamadı</h4>
                            <p class="text-muted">Filtreleri değiştirin veya yeni müşteri ekleyin.</p>
                            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>İlk Müşteriyi Ekle
                            </a>
                        </div>
                    @endif
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
                Bu müşteriyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteCustomer(customerId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `{{ route('customers.index') }}/${customerId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush 