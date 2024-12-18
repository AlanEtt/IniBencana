@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Jenis Bantuan</h5>
                <a href="{{ route('aid-types.create') }}" class="btn btn-primary">Tambah Jenis Bantuan</a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Prioritas</th>
                            <th>Penyumbang</th>
                            <th>Tanggal Donasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aidTypes as $aidType)
                            <tr>
                                <td>{{ $aidType->name }}</td>
                                <td>{{ $aidType->category }}</td>
                                <td>{{ $aidType->unit }}</td>
                                <td>
                                    @if($aidType->priority_level == 'tinggi')
                                        <span class="badge bg-danger">Tinggi</span>
                                    @elseif($aidType->priority_level == 'sedang')
                                        <span class="badge bg-warning">Sedang</span>
                                    @else
                                        <span class="badge bg-info">Rendah</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $aidType->donor_name }}
                                    <small class="text-muted d-block">{{ $aidType->donor_type }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($aidType->donation_date)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('aid-types.show', $aidType) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('aid-types.edit', $aidType) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('aid-types.destroy', $aidType) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data jenis bantuan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endpush
