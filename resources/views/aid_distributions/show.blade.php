@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Distribusi Bantuan</h1>
    <p><strong>Bencana:</strong> {{ $aidDistribution->disasterLocation->type }}</p>
    <p><strong>Lokasi Penampungan:</strong> {{ $aidDistribution->shelterLocation->name }}</p>
    <p><strong>Jenis Bantuan:</strong> {{ $aidDistribution->aidType->name }}</p>
    <p><strong>Jumlah:</strong> {{ $aidDistribution->quantity }}</p>
    <p><strong>Tanggal:</strong> {{ $aidDistribution->date }}</p>
    <a href="{{ route('aid-distributions.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
