@extends('layouts.app')

@section('styles')
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Tambah Data Bencana</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('disasters.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="type" class="form-label">Jenis Bencana</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Pilih Jenis Bencana</option>
                                <option value="Banjir">Banjir</option>
                                <option value="Longsor">Longsor</option>
                                <option value="Kebakaran">Kebakaran</option>
                                <option value="Angin Puting Beliung">Angin Puting Beliung</option>
                                <option value="Gempa Bumi">Gempa Bumi</option>
                                <option value="Tsunami">Tsunami</option>
                                <option value="Kekeringan">Kekeringan</option>
                                <option value="Gunung Meletus">Gunung Meletus</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="map" class="form-label">Lokasi Bencana (Klik pada peta)</label>
                            <div id="map" style="height: 400px;" class="mb-2 border rounded"></div>
                            <input type="hidden" name="location" id="location" required>
                            <small class="text-muted">Koordinat yang dipilih: <span id="selectedCoords">Belum dipilih</span></small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Deskripsi Bencana</label>
                            <textarea name="description" id="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror"
                                required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="date" class="form-label">Tanggal & Waktu Kejadian</label>
                            <input type="datetime-local" name="date" id="date"
                                class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date') }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="severity" class="form-label">Tingkat Keparahan (1-10)</label>
                            <input type="number" name="severity" id="severity"
                                class="form-control @error('severity') is-invalid @enderror"
                                min="1" max="10" value="{{ old('severity') }}" required>
                            @error('severity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Data Bencana</button>
                            <a href="{{ route('disasters.index') }}" class="btn btn-secondary">Batal</a>
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
            // Inisialisasi map
            const map = L.map('map').setView([-6.5935, 110.6776], 13);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Variabel untuk marker
            let marker;

            // Event handler untuk klik pada map
            map.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);

                // Hapus marker lama jika ada
                if (marker) {
                    map.removeLayer(marker);
                }

                // Tambah marker baru
                marker = L.marker(e.latlng).addTo(map);

                // Update nilai input hidden dan tampilkan koordinat
                document.getElementById('location').value = `${lat},${lng}`;
                document.getElementById('selectedCoords').textContent = `${lat}, ${lng}`;
            });

            // Tambahkan marker untuk lokasi awal
            marker = L.marker([-6.5935, 110.6776]).addTo(map);
            document.getElementById('location').value = '-6.5935,110.6776';
            document.getElementById('selectedCoords').textContent = '-6.5935, 110.6776';

            // Invalidate size setelah map dimuat
            setTimeout(function(){ map.invalidateSize()}, 100);

            // Tambahkan Font Awesome icons ke select option
            const typeSelect = document.getElementById('type');
            const options = typeSelect.options;

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (option.value) {
                    const iconClass = getIconClass(option.value);
                    option.innerHTML = `<i class="fas ${iconClass}"></i> ${option.text}`;
                }
            }
        });

        // Fungsi untuk mendapatkan class icon berdasarkan tipe bencana
        function getIconClass(type) {
            const icons = {
                banjir: 'fa-water',
                longsor: 'fa-mountain',
                kebakaran: 'fa-fire',
                angin_kencang: 'fa-wind',
                gempa: 'fa-house-crack',
                tsunami: 'fa-water',
                kekeringan: 'fa-sun',
                gunung_meletus: 'fa-volcano'
            };
            return icons[type] || 'fa-exclamation-triangle';
        }

        // Fungsi untuk mendapatkan icon berdasarkan jenis bencana
        function getDisasterIcon(type) {
            // Konfigurasi icon untuk berbagai jenis bencana
            const iconConfigs = {
                'Banjir': {
                    html: '<div class="banjir-icon"></div>',
                    className: 'custom-div-icon'
                },
                'Longsor': {
                    html: '<div class="longsor-icon"></div>',
                    className: 'custom-div-icon'
                },
                'Kebakaran': {
                    html: '<div class="kebakaran-icon"></div>',
                    className: 'custom-div-icon'
                },
                'Angin Puting Beliung': { icon: 'fa-wind', color: '#2ecc71' },
                'Gempa Bumi': { icon: 'fa-asterisk', color: '#9b59b6' },
                'Tsunami': { icon: 'fa-equals', color: '#34495e', scale: 1.2 },
                'Kekeringan': { icon: 'fa-sun', color: '#f1c40f' },
                'Gunung Meletus': {
                    html: '<div class="marker-icon-container" style="background-color: transparent; box-shadow: none;"><span style="color: #c0392b; font-size: 24px;">▲</span></div>',
                    className: 'custom-div-icon'
                }
            };

            const config = iconConfigs[type] || { icon: 'fa-exclamation-triangle', color: '#95a5a6' };

            // Untuk ikon kustom (Banjir, Longsor, Kebakaran)
            if (['Banjir', 'Longsor', 'Kebakaran'].includes(type)) {
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
        }
    </script>
@endsection
