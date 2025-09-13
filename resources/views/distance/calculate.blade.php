@extends('welcome')

@section('title', 'Distance Calculator')

@section('content')
<div class="card mb-3">
    <div class="card-header fw-bold text-dark text-center p-2">
        <i class="bi bi-geo-alt text-success"></i> Distance Calculator
    </div>
</div>

<div class="card p-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label">From</label>
            <select id="fromSelect" class="form-select">
                <option value="">-- Select location --</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" data-lat="{{ $loc->latitude }}" data-lon="{{ $loc->longitude }}">
                        {{ $loc->name }} ({{ $loc->latitude }}, {{ $loc->longitude }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label">To</label>
            <select id="toSelect" class="form-select">
                <option value="">-- Select location --</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" data-lat="{{ $loc->latitude }}" data-lon="{{ $loc->longitude }}">
                        {{ $loc->name }} ({{ $loc->latitude }}, {{ $loc->longitude }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 text-end">
            <button type="button" id="calcBtn" class="btn btn-primary w-100">Calculate</button>
        </div>
    </div>

    <hr class="my-3">

    <div id="resultArea" style="display:none;">
        <h5>Result</h5>
        <p><strong>From:</strong> <span id="resFrom"></span></p>
        <p><strong>To:</strong> <span id="resTo"></span></p>
        <p><strong>Distance:</strong> <span id="resDistance"></span> km</p>
        <p><strong>Duration:</strong> <span id="resDuration"></span></p>
    </div>

    <div id="errorArea" class="alert alert-danger" style="display:none;"></div>

    <div id="map" style="height:400px;" class="mt-3"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const map = L.map('map').setView([20.5937, 78.9629], 5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM contributors' }).addTo(map);

let routeLayer;

function secondsToHMS(sec) {
    if (!sec && sec !== 0) return '-';
    sec = Math.round(sec);
    const hours = Math.floor(sec / 3600);
    const minutes = Math.floor((sec % 3600) / 60);
    const seconds = sec % 60;
    return `${hours}h ${minutes}m ${seconds}s`;
}

document.getElementById('calcBtn').addEventListener('click', async () => {
    const fromId = document.getElementById('fromSelect').value;
    const toId = document.getElementById('toSelect').value;

    document.getElementById('errorArea').style.display = 'none';
    document.getElementById('resultArea').style.display = 'none';

    if (!fromId || !toId) {
        document.getElementById('errorArea').innerText = 'Please select both locations.';
        document.getElementById('errorArea').style.display = 'block';
        return;
    }

    if (fromId === toId) {
        document.getElementById('errorArea').innerText = 'Please select two different locations.';
        document.getElementById('errorArea').style.display = 'block';
        return;
    }

    try {
       const res = await fetch('{{ route('distance.calculate') }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
    },
    body: JSON.stringify({ from_id: fromId, to_id: toId })
});
const json = await res.json(); // <-- this will fail if response is HTML


        if (!res.ok || json.error) {
            document.getElementById('errorArea').innerText = json.error || 'Failed to calculate distance';
            document.getElementById('errorArea').style.display = 'block';
            return;
        }

        document.getElementById('resFrom').innerText = json.from.name;
        document.getElementById('resTo').innerText = json.to.name;
        document.getElementById('resDistance').innerText = json.distance_km;
        document.getElementById('resDuration').innerText = secondsToHMS(json.duration_s);
        document.getElementById('resultArea').style.display = 'block';

        // Draw line on map
        if (routeLayer) map.removeLayer(routeLayer);
        routeLayer = L.geoJSON(json.geojson).addTo(map);
        map.fitBounds(routeLayer.getBounds());

    } catch (err) {
        console.error(err);
        document.getElementById('errorArea').innerText = 'An error occurred while fetching distance.';
        document.getElementById('errorArea').style.display = 'block';
    }
});
</script>
@endpush
