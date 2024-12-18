@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Jenis Bantuan</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('aid-types.update', $aidType) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Bantuan</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $aidType->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $aidType->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('category', $aidType->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="unit" class="form-label">Satuan</label>
                            <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                                <option value="">Pilih Satuan</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit }}" {{ old('unit', $aidType->unit) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                @endforeach
                            </select>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority_level" class="form-label">Tingkat Prioritas</label>
                            <select class="form-select @error('priority_level') is-invalid @enderror" id="priority_level" name="priority_level" required>
                                <option value="tinggi" {{ old('priority_level', $aidType->priority_level) == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                <option value="sedang" {{ old('priority_level', $aidType->priority_level) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="rendah" {{ old('priority_level', $aidType->priority_level) == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            </select>
                            @error('priority_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_perishable') is-invalid @enderror" type="checkbox" id="is_perishable" name="is_perishable" value="1" {{ old('is_perishable', $aidType->is_perishable) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_perishable">
                                    Mudah Kadaluarsa/Rusak
                                </label>
                                @error('is_perishable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="storage_method" class="form-label">Cara Penyimpanan</label>
                            <input type="text" class="form-control @error('storage_method') is-invalid @enderror" id="storage_method" name="storage_method" value="{{ old('storage_method', $aidType->storage_method) }}">
                            @error('storage_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="distribution_method" class="form-label">Metode Distribusi</label>
                            <input type="text" class="form-control @error('distribution_method') is-invalid @enderror" id="distribution_method" name="distribution_method" value="{{ old('distribution_method', $aidType->distribution_method) }}">
                            @error('distribution_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="donor_name" class="form-label">Nama Penyumbang</label>
                            <input type="text" class="form-control @error('donor_name') is-invalid @enderror" id="donor_name" name="donor_name" value="{{ old('donor_name', $aidType->donor_name) }}" required>
                            @error('donor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="donor_contact" class="form-label">Kontak Penyumbang</label>
                            <input type="text" class="form-control @error('donor_contact') is-invalid @enderror" id="donor_contact" name="donor_contact" value="{{ old('donor_contact', $aidType->donor_contact) }}">
                            @error('donor_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="donor_type" class="form-label">Tipe Penyumbang</label>
                            <select class="form-select @error('donor_type') is-invalid @enderror" id="donor_type" name="donor_type" required>
                                <option value="individu" {{ old('donor_type', $aidType->donor_type) == 'individu' ? 'selected' : '' }}>Individu</option>
                                <option value="organisasi" {{ old('donor_type', $aidType->donor_type) == 'organisasi' ? 'selected' : '' }}>Organisasi</option>
                            </select>
                            @error('donor_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="donation_date" class="form-label">Tanggal Donasi</label>
                            <input type="datetime-local" class="form-control @error('donation_date') is-invalid @enderror" id="donation_date" name="donation_date" value="{{ old('donation_date', $aidType->donation_date ? \Carbon\Carbon::parse($aidType->donation_date)->format('Y-m-d\TH:i') : '') }}" required>
                            @error('donation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('aid-types.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
