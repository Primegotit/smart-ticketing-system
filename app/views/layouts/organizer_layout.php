<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Organizer - Smart Ticketing System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.5rem 1rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>/organizer/dashboard">Smart Ticketing Organizer</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text me-3">Hello, <?= htmlspecialchars($_SESSION['user']['first_name']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>">View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar py-3">
                <!-- Update the sidebar navigation -->
<!-- Update the sidebar navigation -->
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'organizer/dashboard') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/organizer/dashboard">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'organizer/events') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/organizer/events">
            <i class="bi bi-calendar-event me-2"></i> My Events
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'organizer/tickets') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/organizer/tickets">
            <i class="bi bi-ticket-perforated me-2"></i> Tickets
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'organizer/transactions') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/organizer/transactions">
            <i class="bi bi-cash-coin me-2"></i> Transactions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'organizer/scans') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/organizer/scans">
            <i class="bi bi-qr-code-scan me-2"></i> Scan Tickets
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'organizer/scanner') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/organizer/scanner">
            <i class="bi bi-qr-code me-2"></i> QR Codes
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'organizer/reports') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/organizer/reports">
            <i class="bi bi-bar-chart me-2"></i> Reports
        </a>
    </li>
</ul>
            </div>
            <div class="col-md-10 py-3">
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if (isset($success) && $success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>