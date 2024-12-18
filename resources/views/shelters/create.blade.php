@extends('layouts.app')

@section('styles')
    <!-- Leaflet CSS -->
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
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Tambah Data Tempat Pengungsian</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('shelters.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nama Tempat Pengungsian</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="map" class="form-label">Lokasi Pengungsian (Klik pada peta)</label>
                            <div id="map" class="mb-2 border rounded"></div>
                            <input type="hidden" name="location" id="location"
                                class="@error('location') is-invalid @enderror" required>
                            <small class="text-muted">Koordinat yang dipilih: <span id="selectedCoords">Belum dipilih</span></small>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="capacity" class="form-label">Kapasitas</label>
                            <input type="number" name="capacity" id="capacity"
                                class="form-control @error('capacity') is-invalid @enderror"
                                value="{{ old('capacity') }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="facilities" class="form-label">Fasilitas</label>
                            <textarea name="facilities" id="facilities" rows="3"
                                class="form-control @error('facilities') is-invalid @enderror"
                                required>{{ old('facilities') }}</textarea>
                            @error('facilities')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Data Pengungsian</button>
                            <a href="{{ route('shelters.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi map
            const map = L.map('map', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView([-6.5935, 110.6776], 13);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Variabel untuk marker
            let marker = null;

            // Event handler untuk klik pada map
            map.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);

                // Hapus marker lama jika ada
                if (marker !== null) {
                    map.removeLayer(marker);
                }

                // Tambah marker baru
                marker = L.marker(e.latlng).addTo(map);

                // Update nilai input hidden dan tampilkan koordinat
                document.getElementById('location').value = `${lat},${lng}`;
                document.getElementById('selectedCoords').textContent = `${lat}, ${lng}`;
            });

            // Set initial marker jika ada nilai default
            const defaultLat = -6.5935;
            const defaultLng = 110.6776;
            marker = L.marker([defaultLat, defaultLng]).addTo(map);
            document.getElementById('location').value = `${defaultLat},${defaultLng}`;
            document.getElementById('selectedCoords').textContent = `${defaultLat}, ${defaultLng}`;

            // Fix map rendering issues
            setTimeout(() => {
                map.invalidateSize();
            }, 250);
        });
    </script>
@endsection
