@extends('welcome')

@section('title', 'Saved Locations')

@section('content')
<div class="card mb-3">
  <div class="card-header fw-bold text-dark text-center p-2">
    <i class="bi bi-geo-alt text-success "></i> Saved Locations
  </div>
  </div>
  <div class="card-body mt-2">
    @if($locations->isEmpty())
      <p class="">No saved locations yet.</p>
    @else
      <table class="table table-hover" >
        <thead>
          <tr>
            <th>S.NO</th>
            <th>Place Name</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          @foreach($locations as $location)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $location->name }}</td>
              <td>{{ $location->latitude }}</td>
              <td>{{ $location->longitude }}</td>
              <td>{{ $location->created_at->format('d M Y, H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

@endsection
