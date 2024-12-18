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
        .custom-popup .leaflet-popup-content {
            margin: 12px;
            max-width: 300px;
        }
        .custom-popup .leaflet-popup-content h6 {
            margin-bottom: 8px;
            font-weight: bold;
        }
        .custom-popup .leaflet-popup-content p {
            margin: 5px 0;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detail Tempat Pengungsian</h4>
                    <a href="{{ route('shelters.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div id="map" class="rounded mb-3"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <tr>
                                    <th style="width: 200px;">Nama Tempat</th>
                                    <td>{{ $shelter->name }}</td>
                                </tr>
                                <tr>
                                    <th>Koordinat</th>
                                    <td>{{ $shelter->location }}</td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td>{{ $shelter->capacity }} orang</td>
                                </tr>
                                <tr>
                                    <th>Fasilitas</th>
                                    <td>{{ $shelter->facilities }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <td>{{ $shelter->created_at->format('d F Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diupdate</th>
                                    <td>{{ $shelter->updated_at->format('d F Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('shelters.edit', $shelter->id) }}"
                                   class="btn btn-warning">Edit Data</a>
                                <form action="{{ route('shelters.destroy', $shelter->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        Hapus Data
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
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Ambil koordinat dari data shelter
                const coordinates = '{{ $shelter->location }}'.split(',');
                const lat = parseFloat(coordinates[0]);
                const lng = parseFloat(coordinates[1]);

                // Inisialisasi map dengan opsi yang lebih lengkap
                const map = L.map('map', {
                    center: [lat, lng],
                    zoom: 15,
                    minZoom: 5,
                    maxZoom: 19,
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

                // Tambahkan marker
                const marker = L.marker([lat, lng], {
                    icon: shelterIcon,
                    title: '{{ $shelter->name }}'
                }).addTo(map);

                // Tambahkan popup
                const popupContent = `
                    <div class="custom-popup">
                        <h6>{{ $shelter->name }}</h6>
                        <p>
                            <strong>Kapasitas:</strong> {{ $shelter->capacity }} orang<br>
                            <strong>Fasilitas:</strong> {{ $shelter->facilities }}<br>
                            <strong>Koordinat:</strong> ${lat}, ${lng}
                        </p>
                    </div>
                `;
                marker.bindPopup(popupContent).openPopup();

                // Invalidate size setelah map dimuat
                setTimeout(() => {
                    map.invalidateSize();
                }, 250);
            } catch (error) {
                console.error('Error initializing map:', error);
            }
        });
    </script>
@endsection
