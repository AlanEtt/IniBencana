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
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .detail-card {
            border-left: 4px solid #3498db;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Detail Distribusi Bantuan</h4>
                <div>
                    <a href="{{ route('aid-distributions.edit', $aidDistribution) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('aid-distributions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Peta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Peta Distribusi</h5>
                </div>
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>

            <!-- Informasi Bantuan -->
            <div class="card mb-4 detail-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Bantuan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <span class="info-label">Jenis Bantuan</span>
                        </div>
                        <div class="col-md-8">
                            {{ $aidDistribution->aidType->name }} - {{ $aidDistribution->aidType->category }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <span class="info-label">Jumlah</span>
                        </div>
                        <div class="col-md-8">
                            {{ $aidDistribution->quantity }} {{ $aidDistribution->aidType->unit }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <span class="info-label">Tanggal Distribusi</span>
                        </div>
                        <div class="col-md-8">
                            {{ \Carbon\Carbon::parse($aidDistribution->date)->format('d F Y H:i') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <span class="info-label">Deskripsi</span>
                        </div>
                        <div class="col-md-8">
                            {{ $aidDistribution->description ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Lokasi -->
            <div class="row">
                <!-- Lokasi Bencana -->
                <div class="col-md-6">
                    <div class="card mb-4 detail-card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Lokasi Bencana</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="info-label">Nama Bencana</span><br>
                                {{ $aidDistribution->disasterLocation->type }}
                            </div>
                            <div class="mb-2">
                                <span class="info-label">Lokasi</span><br>
                                {{ $aidDistribution->disasterLocation->location }}
                            </div>
                            <div class="mb-2">
                                <span class="info-label">Tingkat Keparahan</span><br>
                                @php
                                    $severity = $aidDistribution->disasterLocation->severity;
                                    if ($severity <= 3) {
                                        $color = '#27ae60'; // Hijau
                                    } elseif ($severity <= 6) {
                                        $color = '#f1c40f'; // Kuning
                                    } elseif ($severity <= 8) {
                                        $color = '#e67e22'; // Oranye
                                    } else {
                                        $color = '#e74c3c'; // Merah
                                    }
                                @endphp
                                <div class="mt-1">
                                    <span style="color: {{ $color }}; font-weight: bold;">
                                        {{ $severity }}/10
                                    </span>
                                    <div class="progress" style="height: 10px; width: 200px;">
                                        <div class="progress-bar"
                                             role="progressbar"
                                             style="width: {{ ($severity/10) * 100 }}%; background-color: {{ $color }};"
                                             aria-valuenow="{{ $severity }}"
                                             aria-valuemin="0"
                                             aria-valuemax="10">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span class="info-label">Deskripsi</span><br>
                                {{ $aidDistribution->disasterLocation->description }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shelter Tujuan -->
                <div class="col-md-6">
                    <div class="card mb-4 detail-card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Shelter Tujuan</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="info-label">Nama Shelter</span><br>
                                {{ $aidDistribution->shelterLocation->name }}
                            </div>
                            <div class="mb-2">
                                <span class="info-label">Kapasitas</span><br>
                                {{ $aidDistribution->shelterLocation->capacity }} orang
                            </div>
                            <div class="mb-2">
                                <span class="info-label">Fasilitas</span><br>
                                {{ $aidDistribution->shelterLocation->facilities }}
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

                // Koordinat lokasi
                const disasterLat = {{ $aidDistribution->disasterLocation->latitude }};
                const disasterLng = {{ $aidDistribution->disasterLocation->longitude }};
                const shelterLat = {{ $aidDistribution->shelterLocation->latitude }};
                const shelterLng = {{ $aidDistribution->shelterLocation->longitude }};

                // Tambah marker bencana
                disasterMarker = L.marker([disasterLat, disasterLng], { icon: disasterIcon }).addTo(map);
                disasterMarker.bindPopup(`
                    <div class="custom-popup">
                        <h6>{{ $aidDistribution->disasterLocation->type }}</h6>
                        <p>{{ $aidDistribution->disasterLocation->location }}</p>
                    </div>
                `);

                // Tambah marker shelter
                shelterMarker = L.marker([shelterLat, shelterLng], { icon: shelterIcon }).addTo(map);
                shelterMarker.bindPopup(`
                    <div class="custom-popup">
                        <h6>{{ $aidDistribution->shelterLocation->name }}</h6>
                        <p>Kapasitas: {{ $aidDistribution->shelterLocation->capacity }} orang</p>
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
