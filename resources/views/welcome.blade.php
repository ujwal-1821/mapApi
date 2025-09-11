<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'My App')</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  @stack('styles')

  <style>
    body {
      margin: 0;
      background-color: #f8f9fa;
    }

    .sidebar {
      width: 220px;
      min-height: 100vh;
      background-color: #343a40;
    }

    .sidebar .nav-link {
      color: #adb5bd;
      padding: 12px 20px;
      font-weight: 500;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #495057;
      color: #ffffff;
    }

    .sidebar h4 {
      color: #ffffff;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid #495057;
      margin-bottom: 1rem;
    }

    .content {
      flex-grow: 1;
      padding: 2rem;
      background-color: #ffffff;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        min-height: auto;
      }
    }
  </style>
</head>
<body>

  <div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
      <h4>SpotFinder</h4>
      <nav class="nav flex-column">
        <a href="{{ route('map.index') }}"
           class="nav-link {{ request()->routeIs('map.index') ? 'active' : '' }}">
          <i class="bi bi-globe me-2 text-primary"></i> Map
        </a>
        <a href="{{ route('map.show') }}"
           class="nav-link {{ request()->routeIs('map.show') ? 'active' : '' }}">
          <i class="bi bi-geo-alt me-2 text-success"></i> Saved Locations
        </a>
      </nav>
    </div>

    <!-- Main content -->
    <div class="content flex-grow-1">
      @yield('content')
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
