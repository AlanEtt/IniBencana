@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Distribusi Bantuan</h1>
    <a href="{{ route('aid-distributions.create') }}" class="btn btn-primary">Tambah Distribusi Bantuan</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Bencana</th>
                <th>Lokasi Penampungan</th>
                <th>Jenis Bantuan</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distributions as $distribution)
                <tr>
                    <td>{{ $distribution->disasterLocation->type }}</td>
                    <td>{{ $distribution->shelterLocation->name }}</td>
                    <td>{{ $distribution->aidType->name }}</td>
                    <td>{{ $distribution->quantity }}</td>
                    <td>{{ $distribution->date }}</td>
                    <td>
                        <a href="{{ route('aid-distributions.edit', $distribution) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('aid-distributions.destroy', $distribution) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
