@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.25rem;
        }
        .custom-popup .leaflet-popup-content {
            margin: 12px;
            max-width: 300px;
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
        .distribution-line {
            stroke: #3498db;
            stroke-width: 3;
            stroke-dasharray: 10, 10;
            animation: dash 20s linear infinite;
        }
        @keyframes dash {
            to {
                stroke-dashoffset: -1000;
            }
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Peta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Peta Distribusi Bantuan</h5>
                </div>
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Distribusi Bantuan</h5>
                    <a href="{{ route('aid-distributions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('aid-distributions.update', $aidDistribution) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="disaster_location_id" class="form-label">Lokasi Bencana</label>
                            <select name="disaster_location_id" id="disaster_location_id" class="form-select @error('disaster_location_id') is-invalid @enderror" required>
                                <option value="">Pilih Lokasi Bencana</option>
                                @foreach($disasters as $disaster)
                                    <option value="{{ $disaster->id }}"
                                            {{ old('disaster_location_id', $aidDistribution->disaster_location_id) == $disaster->id ? 'selected' : '' }}
                                            data-lat="{{ $disaster->latitude }}"
                                            data-lng="{{ $disaster->longitude }}">
                                        {{ $disaster->type }} - {{ $disaster->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('disaster_location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shelter_location_id" class="form-label">Shelter Tujuan</label>
                            <select name="shelter_location_id" id="shelter_location_id" class="form-select @error('shelter_location_id') is-invalid @enderror" required>
                                <option value="">Pilih Shelter Tujuan</option>
                                @foreach($shelters as $shelter)
                                    <option value="{{ $shelter->id }}"
                                            {{ old('shelter_location_id', $aidDistribution->shelter_location_id) == $shelter->id ? 'selected' : '' }}
                                            data-lat="{{ $shelter->latitude }}"
                                            data-lng="{{ $shelter->longitude }}">
                                        {{ $shelter->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shelter_location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="aid_type" class="form-label">Jenis Bantuan</label>
                            <select name="aid_type" id="aid_type" class="form-select @error('aid_type') is-invalid @enderror" required>
                                <option value="">Pilih Jenis Bantuan</option>
                                <option value="Makanan" {{ old('aid_type', $aidDistribution->aid_type) == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                <option value="Pakaian" {{ old('aid_type', $aidDistribution->aid_type) == 'Pakaian' ? 'selected' : '' }}>Pakaian</option>
                                <option value="Obat-obatan" {{ old('aid_type', $aidDistribution->aid_type) == 'Obat-obatan' ? 'selected' : '' }}>Obat-obatan</option>
                                <option value="Peralatan" {{ old('aid_type', $aidDistribution->aid_type) == 'Peralatan' ? 'selected' : '' }}>Peralatan</option>
                                <option value="Lainnya" {{ old('aid_type', $aidDistribution->aid_type) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('aid_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                   id="quantity" name="quantity"
                                   value="{{ old('quantity', $aidDistribution->quantity) }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal</label>
                            <input type="datetime-local" class="form-control @error('date') is-invalid @enderror"
                                   id="date" name="date"
                                   value="{{ old('date', $aidDistribution->date ? \Carbon\Carbon::parse($aidDistribution->date)->format('Y-m-d\TH:i') : '') }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
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
            try {
                // Inisialisasi map
                const map = L.map('map');
                let disasterMarker, shelterMarker, distributionLine;

                // Tambahkan tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Icon untuk shelter
                const shelterIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="marker-icon-container">
                            <i class="bi bi-house-door-fill" style="color: #27ae60;"></i>
                          </div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -18]
                });

                // Icon untuk bencana
                const disasterIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="marker-icon-container">
                            <i class="bi bi-exclamation-triangle-fill" style="color: #e74c3c;"></i>
                          </div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -18]
                });

                // Fungsi untuk memperbarui peta
                function updateMap() {
                    // Hapus marker dan garis yang ada
                    if (disasterMarker) map.removeLayer(disasterMarker);
                    if (shelterMarker) map.removeLayer(shelterMarker);
                    if (distributionLine) map.removeLayer(distributionLine);

                    // Ambil lokasi yang dipilih
                    const disasterSelect = document.getElementById('disaster_location_id');
                    const shelterSelect = document.getElementById('shelter_location_id');

                    if (disasterSelect.value && shelterSelect.value) {
                        const disasterOption = disasterSelect.options[disasterSelect.selectedIndex];
                        const shelterOption = shelterSelect.options[shelterSelect.selectedIndex];

                        const disasterLat = parseFloat(disasterOption.dataset.lat);
                        const disasterLng = parseFloat(disasterOption.dataset.lng);
                        const shelterLat = parseFloat(shelterOption.dataset.lat);
                        const shelterLng = parseFloat(shelterOption.dataset.lng);

                        // Tambah marker bencana
                        disasterMarker = L.marker([disasterLat, disasterLng], { icon: disasterIcon }).addTo(map);
                        disasterMarker.bindPopup(`
                            <div class="custom-popup">
                                <h6>${disasterOption.text}</h6>
                                <p>Lokasi Bencana</p>
                            </div>
                        `);

                        // Tambah marker shelter
                        shelterMarker = L.marker([shelterLat, shelterLng], { icon: shelterIcon }).addTo(map);
                        shelterMarker.bindPopup(`
                            <div class="custom-popup">
                                <h6>${shelterOption.text}</h6>
                                <p>Shelter Tujuan</p>
                            </div>
                        `);

                        // Tambah garis distribusi
                        distributionLine = L.polyline([
                            [disasterLat, disasterLng],
                            [shelterLat, shelterLng]
                        ], {
                            color: '#3498db',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 10',
                            className: 'distribution-line'
                        }).addTo(map);

                        // Sesuaikan bounds peta
                        const bounds = L.latLngBounds(
                            [disasterLat, disasterLng],
                            [shelterLat, shelterLng]
                        );
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }
                }

                // Event listener untuk perubahan lokasi
                document.getElementById('disaster_location_id').addEventListener('change', updateMap);
                document.getElementById('shelter_location_id').addEventListener('change', updateMap);

                // Inisialisasi peta dengan data yang ada
                updateMap();

                // Tambahkan legenda
                const legend = L.control({ position: 'bottomright' });
                legend.onAdd = function(map) {
                    const div = L.DomUtil.create('div', 'info legend');
                    div.style.backgroundColor = 'white';
                    div.style.padding = '10px';
                    div.style.borderRadius = '5px';
                    div.style.boxShadow = '0 1px 5px rgba(0,0,0,0.2)';

                    div.innerHTML = `
                        <div style="font-weight: bold; margin-bottom: 5px;">Keterangan</div>
                        <div style="display: flex; align-items: center; margin: 5px 0;">
                            <div class="marker-icon-container" style="width: 24px; height: 24px; margin-right: 8px;">
                                <i class="bi bi-house-door-fill" style="color: #27ae60; font-size: 14px;"></i>
                            </div>
                            <span>Shelter Tujuan</span>
                        </div>
                        <div style="display: flex; align-items: center; margin: 5px 0;">
                            <div class="marker-icon-container" style="width: 24px; height: 24px; margin-right: 8px;">
                                <i class="bi bi-exclamation-triangle-fill" style="color: #e74c3c; font-size: 14px;"></i>
                            </div>
                            <span>Lokasi Bencana</span>
                        </div>
                        <div style="display: flex; align-items: center; margin: 5px 0;">
                            <div style="width: 24px; height: 3px; background-color: #3498db; margin-right: 8px;"></div>
                            <span>Jalur Distribusi</span>
                        </div>
                    `;

                    return div;
                };
                legend.addTo(map);

            } catch (err) {
                console.error('Error initializing map:', err);
            }
        });
    </script>
@endsection
