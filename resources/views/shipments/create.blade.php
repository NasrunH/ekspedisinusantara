@extends('layouts.app')

@section('title', 'Buat Pengiriman Baru - Ekspedisi Nusantara')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="container py-4">
        <h1 class="h2 fw-bold text-dark mb-1">Buat Pengiriman Baru</h1>
        <p class="text-muted mb-0">Isi form di bawah untuk membuat pengiriman baru</p>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('shipments.store') }}" method="POST">
                        @csrf
                        
                        <!-- Informasi Pengiriman -->
                        <div class="mb-5">
                            <h4 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informasi Pengiriman
                            </h4>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="id" class="form-label fw-medium">ID Pengiriman <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('id') is-invalid @enderror" 
                                           id="id" name="id" value="{{ old('id', $nextId) }}" 
                                           placeholder="1" required>
                                    @error('id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">ID unik untuk pengiriman ini</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="tracking_number" class="form-label fw-medium">Nomor Resi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                           id="tracking_number" name="tracking_number" value="{{ old('tracking_number') }}" 
                                           placeholder="EXP12345678" required>
                                    @error('tracking_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="weight" class="form-label fw-medium">Berat (kg) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                           id="weight" name="weight" value="{{ old('weight') }}" 
                                           step="0.01" min="0.01" required>
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        @foreach(\App\Models\Shipment::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="description" class="form-label fw-medium">Deskripsi Barang</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Deskripsi barang yang dikirim">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pengirim -->
                        <div class="mb-5">
                            <h4 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                                <i class="fas fa-user text-success me-2"></i>
                                Informasi Pengirim
                            </h4>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="sender_name" class="form-label fw-medium">Nama Pengirim <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sender_name') is-invalid @enderror" 
                                           id="sender_name" name="sender_name" value="{{ old('sender_name') }}" required>
                                    @error('sender_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="sender_phone" class="form-label fw-medium">Telepon Pengirim <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sender_phone') is-invalid @enderror" 
                                           id="sender_phone" name="sender_phone" value="{{ old('sender_phone') }}" required>
                                    @error('sender_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="sender_address" class="form-label fw-medium">Alamat Pengirim <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('sender_address') is-invalid @enderror" 
                                              id="sender_address" name="sender_address" rows="3" required>{{ old('sender_address') }}</textarea>
                                    @error('sender_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Penerima -->
                        <div class="mb-5">
                            <h4 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                                <i class="fas fa-user-check text-warning me-2"></i>
                                Informasi Penerima
                            </h4>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="recipient_name" class="form-label fw-medium">Nama Penerima <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" 
                                           id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required>
                                    @error('recipient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="recipient_phone" class="form-label fw-medium">Telepon Penerima <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('recipient_phone') is-invalid @enderror" 
                                           id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone') }}" required>
                                    @error('recipient_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="recipient_address" class="form-label fw-medium">Alamat Penerima <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('recipient_address') is-invalid @enderror" 
                                              id="recipient_address" name="recipient_address" rows="3" required>{{ old('recipient_address') }}</textarea>
                                    @error('recipient_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                            <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-save me-2"></i>
                                Simpan Pengiriman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate tracking number based on ID
document.getElementById('id').addEventListener('input', function() {
    const id = this.value;
    if (id) {
        const trackingNumber = 'EXP' + String(id).padStart(8, '0');
        document.getElementById('tracking_number').value = trackingNumber;
    }
});

// Trigger on page load if ID already has value
document.addEventListener('DOMContentLoaded', function() {
    const idField = document.getElementById('id');
    if (idField.value) {
        idField.dispatchEvent(new Event('input'));
    }
});
</script>
@endpush
@endsection
