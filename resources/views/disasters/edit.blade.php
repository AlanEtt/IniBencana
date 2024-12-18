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
            font-size: 18px;
        }
        .banjir-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }
        .banjir-icon::before {
            content: "";
            width: 20px;
            height: 4px;
            background-color: #3498db;
            box-shadow: 0 -8px 0 #3498db, 0 8px 0 #3498db;
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
                                value="{{ old('location', $disaster->location) }}" required>
                            <small class="text-muted">Koordinat yang dipilih: <span id="selectedCoords">{{ $disaster->location }}</span></small>
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
                                value="{{ old('date', date('Y-m-d\TH:i', strtotime($disaster->date))) }}" required>
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
                    html: '<div class="banjir-icon"></div>',
                    className: 'custom-div-icon'
                },
                'Longsor': { icon: 'fa-caret-up', color: '#e67e22', scale: 1.2, rotate: 180 },
                'Kebakaran': { icon: 'fa-circle', color: '#e74c3c' },
                'Angin Puting Beliung': { icon: 'fa-wind', color: '#2ecc71' },
                'Gempa Bumi': { icon: 'fa-asterisk', color: '#9b59b6' },
                'Tsunami': { icon: 'fa-equals', color: '#34495e', scale: 1.2, rotate: 0 },
                'Kekeringan': { icon: 'fa-sun', color: '#f1c40f' },
                'Gunung Meletus': { icon: 'fa-caret-up', color: '#c0392b', scale: 1.2, rotate: 0 }
            };

            const config = iconConfigs[type] || { icon: 'fa-exclamation-triangle', color: '#95a5a6' };

            // Khusus untuk banjir
            if (type === 'Banjir') {
                return L.divIcon({
                    className: config.className,
                    html: config.html,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -18]
                });
            }

            // Untuk jenis bencana lainnya
            const scale = config.scale || 1;
            const rotate = config.rotate || 0;
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="marker-icon-container">
                        <i class="fas ${config.icon}" style="color: ${config.color}; font-size: ${18 * scale}px; transform: rotate(${rotate}deg);"></i>
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
            // Ambil koordinat awal dari data disaster
            const coordinates = '{{ $disaster->location }}'.split(',');
            const initialLat = parseFloat(coordinates[0]);
            const initialLng = parseFloat(coordinates[1]);

            // Inisialisasi map
            map = L.map('map', {
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

            // Tambahkan marker awal dengan icon sesuai jenis bencana
            const initialType = document.getElementById('type').value;
            marker = L.marker([initialLat, initialLng], {
                icon: getDisasterIcon(initialType),
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
                const currentType = document.getElementById('type').value;

                // Hapus marker lama
                if (marker) {
                    map.removeLayer(marker);
                }

                // Tambah marker baru dengan icon sesuai jenis bencana
                marker = L.marker([lat, lng], {
                    icon: getDisasterIcon(currentType),
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

            // Tambahkan event listener untuk perubahan jenis bencana
            document.getElementById('type').addEventListener('change', function(e) {
                const newType = e.target.value;
                updateMarkerIcon(newType);
            });

            // Invalidate size setelah map dimuat
            setTimeout(() => {
                map.invalidateSize();
            }, 250);
        });
    </script>
@endsection
