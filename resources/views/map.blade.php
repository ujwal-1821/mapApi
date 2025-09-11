@extends('welcome')

@section('title', 'Map Search')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
  #map { height: 520px; margin-bottom: 15px; border-radius: 8px; }
  .leaflet-tooltip { 
    background-color: rgba(255, 255, 255, 0.9); 
    border: 1px solid #ccc; 
    border-radius: 4px; 
    padding: 5px 10px; 
    font-size: 12px; 
    box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
  }
</style>
@endpush

@section('content')
  <h2 class="mb-4 text-center">Search & Save Location</h2>

<div class="input-group mb-3">
  <input id="search" class="form-control" placeholder="Type an address or place">
  <button id="searchBtn" class="btn btn-primary ml-2">Search</button>
  <button id="clearBtn" class="btn btn-secondary">Clear Search</button>
</div>


  <div id="map"></div>

  <form id="saveForm" class="card p-3 shadow-sm">
    <div class="mb-2">
      <label class="form-label">Place Name</label>
      <input id="name" name="name" class="form-control" placeholder="Place name (optional)">
    </div>
    <div class="row">
      <div class="col">
        <label class="form-label">Latitude</label>
        <input id="latitude" name="latitude" class="form-control" readonly>
      </div>
      <div class="col">
        <label class="form-label">Longitude</label>
        <input id="longitude" name="longitude" class="form-control" readonly>
      </div>
    </div>
    <button type="submit" class="btn btn-success mt-3">Save</button>
  </form>

  <!-- Confirm Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Save</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to save this location?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmSaveBtn" class="btn btn-primary">Yes, Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Success</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          ✅ Location saved successfully!
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

//  MapTiler key
const maptilerKey = "isbdO3nAyw7xXKQ0wWgx";

// MapTiler base layers
const streets = L.tileLayer(`https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=${maptilerKey}`, {
  attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">© MapTiler</a> © OpenStreetMap contributors'
});

const satellite = L.tileLayer(`https://api.maptiler.com/maps/hybrid/{z}/{x}/{y}.png?key=${maptilerKey}`, {
  attribution: '<a href="https://www.maptiler.com/" target="_blank">© MapTiler</a>'
});

// Initialize map
const map = L.map('map', {
  center: [20.5937, 78.9629],
  zoom: 5,
  layers: [streets],
  scrollWheelZoom: true
});

// Zoom control
L.control.zoom({ position: 'topright' }).addTo(map);

// Layer switcher
const baseMaps = {
  "Streets": streets,
  "Satellite": satellite
};
L.control.layers(baseMaps).addTo(map);

let marker = null;

// Helper: get popup and tooltip color based on active layer
function getPopupColor() {
  return map.hasLayer(satellite) ? 'yellow' : 'skyblue';
}

// Forward geocode function
async function geocode(text) {
  const res = await fetch('{{ route("map.geocode") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ text })
  });
  return res.json();
}

// Reverse geocode function
async function reverseGeocode(lat, lon) {
  const res = await fetch('{{ route("map.geocode") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ lat, lon })
  });
  return res.json();
}

// Update form and marker with location details
function updateLocation(lat, lon, name) {
  if (marker) marker.remove();

  marker = L.marker([lat, lon])
    .addTo(map)
    .bindPopup(
      `<span style="color: ${getPopupColor()}; font-weight: 900; font-size: 16px; text-shadow: 1px 1px 2px black;">
        ${name}
      </span>`
    )
    .bindTooltip(
      `<div style="color: ${getPopupColor()}; font-weight: bold;">
        ${name}<br>
        Lat: ${lat.toFixed(7)}<br>
        Lon: ${lon.toFixed(7)}
      </div>`,
      { 
        permanent: false, 
        direction: 'top', 
        offset: [0, -10], 
        className: 'leaflet-tooltip'
      }
    )
    .openPopup();

  map.setView([lat, lon], 14);
  document.getElementById('latitude').value = lat.toFixed(7);
  document.getElementById('longitude').value = lon.toFixed(7);
  document.getElementById('name').value = name;
}

