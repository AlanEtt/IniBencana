@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Distribusi Bantuan</h1>
    <form action="{{ route('aid-distributions.update', $aidDistribution) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="disaster_id">Bencana</label>
            <select name="disaster_id" class="form-control" required>
                <option value="">Pilih Bencana</option>
                @foreach($disasters as $disaster)
                    <option value="{{ $disaster->id }}" {{ $disaster->id == $aidDistribution->disaster_id ? 'selected' : '' }}>{{ $disaster->type }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="shelter_id">Lokasi Penampungan</label>
            <select name="shelter_id" class="form-control" required>
                <option value="">Pilih Lokasi Penampungan</option>
                @foreach($shelters as $shelter)
                    <option value="{{ $shelter->id }}" {{ $shelter->id == $aidDistribution->shelter_id ? 'selected' : '' }}>{{ $shelter->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="aid_type_id">Jenis Bantuan</label>
            <select name="aid_type_id" class="form-control" required>
                <option value="">Pilih Jenis Bantuan</option>
                @foreach($aidTypes as $aidType)
                    <option value="{{ $aidType->id }}" {{ $aidType->id == $aidDistribution->aid_type_id ? 'selected' : '' }}>{{ $aidType->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Jumlah</label>
            <input type="number" name="quantity" class="form-control" value="{{ $aidDistribution->quantity }}" required>
        </div>
        <div class="form-group">
            <label for="date">Tanggal</label>
            <input type="datetime-local" name="date" class="form-control" value="{{ \Carbon\Carbon::parse($aidDistribution->date)->format('Y-m-d\TH:i') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
