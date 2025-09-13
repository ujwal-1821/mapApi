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
            background-color: #343a40;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* App name/header */
        .sidebar-header {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(45deg, #bba964, #e08f32);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 10px rgba(84, 77, 49, 0.3);
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #495057;
            flex-shrink: 0;
            text-align: center;
        }

        .app-name {
            font-family: "Poppins", sans-serif;
        }

        /* Sidebar body scrolls */
        .sidebar-body {
            flex-grow: 1;
            overflow-y: auto;
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

        .content {
            flex-grow: 1;
            padding: 2rem;
            background-color: #ffffff;
            height: 100vh;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column">
            <!-- Fixed App Name -->
            <div class="sidebar-header">
                <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                <span class="app-name">SpotFinder</span>
            </div>

            <!-- Scrollable Nav -->
            <div class="sidebar-body">
                <nav class="nav flex-column">
                    <a href="{{ route('map.index') }}"
                        class="nav-link {{ request()->routeIs('map.index') ? 'active' : '' }}">
                        <i class="bi bi-globe me-2 text-primary"></i> Maps
                    </a>
                    <a href="{{ route('map.show') }}"
                        class="nav-link {{ request()->routeIs('map.show') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt me-2 text-success"></i> Saved Locations
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="content flex-grow-1">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- yajra DataTable --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    @stack('scripts')
</body>

</html>
