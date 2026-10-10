<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>QR Code Management</h2>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-qr-code-scan display-4 text-primary"></i>
                <h5 class="card-title mt-3">Scan Tickets</h5>
                <p class="card-text">Scan QR codes to validate event tickets</p>
                <a href="<?= BASE_URL ?>/organizer/scanner/scan" class="btn btn-primary">Start Scanning</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-clock-history display-4 text-success"></i>
                <h5 class="card-title mt-3">Scan History</h5>
                <p class="card-text">View history of all ticket scans</p>
                <a href="<?= BASE_URL ?>/organizer/scanner/history" class="btn btn-success">View History</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-download display-4 text-info"></i>
                <h5 class="card-title mt-3">Download QR Codes</h5>
                <p class="card-text">Download QR codes for specific tickets</p>
                <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#downloadQRModal">Download</a>
            </div>
        </div>
    </div>
</div>

<!-- Download QR Code Modal -->
<div class="modal fade" id="downloadQRModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="<?= BASE_URL ?>/organizer/scanner/download" method="GET">
                    <div class="mb-3">
                        <label for="ticketId" class="form-label">Enter Ticket ID</label>
                        <input type="number" class="form-control" id="ticketId" name="ticketId" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Download QR Code</button>
                </form>
            </div>
        </div>
    </div>
</div>