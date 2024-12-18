@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .disaster-popup {
            padding: 5px;
        }
        .disaster-popup h5 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .disaster-popup p {
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
        .marker-icon-container i {
            font-size: 18px;
        }
        /* Ikon Banjir - 3 garis horizontal */
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
        /* Ikon Longsor - segitiga terbalik */
        .longsor-icon {
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 18px solid #e67e22;
        }
        /* Ikon Kebakaran - lingkaran merah dengan border putih */
        .kebakaran-icon {
            position: relative;
            width: 24px;
            height: 24px;
        }
        .kebakaran-icon::before {
            content: "○";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #e74c3c;
            font-size: 24px;
            font-weight: bold;
        }

        /* Ikon Gunung Meletus - segitiga merah */
        .gunung-meletus-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            font-size: 24px;
            color: #c0392b;
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
                    <h4 class="mb-0">Peta Sebaran Bencana</h4>
                </div>
                <div class="card-body">
                    <div id="map" class="rounded"></div>
                </div>
            </div>

            <!-- Tabel -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Data Bencana</h4>
                    <a href="{{ route('disasters.create') }}" class="btn btn-primary">Tambah Data Bencana</a>
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
                                    <th>Jenis Bencana</th>
                                    <th>Lokasi</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal</th>
                                    <th>Tingkat Keparahan</th>
                                    <th>Koordinat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disasters as $disaster)
                                    <tr data-lat="{{ $disaster->latitude }}" data-lng="{{ $disaster->longitude }}" data-id="{{ $disaster->id }}" class="disaster-row" style="cursor: pointer;">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst($disaster->type) }}</td>
                                        <td>{{ $disaster->location }}</td>
                                        <td>{{ Str::limit($disaster->description, 50) }}</td>
                                        <td>{{ $disaster->date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $disaster->severity > 7 ? 'danger' : ($disaster->severity > 4 ? 'warning' : 'success') }}">
                                                {{ $disaster->severity }}/10
                                            </span>
                                        </td>
                                        <td>
                                            {{ number_format($disaster->latitude, 6) }},
                                            {{ number_format($disaster->longitude, 6) }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('disasters.show', $disaster) }}"
                                                   class="btn btn-info btn-sm"
                                                   title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                <a href="{{ route('disasters.edit', $disaster) }}"
                                                   class="btn btn-warning btn-sm"
                                                   title="Edit Data">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('disasters.destroy', $disaster) }}"
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
                                        <td colspan="8" class="text-center">Tidak ada data bencana</td>
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

                // Konfigurasi icon untuk berbagai jenis bencana
                const iconConfigs = {
                    'Banjir': {
                        html: '<div class="marker-icon-container" style="background-color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><span style="color: #3498db; font-size: 20px;">≡</span></div>',
                        className: 'custom-div-icon'
                    },
                    'Longsor': {
                        html: '<div class="marker-icon-container" style="background-color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><span style="color: #e67e22; font-size: 24px;">▼</span></div>',
                        className: 'custom-div-icon'
                    },
                    'Kebakaran': {
                        html: '<div class="marker-icon-container" style="background-color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="fas fa-fire" style="color: #e74c3c; font-size: 20px;"></i></div>',
                        className: 'custom-div-icon'
                    },
                    'Angin Puting Beliung': { icon: 'fa-wind', color: '#2ecc71' },
                    'Gempa Bumi': { icon: 'fa-asterisk', color: '#9b59b6' },
                    'Tsunami': { icon: 'fa-equals', color: '#34495e', scale: 1.2 },
                    'Kekeringan': { icon: 'fa-sun', color: '#f1c40f' },
                    'Gunung Meletus': {
                        html: '<div class="marker-icon-container" style="background-color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><span style="color: #c0392b; font-size: 24px;">▲</span></div>',
                        className: 'custom-div-icon'
                    }
                };

                // Fungsi untuk membuat custom icon
                const createCustomIcon = (type) => {
                    const config = iconConfigs[type] || { icon: 'fa-exclamation-triangle', color: '#95a5a6' };

                    // Untuk ikon kustom (Banjir, Longsor, Kebakaran, Gunung Meletus)
                    if (['Banjir', 'Longsor', 'Kebakaran', 'Gunung Meletus'].includes(type)) {
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
                    return L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div class="marker-icon-container">
                                <i class="fas ${config.icon}" style="color: ${config.color}; font-size: ${18 * scale}px;"></i>
                              </div>`,
                        iconSize: [36, 36],
                        iconAnchor: [18, 18],
                        popupAnchor: [0, -18]
                    });
                };

                // Tambahkan variabel untuk menyimpan semua marker
                const markers = {};

                // Tambahkan marker untuk setiap bencana
                const bounds = [];
                @foreach($disasters as $disaster)
                    try {
                        const lat = {{ $disaster->latitude }};
                        const lng = {{ $disaster->longitude }};
                        bounds.push([lat, lng]);

                        // Buat marker dengan icon yang sesuai
                        const marker = L.marker(
                            [lat, lng],
                            { icon: createCustomIcon('{{ $disaster->type }}') }
                        ).addTo(map);

                        // Simpan marker ke dalam objek markers dengan ID sebagai key
                        markers['{{ $disaster->id }}'] = marker;

                        // Tambahkan popup dengan informasi
                        const popupContent = `
                            <div class="disaster-popup">
                                <h5>${'{{ ucfirst($disaster->type) }}'}</h5>
                                <p><strong>Lokasi:</strong> {{ $disaster->location }}</p>
                                <p><strong>Tanggal:</strong> {{ $disaster->date->format('d/m/Y H:i') }}</p>
                                <p><strong>Tingkat Keparahan:</strong>
                                    <span class="badge bg-{{ $disaster->severity > 7 ? 'danger' : ($disaster->severity > 4 ? 'warning' : 'success') }}">
                                        {{ $disaster->severity }}/10
                                    </span>
                                </p>
                                <p><strong>Deskripsi:</strong> {{ Str::limit($disaster->description, 100) }}</p>
                                <a href="{{ route('disasters.show', $disaster) }}" class="btn btn-info btn-sm mt-2">
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
                document.querySelectorAll('.disaster-row').forEach(row => {
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

                    div.innerHTML = '<div style="font-weight: bold; margin-bottom: 5px;">Jenis Bencana</div>';

                    // Daftar bencana sesuai urutan di legenda
                    const disasterTypes = [
                        { type: 'Banjir', icon: '≡', color: '#3498db' },
                        { type: 'Longsor', icon: '▼', color: '#e67e22' },
                        { type: 'Kebakaran', icon: '<i class="fas fa-fire"></i>', color: '#e74c3c' },
                        { type: 'Angin Puting Beliung', icon: '<i class="fas fa-wind"></i>', color: '#2ecc71' },
                        { type: 'Gempa Bumi', icon: '*', color: '#9b59b6' },
                        { type: 'Tsunami', icon: '=', color: '#34495e' },
                        { type: 'Kekeringan', icon: '<i class="fas fa-sun"></i>', color: '#f1c40f' },
                        { type: 'Gunung Meletus', icon: '▲', color: '#c0392b' }
                    ];

                    disasterTypes.forEach(item => {
                        div.innerHTML += `
                            <div style="display: flex; align-items: center; margin: 5px 0;">
                                <span style="color: ${item.color}; font-size: 18px; width: 25px; text-align: center; margin-right: 5px;">
                                    ${item.icon}
                                </span>
                                <span>${item.type}</span>
                            </div>
                        `;
                    });

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
