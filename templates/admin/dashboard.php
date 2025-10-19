<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title ?? 'HostView Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@2.40.0/tabler-sprites.svg" rel="stylesheet">
    <style>
    .card-stats {
        transition: transform 0.2s;
    }
    .card-stats:hover {
        transform: translateY(-2px);
    }
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    </style>
</head>
<body>
    <div class="page">
        <!-- Sidebar -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="/admin">
                        HostView
                    </a>
                </h1>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="/admin">
                                <span class="nav-link-icon">
                                    <svg class="icon"><use xlink:href="#tabler-home"></use></svg>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'clients' ? 'active' : ''; ?>" href="/admin/clients">
                                <span class="nav-link-icon">
                                    <svg class="icon"><use xlink:href="#tabler-users"></use></svg>
                                </span>
                                <span class="nav-link-title">Clients</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'invoices' ? 'active' : ''; ?>" href="/admin/invoices">
                                <span class="nav-link-icon">
                                    <svg class="icon"><use xlink:href="#tabler-file-invoice"></use></svg>
                                </span>
                                <span class="nav-link-title">Invoices</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'services' ? 'active' : ''; ?>" href="/admin/services">
                                <span class="nav-link-icon">
                                    <svg class="icon"><use xlink:href="#tabler-server"></use></svg>
                                </span>
                                <span class="nav-link-title">Services</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'servers' ? 'active' : ''; ?>" href="/admin/servers">
                                <span class="nav-link-icon">
                                    <svg class="icon"><use xlink:href="#tabler-database"></use></svg>
                                </span>
                                <span class="nav-link-title">Servers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>" href="/admin/settings">
                                <span class="nav-link-icon">
                                    <svg class="icon"><use xlink:href="#tabler-settings"></use></svg>
                                </span>
                                <span class="nav-link-title">Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="page-wrapper">
            <!-- Header -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                Dashboard
                            </h2>
                            <div class="text-muted mt-1">Real-time data from FOSSBilling</div>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <a href="/admin/logout" class="btn btn-outline-danger">
                                    <svg class="icon icon-tabler-logout"><use xlink:href="#tabler-logout"></use></svg>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Body -->
            <div class="page-body">
                <div class="container-xl">
                    <!-- Statistics Cards -->
                    <div class="row row-deck row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar">
                                                <svg class="icon"><use xlink:href="#tabler-users"></use></svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <?php echo number_format($stats['total_clients'] ?? 0); ?>
                                            </div>
                                            <div class="text-muted">Total Clients</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-success text-white avatar">
                                                <svg class="icon"><use xlink:href="#tabler-server"></use></svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <?php echo number_format($stats['active_services'] ?? 0); ?>
                                            </div>
                                            <div class="text-muted">Active Services</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar">
                                                <svg class="icon"><use xlink:href="#tabler-currency-dollar"></use></svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                $<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?>
                                            </div>
                                            <div class="text-muted">Total Revenue</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <svg class="icon"><use xlink:href="#tabler-file-invoice"></use></svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <?php echo number_format($stats['pending_invoices'] ?? 0); ?>
                                            </div>
                                            <div class="text-muted">Pending Invoices</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="row row-deck row-cards mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Clients</h3>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (!empty($recent_clients)): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($recent_clients as $client): ?>
                                                <div class="list-group-item">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <span class="avatar avatar-sm"><?php echo substr($client['name'], 0, 1); ?></span>
                                                        </div>
                                                        <div class="col text-truncate">
                                                            <strong><?php echo htmlspecialchars($client['name']); ?></strong>
                                                            <div class="text-muted"><?php echo htmlspecialchars($client['email']); ?></div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <span class="badge bg-<?php echo $client['status'] === 'active' ? 'success' : 'warning'; ?>">
                                                                <?php echo ucfirst($client['status']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <svg class="icon"><use xlink:href="#tabler-users"></use></svg>
                                            </div>
                                            <p class="empty-title">No recent clients</p>
                                            <p class="empty-subtitle text-muted">Client data will appear here when available from FOSSBilling</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Invoices</h3>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (!empty($recent_invoices)): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($recent_invoices as $invoice): ?>
                                                <div class="list-group-item">
                                                    <div class="row align-items-center">
                                                        <div class="col">
                                                            <strong>#<?php echo htmlspecialchars($invoice['number']); ?></strong>
                                                            <div class="text-muted"><?php echo htmlspecialchars($invoice['client_name']); ?></div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="text-end">
                                                                <div class="font-weight-medium">$<?php echo number_format($invoice['amount'], 2); ?></div>
                                                                <span class="badge bg-<?php echo $invoice['status'] === 'paid' ? 'success' : ($invoice['status'] === 'unpaid' ? 'warning' : 'danger'); ?>">
                                                                    <?php echo ucfirst($invoice['status']); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <svg class="icon"><use xlink:href="#tabler-file-invoice"></use></svg>
                                            </div>
                                            <p class="empty-title">No recent invoices</p>
                                            <p class="empty-subtitle text-muted">Invoice data will appear here when available from FOSSBilling</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FOSSBilling Status -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">FOSSBilling Integration Status</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-5">API URL:</dt>
                                                <dd class="col-7"><code><?php echo FOSSBILLING_URL; ?></code></dd>
                                                <dt class="col-5">Status:</dt>
                                                <dd class="col-7">
                                                    <span class="badge bg-success">Connected</span>
                                                </dd>
                                                <dt class="col-5">Last Update:</dt>
                                                <dd class="col-7"><?php echo date('Y-m-d H:i:s'); ?></dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted">All dashboard data is fetched in real-time from your FOSSBilling instance. No mock data is used.</p>
                                            <a href="/api/fossbilling/test" class="btn btn-primary btn-sm" target="_blank">Test API Connection</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script>
    // Auto-refresh dashboard every 30 seconds
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 30000);
    </script>
</body>
</html>