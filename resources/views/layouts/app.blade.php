<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Stok Takip Sistemi') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 0.375rem;
            margin: 0.25rem 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
            min-height: 100vh;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .alert-low-stock {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center text-white mb-4">
                        <h5><i class="bi bi-box-seam"></i> Stok Takip</h5>
                        <small>{{ Auth::user()->name }} ({{ Auth::user()->role->display_name }})</small>
                    </div>
                    
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        
                        @if(Auth::user()->hasPermission('product_management'))
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="bi bi-box me-2"></i>Ürünler
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('category_management'))
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="bi bi-tags me-2"></i>Kategoriler
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('supplier_management'))
                        <li class="nav-item">
                            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                                <i class="bi bi-truck me-2"></i>Tedarikçiler
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('customer_management'))
                        <li class="nav-item">
                            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                                <i class="bi bi-people me-2"></i>Müşteriler
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('stock_entry') || Auth::user()->hasPermission('stock_exit') || Auth::user()->hasPermission('stock_return'))
                        <li class="nav-item">
                            <a href="{{ route('stock-movements.index') }}" class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-left-right me-2"></i>Stok Hareketleri
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('reports_view'))
                        <li class="nav-item">
                            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="bi bi-graph-up me-2"></i>Raporlar
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('user_management'))
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="bi bi-person-check me-2"></i>Kullanıcılar
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('role_management'))
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="bi bi-shield-check me-2"></i>Roller & Yetkiler
                            </a>
                        </li>
                        @endif
                    </ul>
                    
                    <hr>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong>{{ Auth::user()->name }}</strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Çıkış Yap</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                    <div class="container-fluid">
                        @isset($header)
                            <h1 class="navbar-brand mb-0 h1">{{ $header }}</h1>
            @endisset
                        
                        <div class="ms-auto">
                            <!-- Düşük stok uyarıları -->
                            @php
                                $lowStockProducts = \App\Models\Product::active()->lowStock()->count();
                            @endphp
                            @if($lowStockProducts > 0)
                                <span class="badge bg-warning text-dark me-2">
                                    <i class="bi bi-exclamation-triangle"></i> {{ $lowStockProducts }} ürün düşük stokta
                                </span>
                            @endif
                            
                            <span class="text-muted">{{ now()->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                </nav>

            <!-- Page Content -->
                <main class="p-4">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                {{ $slot }}
            </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
    </body>
</html>
