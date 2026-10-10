<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Scan Monitoring</h2>
    <div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/scans/export">All Scans</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/scans/export/valid">Valid Scans</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/scans/export/invalid">Invalid Scans</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/scans/export/duplicate">Duplicate Scans</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/scans/export/suspicious">Suspicious Scans</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Scans</h5>
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
                            <th>Ticket ID</th>
                            <th>Event</th>
                            <th>Attendee</th>
                            <th>Scanner</th>
                            <th>Result</th>
                            <th>Scanned At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scans as $scan): ?>
                            <tr>
                                <td>#<?= $scan['id'] ?></td>
                                <td>#<?= $scan['ticket_id'] ?></td>
                                <td><?= htmlspecialchars($scan['event_title']) ?></td>
                                <td><?= htmlspecialchars($scan['attendee_name']) ?></td>
                                <td><?= htmlspecialchars($scan['scanner_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $scan['scan_result'] === 'valid' ? 'success' : ($scan['scan_result'] === 'duplicate' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($scan['scan_result']) ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($scan['scanned_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/scans/view/<?= $scan['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>