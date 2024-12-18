@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Tipe Bantuan</h1>
    <p><strong>Nama:</strong> {{ $aidType->name }}</p>
    <p><strong>Deskripsi:</strong> {{ $aidType->description }}</p>
    <a href="{{ route('aid-types.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