// Search button
document.getElementById('searchBtn').addEventListener('click', async () => {
  const text = document.getElementById('search').value.trim();
  if (!text) return alert('Type an address to search');

  const data = await geocode(text);
  if (!data || !data.features || data.features.length === 0) {
    return alert('No results found');
  }

  const f = data.features[0];
  const [lon, lat] = f.geometry.coordinates;
updateLocation(lat, lon, f.properties.label || f.properties.name || text);
});

document.getElementById('clearBtn').addEventListener('click', () => {
  clearSearch();
});


// Map click event with reverse geocoding
map.on('click', async (e) => {
  const { lat, lng } = e.latlng;

  // Immediately update with temporary name for responsiveness
  updateLocation(lat, lng, 'Loading...');

  try {
    const data = await reverseGeocode(lat, lng);
    let name = 'Unknown Location'; // Fallback if no name is found

    if (data && data.features && data.features.length > 0) {
name = data.features[0].properties.label || data.features[0].properties.name || name;    }

    // Update with resolved name
    updateLocation(lat, lng, name);
  } catch (error) {
    console.error('Reverse geocoding failed:', error);
    updateLocation(lat, lng, 'Unknown Location'); // Fallback on error
  }
});

// Update popup and tooltip color when base layer changes
map.on('baselayerchange', () => {
  if (!marker) return;

  const label = marker.getPopup().getContent().replace(/<[^>]+>/g, '');
  marker.setPopupContent(
    `<span style="color: ${getPopupColor()}; font-weight: 900; font-size: 16px; text-shadow: 1px 1px 2px black;">
      ${label}
    </span>`
  ).openPopup();

  marker.setTooltipContent(
    `<div style="color: ${getPopupColor()}; font-weight: bold;">
      ${label}<br>
      Lat: ${document.getElementById('latitude').value}<br>
      Lon: ${document.getElementById('longitude').value}
    </div>`
  );
});

// Save form
const saveForm = document.getElementById('saveForm');
let formPayload = {};

saveForm.addEventListener('submit', (e) => {
  e.preventDefault();
  formPayload = {
    name: document.getElementById('name').value,
    latitude: parseFloat(document.getElementById('latitude').value),
    longitude: parseFloat(document.getElementById('longitude').value)
  };

  if (isNaN(formPayload.latitude) || isNaN(formPayload.longitude)) {
    return alert('Pick a valid location first');
  }

  const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
  modal.show();
});

document.getElementById('confirmSaveBtn').addEventListener('click', async () => {
  const res = await fetch('{{ route("locations.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify(formPayload)
  });

  const json = await res.json();
  bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();

  if (res.ok) {
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
  } else {
    alert('Could not save location.');
    console.error(json);
  }
});


// Helper: get query params
function getQueryParams() {
  const params = {};
  window.location.search.substring(1).split('&').forEach(pair => {
    const [key, value] = pair.split('=');
    if (key && value) {
      params[decodeURIComponent(key)] = decodeURIComponent(value);
    }
  });
  return params;
}

window.addEventListener('DOMContentLoaded', () => {
  const params = getQueryParams();
  if (params.lat && params.lon) {
    const lat = parseFloat(params.lat);
    const lon = parseFloat(params.lon);
    if (!isNaN(lat) && !isNaN(lon)) {
      // If you want to get place name via reverse geocode API on load
      reverseGeocode(lat, lon).then(data => {
        let name = 'Unknown Location';
        if (data && data.features && data.features.length > 0) {
          name = data.features[0].properties.label || data.features[0].properties.name || name;
        }
        updateLocation(lat, lon, name);
      }).catch(() => {
        updateLocation(lat, lon, 'Unknown Location');
      });
    }
  }
});

function clearSearch() {
  if (marker) {
    marker.remove();
    marker = null;
  }

  document.getElementById('search').value = '';
  document.getElementById('name').value = '';
  document.getElementById('latitude').value = '';
  document.getElementById('longitude').value = '';

  map.setView([20.5937, 78.9629], 5); // Reset to default center (India)
}


</script>
@endpush