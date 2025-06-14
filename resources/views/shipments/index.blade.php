@extends('layouts.app')

@section('title', 'Kelola Pengiriman - Ekspedisi Nusantara')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Kelola Pengiriman</h1>
                <p class="text-muted mb-0">Manajemen semua pengiriman Ekspedisi Nusantara</p>
            </div>
            <a href="{{ route('shipments.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>
                Buat Pengiriman
            </a>
        </div>
    </div>
</div>

<div class="container py-4">
    <!-- Filter dan Pencarian -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('shipments.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label fw-medium">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari berdasarkan resi, pengirim, atau penerima">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label fw-medium">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            @foreach(\App\Models\Shipment::getStatusOptions() as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>
                            Filter
                        </button>
                        <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>
                            Reset
                        </a>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-info w-100" id="syncButton" onclick="syncDatabases()">
                            <i class="fas fa-sync-alt me-1"></i>
                            <span id="syncText">Sync DB</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Pengiriman -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($shipments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 fw-semibold">No. Resi</th>
                                <th class="px-4 py-3 fw-semibold">Pengirim</th>
                                <th class="px-4 py-3 fw-semibold">Penerima</th>
                                <th class="px-4 py-3 fw-semibold">Berat</th>
                                <th class="px-4 py-3 fw-semibold">Status</th>
                                <th class="px-4 py-3 fw-semibold">Tanggal</th>
                                <th class="px-4 py-3 fw-semibold" width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipments as $shipment)
                                @if($shipment->id !== 0) <!-- Menyembunyikan pengiriman dengan ID 0 -->
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="font-monospace fw-bold text-primary">{{ $shipment->tracking_number }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-medium">{{ $shipment->sender_name }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-medium">{{ $shipment->recipient_name }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ number_format($shipment->weight, 2) }} kg</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $badgeClass = 'warning';
                                                if($shipment->status == 'in_transit') $badgeClass = 'info';
                                                if($shipment->status == 'delivered') $badgeClass = 'success';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }} px-3 py-2">
                                                {{ $shipment->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-muted">
                                            {{ $shipment->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('shipments.show', $shipment) }}" 
                                                   class="btn btn-outline-primary" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('shipments.edit', $shipment) }}" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="deleteShipment({{ $shipment->id }})" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-4 border-top">
                    <div>
                        <small class="text-muted">
                            Menampilkan {{ $shipments->firstItem() ?? 0 }} - {{ $shipments->lastItem() ?? 0 }} 
                            dari {{ $shipments->total() ?? 0 }} pengiriman
                        </small>
                    </div>
                    <div>
                        {{ $shipments->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold text-muted mb-3">Belum Ada Pengiriman</h4>
                    <p class="text-muted mb-4">Mulai dengan membuat pengiriman pertama Anda</p>
                    <a href="{{ route('shipments.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Buat Pengiriman Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus pengiriman ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sinkronisasi -->
<div class="modal fade" id="syncResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hasil Sinkronisasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="syncResultBody">
                Sedang melakukan sinkronisasi...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Oke</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteShipment(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/shipments/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function syncDatabases() {
    const button = document.getElementById('syncButton');
    const syncText = document.getElementById('syncText');
    const originalText = syncText.innerHTML;
    
    button.disabled = true;
    syncText.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Syncing...';
    
    // Tampilkan modal
    const resultModal = new bootstrap.Modal(document.getElementById('syncResultModal'));
    const resultBody = document.getElementById('syncResultBody');
    resultBody.innerHTML = '<div class="text-center"><span class="spinner-border" role="status"></span><p class="mt-3">Sedang melakukan sinkronisasi database...</p></div>';
    resultModal.show();
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch('/api/sync-databases', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let warningsHtml = '';
            if (data.warnings && data.warnings.length > 0) {
                warningsHtml = `
                    <div class="alert alert-warning mt-3">
                        <strong>Peringatan:</strong>
                        <ul class="mb-0 mt-2">
                            ${data.warnings.map(warning => `<li>${warning}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            resultBody.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message}
                </div>
                <p>Jumlah data yang disinkronkan: <strong>${data.synced_records}</strong></p>
                ${warningsHtml}
            `;
        } else {
            resultBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${data.message}
                </div>
                <p>Silakan periksa koneksi database dan coba lagi.</p>
            `;
        }
    })
    .catch(error => {
        console.error('Sync error:', error);
        resultBody.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Terjadi kesalahan saat sinkronisasi database.
            </div>
            <p>Detail error: ${error.message || 'Unknown error'}</p>
            <p>Silakan periksa koneksi database dan coba lagi.</p>
        `;
    })
    .finally(() => {
        syncText.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
@endpush
@endsection