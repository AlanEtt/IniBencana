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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Data Tempat Pengungsian</h4>
                    <a href="{{ route('shelters.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('shelters.update', $shelter->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Nama Tempat Pengungsian</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $shelter->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="map" class="form-label">Lokasi Pengungsian (Klik pada peta untuk mengubah)</label>
                            <div id="map" class="mb-2 border rounded"></div>
                            <input type="hidden" name="location" id="location"
                                class="@error('location') is-invalid @enderror"
                                value="{{ old('location', $shelter->location) }}" required>
                            <small class="text-muted">Koordinat yang dipilih: <span id="selectedCoords">{{ $shelter->location }}</span></small>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="capacity" class="form-label">Kapasitas</label>
                            <input type="number" name="capacity" id="capacity"
                                class="form-control @error('capacity') is-invalid @enderror"
                                value="{{ old('capacity', $shelter->capacity) }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="facilities" class="form-label">Fasilitas</label>
                            <textarea name="facilities" id="facilities" rows="3"
                                class="form-control @error('facilities') is-invalid @enderror"
                                required>{{ old('facilities', $shelter->facilities) }}</textarea>
                            @error('facilities')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
            // Ambil koordinat awal dari data shelter
            const coordinates = '{{ $shelter->location }}'.split(',');
            const initialLat = parseFloat(coordinates[0]);
            const initialLng = parseFloat(coordinates[1]);

            // Inisialisasi map
            const map = L.map('map', {
                center: [initialLat, initialLng],
                zoom: 15,
                zoomControl: true,
                scrollWheelZoom: true
            });

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Custom icon untuk marker
            const shelterIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Tambahkan marker awal
            let marker = L.marker([initialLat, initialLng], {
                icon: shelterIcon,
                draggable: true
            }).addTo(map);

            // Update koordinat saat marker di-drag
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('location').value = `${position.lat},${position.lng}`;
                document.getElementById('selectedCoords').textContent = `${position.lat}, ${position.lng}`;
            });

            // Event handler untuk klik pada map
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Hapus marker lama
                if (marker) {
                    map.removeLayer(marker);
                }

                // Tambah marker baru
                marker = L.marker([lat, lng], {
                    icon: shelterIcon,
                    draggable: true
                }).addTo(map);

                // Update nilai input hidden dan tampilkan koordinat
                document.getElementById('location').value = `${lat},${lng}`;
                document.getElementById('selectedCoords').textContent = `${lat}, ${lng}`;

                // Update koordinat saat marker di-drag
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    document.getElementById('location').value = `${position.lat},${position.lng}`;
                    document.getElementById('selectedCoords').textContent = `${position.lat}, ${position.lng}`;
                });
            });

            // Invalidate size setelah map dimuat
            setTimeout(() => {
                map.invalidateSize();
            }, 250);
        });
    </script>
@endsection
