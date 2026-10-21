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
                <table class="table table-striped" data-list-tools data-search-placeholder="Search by ticket ID, event, attendee name or email...">
                    <thead>
                        <tr>
                            <th data-type="number">Ticket ID</th>
                            <th data-type="text" data-filter>Event</th>
                            <th data-type="text">Attendee</th>
                            <th data-type="date" data-filter="range">Purchase Date</th>
                            <th data-type="text" data-filter>Status</th>
                            <th data-type="number">Scans</th>
                            <th data-type="none">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td data-value="<?= (int)$ticket['id'] ?>">#<?= $ticket['id'] ?></td>
                                <td><?= htmlspecialchars($ticket['event_title']) ?></td>
                                <td data-value="<?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?>">
                                    <?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($ticket['email']) ?></small>
                                </td>
                                <td data-value="<?= strtotime($ticket['created_at']) ?>"><?= date('M j, Y', strtotime($ticket['created_at'])) ?></td>
                                <td data-value="<?= $ticket['is_scanned'] ? 'Used' : 'Valid' ?>">
                                    <span class="badge bg-<?= $ticket['is_scanned'] ? 'success' : 'secondary' ?>">
                                        <?= $ticket['is_scanned'] ? 'Used' : 'Valid' ?>
                                    </span>
                                </td>
                                <td data-value="<?= (int)($ticket['scan_count'] ?? 0) ?>">
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