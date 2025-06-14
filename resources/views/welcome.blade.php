@extends('layouts.app')

@section('title', 'Beranda - Sistem Pengiriman')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Hero Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shipping-fast text-primary" style="font-size: 4rem;"></i>
                </div>
                <h1 class="display-5 fw-bold text-primary mb-3">
                    Sistem Pengiriman
                </h1>
                <p class="lead text-muted mb-4">
                    Aplikasi untuk memantau dan mengelola pengiriman barang secara efisien.
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-lg me-md-2">
                        <i class="fas fa-list me-2"></i>
                        Lihat Semua Pengiriman
                    </a>
                    <a href="{{ route('shipments.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Pengiriman Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-cogs text-info" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="card-title">Pengelolaan Data</h5>
                        <p class="card-text text-muted">
                            Sistem yang handal untuk mengelola data pengiriman secara otomatis.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-sync-alt text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="card-title">Sinkronisasi Otomatis</h5>
                        <p class="card-text text-muted">
                            Data tersinkronisasi secara otomatis setiap kali ada perubahan.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-mobile-alt text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="card-title">Akses Multi Perangkat</h5>
                        <p class="card-text text-muted">
                            Dapat diakses dari berbagai perangkat dengan tampilan responsif.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistik Pengiriman
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-warning mb-1">
                                {{ \App\Models\Shipment::where('status', 'pending')->count() }}
                            </h3>
                            <small class="text-muted">Menunggu</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-info mb-1">
                                {{ \App\Models\Shipment::where('status', 'in_transit')->count() }}
                            </h3>
                            <small class="text-muted">Dalam Pengiriman</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-success mb-1">
                                {{ \App\Models\Shipment::where('status', 'delivered')->count() }}
                            </h3>
                            <small class="text-muted">Terkirim</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-primary mb-1">
                            {{ \App\Models\Shipment::count() }}
                        </h3>
                        <small class="text-muted">Total Pengiriman</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tracking Form -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>
                    Lacak Pengiriman
                </h5>
            </div>
            <div class="card-body">
                <form id="trackingForm">
                    <div class="input-group">
                        <input type="text" class="form-control" id="trackingNumber" 
                               placeholder="Masukkan nomor resi (contoh: EXP12345678)" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i>
                            Lacak
                        </button>
                    </div>
                </form>
                <div id="trackingResult" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('trackingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const trackingNumber = document.getElementById('trackingNumber').value;
    const resultDiv = document.getElementById('trackingResult');
    
    if (!trackingNumber) {
        return;
    }
    
    // Show loading
    resultDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>';
    resultDiv.style.display = 'block';
    
    // Fetch tracking data
    fetch(`/api/track?tracking_number=${encodeURIComponent(trackingNumber)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const shipment = data.data;
                let statusClass = 'warning';
                if (shipment.status === 'in_transit') statusClass = 'info';
                if (shipment.status === 'delivered') statusClass = 'success';
                
                resultDiv.innerHTML = `
                    <div class="alert alert-light border">
                        <h6 class="mb-3"><strong>Hasil Pelacakan:</strong></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>No. Resi:</strong> ${shipment.tracking_number}</p>
                                <p><strong>Pengirim:</strong> ${shipment.sender_name}</p>
                                <p><strong>Penerima:</strong> ${shipment.recipient_name}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Berat:</strong> ${shipment.weight} kg</p>
                                <p><strong>Status:</strong> <span class="badge bg-${statusClass}">${shipment.status_label}</span></p>
                                <p><strong>Terakhir Update:</strong> ${shipment.updated_at}</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Terjadi kesalahan saat melacak pengiriman.
                </div>
            `;
        });
});
</script>
@endpush
@endsection