@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Jenis Bantuan</h5>
                        <div>
                            <a href="{{ route('aid-types.edit', $aidType) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('aid-types.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">Nama Bantuan</th>
                            <td>{{ $aidType->name }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $aidType->description ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $aidType->category }}</td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td>{{ $aidType->unit }}</td>
                        </tr>
                        <tr>
                            <th>Tingkat Prioritas</th>
                            <td>
                                @if($aidType->priority_level == 'tinggi')
                                    <span class="badge bg-danger">Tinggi</span>
                                @elseif($aidType->priority_level == 'sedang')
                                    <span class="badge bg-warning">Sedang</span>
                                @else
                                    <span class="badge bg-info">Rendah</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status Kadaluarsa</th>
                            <td>
                                @if($aidType->is_perishable)
                                    <span class="badge bg-danger">Mudah Kadaluarsa/Rusak</span>
                                @else
                                    <span class="badge bg-success">Tahan Lama</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Cara Penyimpanan</th>
                            <td>{{ $aidType->storage_method ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Metode Distribusi</th>
                            <td>{{ $aidType->distribution_method ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Penyumbang</th>
                            <td>{{ $aidType->donor_name }}</td>
                        </tr>
                        <tr>
                            <th>Kontak Penyumbang</th>
                            <td>{{ $aidType->donor_contact ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Penyumbang</th>
                            <td>{{ ucfirst($aidType->donor_type) }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Donasi</th>
                            <td>{{ \Carbon\Carbon::parse($aidType->donation_date)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $aidType->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td>{{ $aidType->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($aidType->aidDistributions->count() > 0)
                        <div class="mt-4">
                            <h6>Riwayat Distribusi</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Lokasi</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aidType->aidDistributions as $distribution)
                                            <tr>
                                                <td>{{ $distribution->date->format('d/m/Y') }}</td>
                                                <td>{{ $distribution->location }}</td>
                                                <td>{{ $distribution->quantity }} {{ $aidType->unit }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
    }
    .table th {
        background-color: #f8f9fa;
    }
</style>
@endpush
