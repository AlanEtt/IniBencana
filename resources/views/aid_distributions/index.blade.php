@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        #map {
            height: 500px;
            width: 100%;
            z-index: 1;
        }
        .leaflet-container {
            z-index: 1;
        }
        .btn-group {
            gap: 5px;
        }
        .table td {
            vertical-align: middle;
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
        <div class="col-md-12">
            <!-- Peta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Peta Distribusi Bantuan</h4>
                </div>
                <div class="card-body">
                    <div id="map" class="rounded"></div>
                </div>
            </div>

            <!-- Tabel -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Data Distribusi Bantuan</h4>
                    <a href="{{ route('aid-distributions.create') }}" class="btn btn-primary">Tambah Distribusi</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Lokasi Bencana</th>
                                    <th>Shelter Tujuan</th>
                                    <th>Jenis Bantuan</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distributions as $distribution)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $distribution->disasterLocation->type }} - {{ $distribution->shelterLocation->name }}
                                            <br>
                                            <small class="text-muted">{{ $distribution->disasterLocation->location }}</small>
                                        </td>
                                        <td>{{ $distribution->shelterLocation->name }}</td>
                                        <td>{{ $distribution->aid_type }}</td>
                                        <td>{{ $distribution->quantity }}</td>
                                        <td>{{ \Carbon\Carbon::parse($distribution->date)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('aid-distributions.show', $distribution) }}"
                                                   class="btn btn-info btn-sm"
                                                   title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                <a href="{{ route('aid-distributions.edit', $distribution) }}"
                                                   class="btn btn-warning btn-sm"
                                                   title="Edit Data">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('aid-distributions.destroy', $distribution) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-danger btn-sm"
                                                            title="Hapus Data">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data distribusi bantuan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                const map = L.map('map').setView([-6.5935, 110.6776], 12);

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

                // Simpan semua bounds untuk fit map
                const bounds = [];

                // Tambahkan marker untuk setiap shelter
                @foreach($shelters as $shelter)
                    const shelterMarker{{ $shelter->id }} = L.marker(
                        [{{ $shelter->latitude }}, {{ $shelter->longitude }}],
                        { icon: shelterIcon }
                    ).addTo(map);

                    shelterMarker{{ $shelter->id }}.bindPopup(`
                        <div class="custom-popup">
                            <h6>{{ $shelter->name }}</h6>
                            <p>Kapasitas: {{ $shelter->capacity }} orang</p>
                        </div>
                    `);

                    bounds.push([{{ $shelter->latitude }}, {{ $shelter->longitude }}]);
                @endforeach

                // Tambahkan marker untuk setiap bencana
                @foreach($disasters as $disaster)
                    const disasterMarker{{ $disaster->id }} = L.marker(
                        [{{ $disaster->latitude }}, {{ $disaster->longitude }}],
                        { icon: disasterIcon }
                    ).addTo(map);

                    disasterMarker{{ $disaster->id }}.bindPopup(`
                        <div class="custom-popup">
                            <h6>{{ $disaster->type }}</h6>
                            <p>Tanggal: {{ $disaster->date->format('d/m/Y') }}</p>
                        </div>
                    `);

                    bounds.push([{{ $disaster->latitude }}, {{ $disaster->longitude }}]);
                @endforeach

                // Tambahkan garis distribusi
                @foreach($distributions as $distribution)
                    const line{{ $distribution->id }} = L.polyline([
                        [{{ $distribution->disasterLocation->latitude }}, {{ $distribution->disasterLocation->longitude }}],
                        [{{ $distribution->shelterLocation->latitude }}, {{ $distribution->shelterLocation->longitude }}]
                    ], {
                        color: '#3498db',
                        weight: 3,
                        opacity: 0.8,
                        dashArray: '10, 10',
                        className: 'distribution-line'
                    }).addTo(map);

                    line{{ $distribution->id }}.bindPopup(`
                        <div class="custom-popup">
                            <h6>Distribusi Bantuan</h6>
                            <p>Dari: {{ $distribution->disasterLocation->type }}<br>
                               Ke: {{ $distribution->shelterLocation->name }}<br>
                               Jenis: {{ $distribution->aid_type }}<br>
                               Jumlah: {{ $distribution->quantity }}<br>
                               Tanggal: {{ $distribution->date->format('d/m/Y') }}</p>
                        </div>
                    `);
                @endforeach

                // Sesuaikan bounds peta
                if (bounds.length > 0) {
                    map.fitBounds(bounds);
                }

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
                        <div style="display: flex; align-items: center; margin: 5px 0;">
                            <div style="width: 24px; height: 3px; background-color: #3498db; margin-right: 8px;"></div>
                            <span>Jalur Distribusi</span>
                        </div>
                    `;

                    return div;
                };
                legend.addTo(map);

                // Invalidate size setelah map dimuat
                setTimeout(function(){ map.invalidateSize()}, 100);

            } catch (err) {
                console.error('Error initializing map:', err);
            }
        });
    </script>
@endsection
