<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Ticket Details - #<?= $ticket['id'] ?></h2>
    <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Tickets
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Ticket Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Event:</strong> <?= htmlspecialchars($ticket['event_title']) ?></p>
                <p><strong>Attendee:</strong> <?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($ticket['email']) ?></p>
                <p><strong>Purchase Date:</strong> <?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?= $ticket['is_scanned'] ? 'success' : 'secondary' ?>">
                        <?= $ticket['is_scanned'] ? 'Used' : 'Valid' ?>
                    </span>
                </p>
                <p><strong>QR Code Hash:</strong> <code><?= $ticket['qr_code_hash'] ?></code></p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Scan History</h5>
            </div>
            <div class="card-body">
                <?php if (empty($scanHistory)): ?>
                    <p class="text-muted">No scan history found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Scanner</th>
                                    <th>Result</th>
                                    <th>Scanned At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scanHistory as $scan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($scan['scanner_name'] ?? 'Unknown') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $scan['scan_result'] === 'valid' ? 'success' : ($scan['scan_result'] === 'duplicate' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($scan['scan_result']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y g:i A', strtotime($scan['scanned_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/admin/tickets/check/<?= $ticket['id'] ?>" class="btn btn-outline-warning">
                        <i class="bi bi-shield-exclamation"></i> Check for Fraud
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
</div>