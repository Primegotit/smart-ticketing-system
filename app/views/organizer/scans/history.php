<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Scan History</h2>
    <a href="<?= BASE_URL ?>/organizer/scans" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Scanning
    </a>
</div>

<?php if ($event): ?>
    <div class="alert alert-info">
        Showing scan history for: <strong><?= htmlspecialchars($event['title']) ?></strong>
        <a href="<?= BASE_URL ?>/organizer/scans/history" class="float-end">View All Events</a>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Scan Records</h5>
    </div>
    <div class="card-body">
        <?php if (empty($scans)): ?>
            <div class="alert alert-info">
                No scan records found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Scan ID</th>
                            <th>Event</th>
                            <th>Attendee</th>
                            <th>QR Code</th>
                            <th>Scanned By</th>
                            <th>Result</th>
                            <th>Scanned At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scans as $scan): ?>
                            <tr>
                                <td>#<?= $scan['id'] ?></td>
                                <td><?= htmlspecialchars($scan['event_title']) ?></td>
                                <td>
                                    <?= htmlspecialchars($scan['first_name'] . ' ' . $scan['last_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($scan['attendee_email']) ?></small>
                                </td>
                                <td>
                                    <small><?= substr($scan['qr_code_hash'], 0, 8) ?>...</small>
                                </td>
                                <td><?= htmlspecialchars($scan['staff_first'] . ' ' . $scan['staff_last']) ?></td>
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