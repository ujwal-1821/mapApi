<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'My App')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


  @stack('styles')

  <style>
    body {
      display: flex;
      min-height: 100vh;
      background: #f9f9f9;
    }
    .sidebar {
      width: 220px;
      background: #343a40;
      color: #fff;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      padding: 20px 0;
    }
    .sidebar a {
      color: #ddd;
      padding: 10px 20px;
      display: block;
      text-decoration: none;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #495057;
      color: #fff;
    }
    .content {
      flex-grow: 1;
      padding: 20px;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h4 class="px-3 mb-4">My App</h4>
    <a href="{{ route('map.index') }}" class="{{ request()->routeIs('map.index') ? 'active' : '' }}">
      🌐 Map
    </a>
    <a href="{{ route('map.show') }}" class="{{ request()->routeIs('map.show') ? 'active' : '' }}">
       <i class="bi bi-geo-alt text-success"></i> Saved Locations
    </a>
  </div>

  <div class="content">
    @yield('content')
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
