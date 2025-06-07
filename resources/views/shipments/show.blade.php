@extends('layouts.app')

@section('title', 'Detail Pengiriman')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-eye me-2"></i>
                Detail Pengiriman
            </h2>
            <div>
                <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>
                    Edit
                </a>
                <a href="{{ route('shipments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Header Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-2">{{ $shipment->tracking_number }}</h3>
                        <p class="text-muted mb-0">Dibuat pada {{ $shipment->formatted_created_at }}</p>
                    </div>
                    <div class="col-md-4">
                        @php
                            $badgeClass = 'warning';
                            if($shipment->status == 'in_transit') $badgeClass = 'info';
                            if($shipment->status == 'delivered') $badgeClass = 'success';
                        @endphp
                        <span class="badge bg-{{ $badgeClass }} fs-6 px-3 py-2">
                            {{ $shipment->status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Informasi Pengirim -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user text-primary me-2"></i>
                            Informasi Pengirim
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Nama:</strong><br>
                            {{ $shipment->sender_name }}
                        </div>
                        <div class="mb-3">
                            <strong>Alamat:</strong><br>
                            {{ $shipment->sender_address }}
                        </div>
                        <div class="mb-0">
                            <strong>Telepon:</strong><br>
                            {{ $shipment->sender_phone }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Penerima -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            Informasi Penerima
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Nama:</strong><br>
                            {{ $shipment->recipient_name }}
                        </div>
                        <div class="mb-3">
                            <strong>Alamat:</strong><br>
                            {{ $shipment->recipient_address }}
                        </div>
                        <div class="mb-0">
                            <strong>Telepon:</strong><br>
                            {{ $shipment->recipient_phone }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Paket -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box text-info me-2"></i>
                    Informasi Paket
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Berat:</strong><br>
                        {{ number_format($shipment->weight / 1000, 2) }} kg
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong><br>
                        {{ $shipment->status_label }}
                    </div>
                    <div class="col-md-3">
                        <strong>Dibuat:</strong><br>
                        {{ $shipment->formatted_created_at }}
                    </div>
                    <div class="col-md-3">
                        <strong>Diupdate:</strong><br>
                        {{ $shipment->formatted_updated_at }}
                    </div>
                </div>
                @if($shipment->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Deskripsi:</strong><br>
                        {{ $shipment->description }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history text-warning me-2"></i>
                    Timeline Pengiriman
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <!-- Created -->
                    <div class="timeline-item completed">
                        <h6>Pengiriman Dibuat</h6>
                        <small class="text-muted">{{ $shipment->formatted_created_at }}</small>
                        <p class="mb-0">Paket telah terdaftar dalam sistem</p>
                    </div>

                    <!-- In Transit -->
                    @if($shipment->status == 'in_transit' || $shipment->status == 'delivered')
                    <div class="timeline-item {{ $shipment->status == 'delivered' ? 'completed' : '' }}">
                        <h6>Dalam Pengiriman</h6>
                        <small class="text-muted">{{ $shipment->formatted_updated_at }}</small>
                        <p class="mb-0">Paket sedang dalam perjalanan ke tujuan</p>
                    </div>
                    @endif

                    <!-- Delivered -->
                    @if($shipment->status == 'delivered')
                    <div class="timeline-item completed">
                        <h6>Terkirim</h6>
                        <small class="text-muted">{{ $shipment->formatted_updated_at }}</small>
                        <p class="mb-0">Paket telah berhasil diterima oleh penerima</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
