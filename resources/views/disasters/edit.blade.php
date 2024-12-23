@extends('layouts.app')

@section('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">

    <style>
        #map {
            height: 400px;
            width: 100%;
            z-index: 1;
        }
        .leaflet-container {
            z-index: 1;
        }
        .marker-icon-container {
            background-color: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .marker-icon-container i {
            font-size: 20px;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Data Bencana</h4>
                    <a href="{{ route('disasters.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('disasters.update', $disaster->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="type" class="form-label">Jenis Bencana</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required onchange="updateMarkerIcon(this.value)">
                                <option value="">Pilih Jenis Bencana</option>
                                <option value="Banjir" {{ $disaster->type == 'Banjir' ? 'selected' : '' }}>Banjir</option>
                                <option value="Longsor" {{ $disaster->type == 'Longsor' ? 'selected' : '' }}>Longsor</option>
                                <option value="Kebakaran" {{ $disaster->type == 'Kebakaran' ? 'selected' : '' }}>Kebakaran</option>
                                <option value="Angin Puting Beliung" {{ $disaster->type == 'Angin Puting Beliung' ? 'selected' : '' }}>Angin Puting Beliung</option>
                                <option value="Gempa Bumi" {{ $disaster->type == 'Gempa Bumi' ? 'selected' : '' }}>Gempa Bumi</option>
                                <option value="Tsunami" {{ $disaster->type == 'Tsunami' ? 'selected' : '' }}>Tsunami</option>
                                <option value="Kekeringan" {{ $disaster->type == 'Kekeringan' ? 'selected' : '' }}>Kekeringan</option>
                                <option value="Gunung Meletus" {{ $disaster->type == 'Gunung Meletus' ? 'selected' : '' }}>Gunung Meletus</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="map" class="form-label">Lokasi Bencana (Klik pada peta untuk mengubah)</label>
                            <div id="map" class="mb-2 border rounded"></div>
                            <input type="hidden" name="location" id="location"
                                class="@error('location') is-invalid @enderror"
                                value="{{ old('location', $disaster->formatted_location) }}" required>
                            <small class="text-muted">Koordinat yang dipilih: <span id="selectedCoords">{{ $disaster->formatted_location }}</span></small>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror"
                                required>{{ old('description', $disaster->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="date" class="form-label">Tanggal Kejadian</label>
                            <input type="datetime-local" name="date" id="date"
                                class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', \Carbon\Carbon::parse($disaster->date)->format('Y-m-d\TH:i')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="severity" class="form-label">Tingkat Keparahan (1-10)</label>
                            <input type="number" name="severity" id="severity"
                                class="form-control @error('severity') is-invalid @enderror"
                                value="{{ old('severity', $disaster->severity) }}" min="1" max="10" required>
                            @error('severity')
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
        let map, marker;

        // Fungsi untuk mendapatkan icon berdasarkan jenis bencana
        function getDisasterIcon(type) {
            const iconConfigs = {
                'Banjir': {
                    icon: 'bi-water',
                    color: '#3498db'
                },
                'Longsor': {
                    icon: 'bi-triangle-fill',
                    color: '#e67e22'
                },
                'Kebakaran': {
                    icon: 'bi-fire',
                    color: '#e74c3c'
                },
                'Angin Puting Beliung': {
                    icon: 'bi-wind',
                    color: '#2ecc71'
                },
                'Gempa Bumi': {
                    icon: 'bi-exclamation-triangle-fill',
                    color: '#9b59b6'
                },
                'Tsunami': {
                    icon: 'bi-water',
                    color: '#34495e'
                },
                'Kekeringan': {
                    icon: 'bi-sun',
                    color: '#f1c40f'
                },
                'Gunung Meletus': {
                    icon: 'bi-triangle-fill',
                    color: '#c0392b'
                }
            };

            const config = iconConfigs[type] || {
                icon: 'bi-exclamation-triangle-fill',
                color: '#95a5a6'
            };

            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="marker-icon-container">
                        <i class="bi ${config.icon}" style="color: ${config.color};"></i>
                      </div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 18],
                popupAnchor: [0, -18]
            });
        }

        // Fungsi untuk mengupdate icon marker
        function updateMarkerIcon(type) {
            if (marker) {
                marker.setIcon(getDisasterIcon(type));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Ambil koordinat awal dari data disaster
                const initialLocation = document.getElementById('location').value;
                const [initialLat, initialLng] = initialLocation.split(',').map(coord => parseFloat(coord.trim()));

                if (!initialLat || !initialLng || isNaN(initialLat) || isNaN(initialLng)) {
                    throw new Error('Koordinat tidak valid');
                }

                // Inisialisasi map
                map = L.map('map').setView([initialLat, initialLng], 15);

                // Tambahkan tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Tambahkan marker awal dengan icon sesuai jenis bencana
                const initialType = document.getElementById('type').value;
                marker = L.marker([initialLat, initialLng], {
                    icon: getDisasterIcon(initialType),
                    draggable: true
                }).addTo(map);

                // Update koordinat saat marker di-drag
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    const newLocation = `${position.lat},${position.lng}`;
                    document.getElementById('location').value = newLocation;
                    document.getElementById('selectedCoords').textContent = newLocation;
                });

                // Event handler untuk klik pada map
                map.on('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    const newLocation = `${lat},${lng}`;
                    const currentType = document.getElementById('type').value;

                    // Update hidden input dan text display
                    document.getElementById('location').value = newLocation;
                    document.getElementById('selectedCoords').textContent = newLocation;

                    // Hapus marker lama
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    // Tambah marker baru
                    marker = L.marker([lat, lng], {
                        icon: getDisasterIcon(currentType),
                        draggable: true
                    }).addTo(map);

                    // Update koordinat saat marker di-drag
                    marker.on('dragend', function(e) {
                        const position = marker.getLatLng();
                        const dragLocation = `${position.lat},${position.lng}`;
                        document.getElementById('location').value = dragLocation;
                        document.getElementById('selectedCoords').textContent = dragLocation;
                    });
                });

                // Event listener untuk perubahan jenis bencana
                document.getElementById('type').addEventListener('change', function() {
                    updateMarkerIcon(this.value);
                });

            } catch (error) {
                console.error('Error initializing map:', error);
                alert('Terjadi kesalahan saat memuat peta. Silakan refresh halaman.');
            }
        });
    </script>
@endsection
