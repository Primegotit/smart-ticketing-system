<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Ticket Oversight</h2>
    <div>
        <a href="<?= BASE_URL ?>/admin/tickets/export" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Export
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Tickets</h5>
    </div>
    <div class="card-body">
        <?php if (empty($tickets)): ?>
            <div class="alert alert-info">
                No tickets found in the system.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Event</th>
                            <th>Attendee</th>
                            <th>Purchase Date</th>
                            <th>Status</th>
                            <th>Scans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td>#<?= $ticket['id'] ?></td>
                                <td><?= htmlspecialchars($ticket['event_title']) ?></td>
                                <td>
                                    <?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($ticket['email']) ?></small>
                                </td>
                                <td><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $ticket['is_scanned'] ? 'success' : 'secondary' ?>">
                                        <?= $ticket['is_scanned'] ? 'Used' : 'Valid' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $ticket['scan_count'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/tickets/view/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/tickets/check/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-shield-exclamation"></i> Check
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