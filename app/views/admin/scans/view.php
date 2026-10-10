<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Scan Details - #<?= $scan['id'] ?></h2>
    <a href="<?= BASE_URL ?>/admin/scans" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Scans
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Scan Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Scan ID:</strong> #<?= $scan['id'] ?></p>
                <p><strong>Ticket ID:</strong> #<?= $scan['ticket_id'] ?></p>
                <p><strong>Event:</strong> <?= htmlspecialchars($scan['event_title']) ?></p>
                <p><strong>Attendee:</strong> <?= htmlspecialchars($scan['attendee_name']) ?></p>
                <p><strong>Scanner:</strong> <?= htmlspecialchars($scan['scanner_name']) ?></p>
                <p><strong>Result:</strong> 
                    <span class="badge bg-<?= $scan['scan_result'] === 'valid' ? 'success' : ($scan['scan_result'] === 'duplicate' ? 'warning' : 'danger') ?>">
                        <?= ucfirst($scan['scan_result']) ?>
                    </span>
                </p>
                <p><strong>Scanned At:</strong> <?= date('M j, Y g:i A', strtotime($scan['scanned_at'])) ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/admin/tickets/view/<?= $scan['ticket_id'] ?>" class="btn btn-outline-primary">
                        <i class="bi bi-ticket"></i> View Ticket
                    </a>
                    <a href="<?= BASE_URL ?>/admin/tickets/check/<?= $scan['ticket_id'] ?>" class="btn btn-outline-warning">
                        <i class="bi bi-shield-exclamation"></i> Check for Fraud
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>