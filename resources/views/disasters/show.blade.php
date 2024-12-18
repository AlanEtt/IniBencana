@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <style>
        #map {
            height: 400px;
            width: 100%;
            z-index: 1;
        }
        .leaflet-container {
            z-index: 1;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .severity-badge {
            font-size: 1.2em;
            padding: 8px 16px;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detail Bencana</h4>
                    <a href="{{ route('disasters.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="detail-label">Jenis Bencana</p>
                            <h5>{{ ucfirst($disaster->type) }}</h5>
                        </div>
                        <div class="col-md-6">
                            <p class="detail-label">Tanggal & Waktu</p>
                            <h5>{{ $disaster->date->format('d F Y, H:i') }}</h5>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <p class="detail-label">Lokasi</p>
                            <h5>{{ $disaster->location }}</h5>
                            <p class="text-muted">
                                Koordinat: {{ number_format($disaster->latitude, 6) }}, {{ number_format($disaster->longitude, 6) }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <p class="detail-label">Tingkat Keparahan</p>
                            <div class="severity-badge badge bg-{{ $disaster->severity > 7 ? 'danger' : ($disaster->severity > 4 ? 'warning' : 'success') }}">
                                {{ $disaster->severity }}/10
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <p class="detail-label">Deskripsi</p>
                            <p class="text-justify">{{ $disaster->description ?: 'Tidak ada deskripsi' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <p class="detail-label">Lokasi pada Peta</p>
                            <div id="map" class="rounded"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('disasters.edit', $disaster) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Edit Data
                                </a>
                                <form action="{{ route('disasters.destroy', $disaster) }}"
                                      method="POST"
                                      class="d-inline w-100"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-trash"></i> Hapus Data
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lat = {{ $disaster->latitude }};
            const lng = {{ $disaster->longitude }};

            // Inisialisasi map
            const map = L.map('map').setView([lat, lng], 15);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Tambahkan marker
            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(`
                <strong>{{ ucfirst($disaster->type) }}</strong><br>
                Tanggal: {{ $disaster->date->format('d/m/Y H:i') }}<br>
                Tingkat Keparahan: {{ $disaster->severity }}/10
            `).openPopup();

            // Invalidate size setelah map dimuat
            setTimeout(function(){ map.invalidateSize()}, 100);
        });
    </script>
@endsection
