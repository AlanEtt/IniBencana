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
        .selected-marker {
            border: 3px solid #3498db;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Tambah Distribusi Bantuan</h4>
                    <a href="{{ route('aid-distributions.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('aid-distributions.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="map" class="form-label">Pilih Lokasi Bencana dan Shelter pada Peta</label>
                            <div id="map" class="mb-2 border rounded"></div>
                            <small class="text-muted d-block">Klik marker bencana dan shelter untuk memilih lokasi distribusi</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="disaster_location_id" class="form-label">Lokasi Bencana</label>
                            <select name="disaster_location_id" id="disaster_location_id"
                                class="form-select @error('disaster_location_id') is-invalid @enderror" required>
                                <option value="">Pilih Lokasi Bencana</option>
                                @foreach($disasters as $disaster)
                                    <option value="{{ $disaster->id }}" data-lat="{{ $disaster->latitude }}" data-lng="{{ $disaster->longitude }}">
                                        {{ $disaster->type }} - {{ $disaster->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('disaster_location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="shelter_location_id" class="form-label">Shelter Tujuan</label>
                            <select name="shelter_location_id" id="shelter_location_id"
                                class="form-select @error('shelter_location_id') is-invalid @enderror" required>
                                <option value="">Pilih Shelter Tujuan</option>
                                @foreach($shelters as $shelter)
                                    <option value="{{ $shelter->id }}" data-lat="{{ $shelter->latitude }}" data-lng="{{ $shelter->longitude }}">
                                        {{ $shelter->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shelter_location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="aid_type" class="form-label">Jenis Bantuan</label>
                            <select name="aid_type" id="aid_type"
                                class="form-select @error('aid_type') is-invalid @enderror" required>
                                <option value="">Pilih Jenis Bantuan</option>
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Pakaian">Pakaian</option>
                                <option value="Obat-obatan">Obat-obatan</option>
                                <option value="Selimut">Selimut</option>
                                <option value="Tenda">Tenda</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            @error('aid_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" name="quantity" id="quantity"
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity') }}" required min="1">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="date" class="form-label">Tanggal Distribusi</label>
                            <input type="date" name="date" id="date"
                                class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
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
            let map, disasterMarkers = {}, shelterMarkers = {}, distributionLine;
            let selectedDisaster = null, selectedShelter = null;

            try {
                // Inisialisasi map
                map = L.map('map').setView([-6.5935, 110.6776], 12);

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

                // Fungsi untuk menggambar garis distribusi
                function drawDistributionLine() {
                    if (selectedDisaster && selectedShelter) {
                        // Hapus garis lama jika ada
                        if (distributionLine) {
                            map.removeLayer(distributionLine);
                        }

                        // Buat garis baru
                        distributionLine = L.polyline([
                            [selectedDisaster.lat, selectedDisaster.lng],
                            [selectedShelter.lat, selectedShelter.lng]
                        ], {
                            color: '#3498db',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 10',
                            className: 'distribution-line'
                        }).addTo(map);

                        // Sesuaikan bounds peta
                        map.fitBounds([
                            [selectedDisaster.lat, selectedDisaster.lng],
                            [selectedShelter.lat, selectedShelter.lng]
                        ], { padding: [50, 50] });
                    }
                }

                // Fungsi untuk mengupdate marker yang dipilih
                function updateSelectedMarker(type, id) {
                    // Reset semua marker
                    Object.values(disasterMarkers).forEach(marker => {
                        marker.getElement().querySelector('.marker-icon-container').classList.remove('selected-marker');
                    });
                    Object.values(shelterMarkers).forEach(marker => {
                        marker.getElement().querySelector('.marker-icon-container').classList.remove('selected-marker');
                    });

                    // Update marker yang dipilih
                    if (type === 'disaster' && disasterMarkers[id]) {
                        disasterMarkers[id].getElement().querySelector('.marker-icon-container').classList.add('selected-marker');
                    } else if (type === 'shelter' && shelterMarkers[id]) {
                        shelterMarkers[id].getElement().querySelector('.marker-icon-container').classList.add('selected-marker');
                    }
                }

                // Tambahkan marker untuk setiap bencana
                @foreach($disasters as $disaster)
                    const disasterMarker{{ $disaster->id }} = L.marker(
                        [{{ $disaster->latitude }}, {{ $disaster->longitude }}],
                        { icon: disasterIcon }
                    ).addTo(map);

                    disasterMarkers[{{ $disaster->id }}] = disasterMarker{{ $disaster->id }};

                    disasterMarker{{ $disaster->id }}.bindPopup(`
                        <div class="custom-popup">
                            <h6>{{ $disaster->type }}</h6>
                            <p>Tanggal: {{ $disaster->date->format('d/m/Y') }}</p>
                            <button class="btn btn-sm btn-primary" onclick="selectDisaster({{ $disaster->id }})">
                                Pilih Lokasi Ini
                            </button>
                        </div>
                    `);
                @endforeach

                // Tambahkan marker untuk setiap shelter
                @foreach($shelters as $shelter)
                    const shelterMarker{{ $shelter->id }} = L.marker(
                        [{{ $shelter->latitude }}, {{ $shelter->longitude }}],
                        { icon: shelterIcon }
                    ).addTo(map);

                    shelterMarkers[{{ $shelter->id }}] = shelterMarker{{ $shelter->id }};

                    shelterMarker{{ $shelter->id }}.bindPopup(`
                        <div class="custom-popup">
                            <h6>{{ $shelter->name }}</h6>
                            <p>Kapasitas: {{ $shelter->capacity }} orang</p>
                            <button class="btn btn-sm btn-primary" onclick="selectShelter({{ $shelter->id }})">
                                Pilih Shelter Ini
                            </button>
                        </div>
                    `);
                @endforeach

                // Event listener untuk select disaster
                document.getElementById('disaster_location_id').addEventListener('change', function(e) {
                    const option = this.options[this.selectedIndex];
                    if (option.value) {
                        selectDisaster(option.value);
                    } else {
                        if (selectedDisaster) {
                            updateSelectedMarker('disaster', null);
                            selectedDisaster = null;
                            if (distributionLine) {
                                map.removeLayer(distributionLine);
                            }
                        }
                    }
                });

                // Event listener untuk select shelter
                document.getElementById('shelter_location_id').addEventListener('change', function(e) {
                    const option = this.options[this.selectedIndex];
                    if (option.value) {
                        selectShelter(option.value);
                    } else {
                        if (selectedShelter) {
                            updateSelectedMarker('shelter', null);
                            selectedShelter = null;
                            if (distributionLine) {
                                map.removeLayer(distributionLine);
                            }
                        }
                    }
                });

                // Fungsi untuk memilih disaster
                window.selectDisaster = function(id) {
                    const select = document.getElementById('disaster_location_id');
                    select.value = id;
                    const option = select.options[select.selectedIndex];
                    selectedDisaster = {
                        lat: parseFloat(option.dataset.lat),
                        lng: parseFloat(option.dataset.lng)
                    };
                    updateSelectedMarker('disaster', id);
                    drawDistributionLine();
                };

                // Fungsi untuk memilih shelter
                window.selectShelter = function(id) {
                    const select = document.getElementById('shelter_location_id');
                    select.value = id;
                    const option = select.options[select.selectedIndex];
                    selectedShelter = {
                        lat: parseFloat(option.dataset.lat),
                        lng: parseFloat(option.dataset.lng)
                    };
                    updateSelectedMarker('shelter', id);
                    drawDistributionLine();
                };

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
                            <span>Lokasi Shelter</span>
                        </div>
                        <div style="display: flex; align-items: center; margin: 5px 0;">
                            <div class="marker-icon-container" style="width: 24px; height: 24px; margin-right: 8px;">
                                <i class="bi bi-exclamation-triangle-fill" style="color: #e74c3c; font-size: 14px;"></i>
                            </div>
                            <span>Lokasi Bencana</span>
                        </div>
                    `;

                    return div;
                };
                legend.addTo(map);

                // Invalidate size setelah map dimuat
                setTimeout(() => {
                    map.invalidateSize();
                }, 250);

            } catch (err) {
                console.error('Error initializing map:', err);
            }
        });
    </script>
@endsection
