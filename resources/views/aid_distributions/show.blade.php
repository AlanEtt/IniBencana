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

            <!-- Detail -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Distribusi Bantuan</h5>
                    <div>
                        <a href="{{ route('aid-distributions.edit', $aidDistribution) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('aid-distributions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Lokasi Bencana</th>
                            <td>
                                {{ $aidDistribution->disasterLocation->type }} - {{ $aidDistribution->shelterLocation->name }}
                                <br>
                                <small class="text-muted">{{ $aidDistribution->disasterLocation->location }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Shelter Tujuan</th>
                            <td>{{ $aidDistribution->shelterLocation->name }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Bantuan</th>
                            <td>{{ $aidDistribution->aid_type }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>{{ $aidDistribution->quantity }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ \Carbon\Carbon::parse($aidDistribution->date)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $aidDistribution->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $aidDistribution->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
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

                // Tambahkan marker untuk lokasi bencana
                const disasterMarker = L.marker(
                    [{{ $aidDistribution->disasterLocation->latitude }}, {{ $aidDistribution->disasterLocation->longitude }}],
                    { icon: disasterIcon }
                ).addTo(map);

                disasterMarker.bindPopup(`
                    <div class="custom-popup">
                        <h6>{{ $aidDistribution->disasterLocation->type }}</h6>
                        <p>{{ $aidDistribution->disasterLocation->location }}</p>
                    </div>
                `);

                // Tambahkan marker untuk shelter
                const shelterMarker = L.marker(
                    [{{ $aidDistribution->shelterLocation->latitude }}, {{ $aidDistribution->shelterLocation->longitude }}],
                    { icon: shelterIcon }
                ).addTo(map);

                shelterMarker.bindPopup(`
                    <div class="custom-popup">
                        <h6>{{ $aidDistribution->shelterLocation->name }}</h6>
                        <p>Shelter Tujuan</p>
                    </div>
                `);

                // Tambahkan garis distribusi
                const distributionLine = L.polyline([
                    [{{ $aidDistribution->disasterLocation->latitude }}, {{ $aidDistribution->disasterLocation->longitude }}],
                    [{{ $aidDistribution->shelterLocation->latitude }}, {{ $aidDistribution->shelterLocation->longitude }}]
                ], {
                    color: '#3498db',
                    weight: 3,
                    opacity: 0.8,
                    dashArray: '10, 10',
                    className: 'distribution-line'
                }).addTo(map);

                // Sesuaikan bounds peta
                const bounds = L.latLngBounds(
                    [{{ $aidDistribution->disasterLocation->latitude }}, {{ $aidDistribution->disasterLocation->longitude }}],
                    [{{ $aidDistribution->shelterLocation->latitude }}, {{ $aidDistribution->shelterLocation->longitude }}]
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
