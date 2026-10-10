<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Reports Dashboard</h2>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-currency-dollar display-4 text-primary"></i>
                <h5 class="card-title mt-3">Sales Reports</h5>
                <p class="card-text">View detailed sales reports and revenue analytics</p>
                <a href="<?= BASE_URL ?>/admin/reports/sales" class="btn btn-primary">View Sales Reports</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people display-4 text-success"></i>
                <h5 class="card-title mt-3">Attendance Reports</h5>
                <p class="card-text">View event attendance statistics and trends</p>
                <a href="<?= BASE_URL ?>/admin/reports/attendance" class="btn btn-success">View Attendance Reports</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-bar-chart display-4 text-info"></i>
                <h5 class="card-title mt-3">Event Reports</h5>
                <p class="card-text">Generate detailed reports for specific events</p>
                <a href="<?= BASE_URL ?>/admin/reports/attendance" class="btn btn-info">View Event Reports</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Quick Export</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h6>Sales Data</h6>
                <a href="<?= BASE_URL ?>/admin/reports/export/sales" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-download"></i> Export Sales Report
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Attendance Data</h6>
                <a href="<?= BASE_URL ?>/admin/reports/export/attendance" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download"></i> Export Attendance Report
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Custom Reports</h6>
                <a href="<?= BASE_URL ?>/admin/reports/sales" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-gear"></i> Generate Custom Report
                </a>
            </div>
        </div>
    </div>
</div>