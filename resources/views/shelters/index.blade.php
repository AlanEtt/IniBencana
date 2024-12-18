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
        .shelter-popup {
            padding: 5px;
        }
        .shelter-popup h5 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .shelter-popup p {
            margin: 3px 0;
            font-size: 0.9em;
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
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Peta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Peta Lokasi Shelter</h4>
                </div>
                <div class="card-body">
                    <div id="map" class="rounded"></div>
                </div>
            </div>

            <!-- Tabel -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Data Shelter</h4>
                    <a href="{{ route('shelters.create') }}" class="btn btn-primary">Tambah Data Shelter</a>
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
                                    <th>Nama Shelter</th>
                                    <th>Alamat</th>
                                    <th>Kapasitas</th>
                                    <th>Fasilitas</th>
                                    <th>Koordinat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shelters as $shelter)
                                    <tr data-lat="{{ $shelter->latitude }}" data-lng="{{ $shelter->longitude }}" data-id="{{ $shelter->id }}" class="shelter-row" style="cursor: pointer;">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $shelter->name }}</td>
                                        <td>{{ $shelter->address }}</td>
                                        <td>{{ $shelter->capacity }} orang</td>
                                        <td>{{ Str::limit($shelter->facilities, 50) }}</td>
                                        <td>
                                            {{ number_format($shelter->latitude, 6) }},
                                            {{ number_format($shelter->longitude, 6) }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('shelters.show', $shelter) }}"
                                                   class="btn btn-info btn-sm"
                                                   title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                <a href="{{ route('shelters.edit', $shelter) }}"
                                                   class="btn btn-warning btn-sm"
                                                   title="Edit Data">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('shelters.destroy', $shelter) }}"
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
                                        <td colspan="7" class="text-center">Tidak ada data shelter</td>
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

                // Buat custom icon untuk shelter
                const shelterIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="marker-icon-container">
                            <i class="bi bi-house-door-fill" style="color: #27ae60; font-size: 20px;"></i>
                          </div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -18]
                });

                // Variabel untuk menyimpan semua marker
                const markers = {};

                // Tambahkan marker untuk setiap shelter
                const bounds = [];
                @foreach($shelters as $shelter)
                    try {
                        const lat = {{ $shelter->latitude }};
                        const lng = {{ $shelter->longitude }};
                        bounds.push([lat, lng]);

                        // Buat marker
                        const marker = L.marker(
                            [lat, lng],
                            { icon: shelterIcon }
                        ).addTo(map);

                        // Simpan marker ke dalam objek markers
                        markers['{{ $shelter->id }}'] = marker;

                        // Tambahkan popup dengan informasi
                        const popupContent = `
                            <div class="shelter-popup">
                                <h5>{{ $shelter->name }}</h5>
                                <p><strong>Alamat:</strong> {{ $shelter->address }}</p>
                                <p><strong>Kapasitas:</strong> {{ $shelter->capacity }} orang</p>
                                <p><strong>Fasilitas:</strong> {{ Str::limit($shelter->facilities, 100) }}</p>
                                <a href="{{ route('shelters.show', $shelter) }}" class="btn btn-info btn-sm mt-2">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                            </div>
                        `;
                        marker.bindPopup(popupContent);
                    } catch (err) {
                        console.error('Error adding marker:', err);
                    }
                @endforeach

                // Tambahkan event listener untuk baris tabel
                document.querySelectorAll('.shelter-row').forEach(row => {
                    row.addEventListener('click', function(e) {
                        // Jika yang diklik adalah tombol atau bagian dari btn-group, jangan lakukan apa-apa
                        if (e.target.closest('.btn-group')) {
                            return;
                        }

                        const lat = parseFloat(this.dataset.lat);
                        const lng = parseFloat(this.dataset.lng);
                        const id = this.dataset.id;

                        // Pindahkan peta ke lokasi yang diklik dengan animasi
                        map.flyTo([lat, lng], 16, {
                            animate: true,
                            duration: 1.5 // durasi animasi dalam detik
                        });

                        // Tunggu animasi selesai baru buka popup
                        setTimeout(() => {
                            const marker = markers[id];
                            if (marker) {
                                marker.openPopup();
                            }
                        }, 1500); // sesuaikan dengan durasi animasi
                    });
                });

                // Jika ada marker, sesuaikan bounds peta
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
