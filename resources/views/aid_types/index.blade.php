@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Tipe Bantuan</h1>
    <a href="{{ route('aid-types.create') }}" class="btn btn-primary">Tambah Tipe Bantuan</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aidTypes as $aidType)
                <tr>
                    <td>{{ $aidType->name }}</td>
                    <td>{{ $aidType->description }}</td>
                    <td>
                        <a href="{{ route('aid-types.edit', $aidType) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('aid-types.destroy', $aidType) }}" method="POST" style="display:inline;">
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
