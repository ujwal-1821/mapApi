@extends('welcome')

@section('title', 'Saved Locations')

@section('content')
    <div class="card mb-3">
        <div class="card-header fw-bold text-dark text-center p-2">
            <i class="bi bi-geo-alt text-success "></i> Saved Locations
        </div>
    </div>
    <div class="card-body mt-2">
        @if ($locations->isEmpty())
            <p class="">No saved locations yet.</p>
        @else
            <table class="table table-hover" id="yajra">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Place Name</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locations as $location)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->latitude }}</td>
                            <td>{{ $location->longitude }}</td>
                            <td>{{ $location->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('map.showLocation', $location->id) }}" target="_blank"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View on Map
                                </a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#yajra').DataTable({


            });
        });
    </script>
@endpush
