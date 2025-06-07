@extends('layouts.app')

@section('title', 'Edit Pengiriman')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Edit Pengiriman
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('shipments.update', $shipment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Informasi Pengiriman -->
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Pengiriman
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="tracking_number" class="form-label">Nomor Resi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                   id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number) }}" 
                                   required>
                            @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="weight" class="form-label">Berat (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                   id="weight" name="weight" value="{{ old('weight', number_format($shipment->weight / 1000, 2)) }}" 
                                   step="0.01" min="0.01" required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Masukkan berat dalam kilogram (kg)</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach(\App\Models\Shipment::getStatusOptions() as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $shipment->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Deskripsi Barang</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Deskripsi barang yang dikirim">{{ old('description', $shipment->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informasi Pengirim -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user me-2"></i>
                                Informasi Pengirim
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="sender_name" class="form-label">Nama Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sender_name') is-invalid @enderror" 
                                   id="sender_name" name="sender_name" value="{{ old('sender_name', $shipment->sender_name) }}" required>
                            @error('sender_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="sender_phone" class="form-label">Telepon Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sender_phone') is-invalid @enderror" 
                                   id="sender_phone" name="sender_phone" value="{{ old('sender_phone', $shipment->sender_phone) }}" required>
                            @error('sender_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="sender_address" class="form-label">Alamat Pengirim <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('sender_address') is-invalid @enderror" 
                                      id="sender_address" name="sender_address" rows="2" required>{{ old('sender_address', $shipment->sender_address) }}</textarea>
                            @error('sender_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informasi Penerima -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user-check me-2"></i>
                                Informasi Penerima
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="recipient_name" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" 
                                   id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $shipment->recipient_name) }}" required>
                            @error('recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="recipient_phone" class="form-label">Telepon Penerima <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('recipient_phone') is-invalid @enderror" 
                                   id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone', $shipment->recipient_phone) }}" required>
                            @error('recipient_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="recipient_address" class="form-label">Alamat Penerima <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('recipient_address') is-invalid @enderror" 
                                      id="recipient_address" name="recipient_address" rows="2" required>{{ old('recipient_address', $shipment->recipient_address) }}</textarea>
                            @error('recipient_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Pengiriman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
