@extends('layouts.app')

@section('title', 'Detail Pengiriman - Ekspedisi Nusantara')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Detail Pengiriman</h1>
                <p class="text-muted mb-0">Informasi lengkap pengiriman {{ $shipment->tracking_number }}</p>
            </div>
            <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-warning btn-lg">
                <i class="fas fa-edit me-2"></i>
                Edit Pengiriman
            </a>
        </div>
    </div>
</div>

<div class="container py-4">
    <!-- Status Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold text-dark mb-2">{{ $shipment->tracking_number }}</h3>
                    <p class="text-muted mb-3">Dibuat pada {{ $shipment->created_at->format('d F Y, H:i') }} WIB</p>
                    
                    <!-- Progress Timeline -->
                    <div class="progress-timeline">
                        <div class="d-flex align-items-center justify-content-between position-relative mb-4">
                            <!-- Pickup -->
                            <div class="timeline-step text-center">
                                <div class="timeline-icon {{ $shipment->status == 'pending' || $shipment->status == 'in_transit' || $shipment->status == 'delivered' ? 'active' : '' }}">
                                    <i class="fas fa-box"></i>
                                </div>
                                <small class="d-block mt-2 fw-medium">Pickup</small>
                            </div>
                            
                            <!-- Progress Line 1 -->
                            <div class="timeline-line {{ $shipment->status == 'in_transit' || $shipment->status == 'delivered' ? 'active' : '' }}"></div>
                            
                            <!-- Transit -->
                            <div class="timeline-step text-center">
                                <div class="timeline-icon {{ $shipment->status == 'in_transit' || $shipment->status == 'delivered' ? 'active' : '' }}">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <small class="d-block mt-2 fw-medium">Transit</small>
                            </div>
                            
                            <!-- Progress Line 2 -->
                            <div class="timeline-line {{ $shipment->status == 'delivered' ? 'active' : '' }}"></div>
                            
                            <!-- Delivered -->
                            <div class="timeline-step text-center">
                                <div class="timeline-icon {{ $shipment->status == 'delivered' ? 'active' : '' }}">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <small class="d-block mt-2 fw-medium">Delivered</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    @php
                        $badgeClass = 'warning';
                        $statusText = 'Menunggu Pickup';
                        if($shipment->status == 'in_transit') {
                            $badgeClass = 'info';
                            $statusText = 'Dalam Perjalanan';
                        }
                        if($shipment->status == 'delivered') {
                            $badgeClass = 'success';
                            $statusText = 'Terkirim';
                        }
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} fs-5 px-4 py-3 rounded-pill">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="row g-4">
        <!-- Sender Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user text-success me-2"></i>
                        Informasi Pengirim
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium">Nama</label>
                        <p class="fw-bold text-dark">{{ $shipment->sender_name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium">Alamat</label>
                        <p class="text-dark">{{ $shipment->sender_address }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium">Telepon</label>
                        <p class="text-dark">{{ $shipment->sender_phone }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipient Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-check text-warning me-2"></i>
                        Informasi Penerima
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium">Nama</label>
                        <p class="fw-bold text-dark">{{ $shipment->recipient_name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium">Alamat</label>
                        <p class="text-dark">{{ $shipment->recipient_address }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium">Telepon</label>
                        <p class="text-dark">{{ $shipment->recipient_phone }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box text-primary me-2"></i>
                        Informasi Paket
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium">Berat</label>
                        <p class="fw-bold text-dark">{{ number_format($shipment->weight, 2) }} kg</p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted fw-medium">Deskripsi</label>
                        <p class="text-dark">{{ $shipment->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-info me-2"></i>
                        Riwayat Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline-vertical">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold mb-1">Pengiriman dibuat</h6>
                                <small class="text-muted">{{ $shipment->created_at->format('d F Y, H:i') }} WIB</small>
                            </div>
                        </div>

                        @if($shipment->status == 'in_transit' || $shipment->status == 'delivered')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold mb-1">Dalam perjalanan</h6>
                                <small class="text-muted">{{ $shipment->updated_at->format('d F Y, H:i') }} WIB</small>
                            </div>
                        </div>
                        @endif

                        @if($shipment->status == 'delivered')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold mb-1">Paket terkirim</h6>
                                <small class="text-muted">{{ $shipment->updated_at->format('d F Y, H:i') }} WIB</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('shipments.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali ke Daftar Pengiriman
        </a>
    </div>
</div>

@push('styles')
<style>
.progress-timeline {
    padding: 20px 0;
}

.timeline-step {
    flex: 1;
    position: relative;
}

.timeline-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin: 0 auto;
    transition: all 0.3s ease;
}

.timeline-icon.active {
    background: #0d6efd;
    color: white;
}

.timeline-line {
    flex: 1;
    height: 3px;
    background: #e9ecef;
    margin: 0 10px;
    position: relative;
    top: -25px;
}

.timeline-line.active {
    background: #0d6efd;
}

.timeline-vertical .timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-vertical .timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-vertical .timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-vertical .timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 17px;
    width: 2px;
    height: calc(100% + 5px);
    background: #e9ecef;
}
</style>
@endpush
@endsection
