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
                        <div class="col-md-3">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="İsim, telefon, email...">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Tip</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tümü</option>
                                <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>
                                    Bireysel
                                </option>
                                <option value="corporate" {{ request('type') === 'corporate' ? 'selected' : '' }}>
                                    Kurumsal
                                </option>
                            </select>
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
                            <label for="city" class="form-label">Şehir</label>
                            <input type="text" class="form-control" id="city" name="city" 
                                   value="{{ request('city') }}" placeholder="Şehir...">
                        </div>
                        <div class="col-md-2">
                            <label for="sort_by" class="form-label">Sıralama</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>
                                    Kayıt Tarihi
                                </option>
                                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>
                                    İsim
                                </option>
                                <option value="company_name" {{ request('sort_by') === 'company_name' ? 'selected' : '' }}>
                                    Firma
                                </option>
                                <option value="city" {{ request('sort_by') === 'city' ? 'selected' : '' }}>
                                    Şehir
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
                                        <th>Müşteri</th>
                                        <th>Tip</th>
                                        <th>İletişim</th>
                                        <th>Şehir</th>
                                        <th>Durum</th>
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
                                                        <span class="badge bg-{{ $customer->type === 'individual' ? 'primary' : 'info' }} rounded-pill">
                                                            <i class="bi bi-{{ $customer->type === 'individual' ? 'person' : 'building' }}"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $customer->full_name }}</h6>
                                                        @if($customer->type === 'corporate' && $customer->tax_number)
                                                            <small class="text-muted">VN: {{ $customer->tax_number }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $customer->type === 'individual' ? 'primary' : 'info' }}">
                                                    {{ $customer->type_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    @if($customer->phone)
                                                        <div><i class="bi bi-telephone me-1"></i>{{ $customer->phone }}</div>
                                                    @endif
                                                    @if($customer->email)
                                                        <div><i class="bi bi-envelope me-1"></i>{{ $customer->email }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $customer->city ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                                                    {{ $customer->is_active ? 'Aktif' : 'Pasif' }}
                                                </span>
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
                            <i class="bi bi-people display-1 text-muted"></i>
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