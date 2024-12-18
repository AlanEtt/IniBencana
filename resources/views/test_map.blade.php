@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Test Peta</h1>
    <div id="map" style="height: 400px;"></div>
</div>

<script>
    var map = L.map('map').setView([-6.2088, 106.8456], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);
</script>
@endsection
