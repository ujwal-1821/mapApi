@extends('welcome')

@section('title', 'Map Search')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
  #map { height: 520px; margin-bottom: 15px; border-radius: 8px; }
</style>
@endpush

@section('content')
  <h2 class="mb-4 text-center">Search & Save Location</h2>

  <div class="input-group mb-3">
    <input id="search" class="form-control" placeholder="Type an address or place">
    <button id="searchBtn" class="btn btn-primary">Search</button>
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

const map = L.map('map').setView([20.5937, 78.9629], 5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

let marker = null;

async function geocode(text) {
  const res = await fetch('{{ route("map.geocode") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ text })
  });
  return res.json();
}

document.getElementById('searchBtn').addEventListener('click', async () => {
  const text = document.getElementById('search').value.trim();
  if (!text) return alert('Type an address to search');

  const data = await geocode(text);
  if (!data || !data.features || data.features.length === 0) {
    return alert('No results found');
  }

  const f = data.features[0];
  const [lon, lat] = f.geometry.coordinates;

  if (marker) marker.remove();
  marker = L.marker([lat, lon]).addTo(map).bindPopup(f.properties.label || text).openPopup();

  map.setView([lat, lon], 14);
  document.getElementById('latitude').value = lat.toFixed(7);
  document.getElementById('longitude').value = lon.toFixed(7);
  document.getElementById('name').value = f.properties.label || '';
});

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
</script>
@endpush
