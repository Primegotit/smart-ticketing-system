<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Ticket Management</h2>
    <div>
        <a href="<?= BASE_URL ?>/organizer/tickets/scan" class="btn btn-primary">
            <i class="bi bi-qr-code-scan"></i> Scan Tickets
        </a>
    </div>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Quick Stats</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body">
                                <h6>Total Tickets</h6>
                                <h3><?= count($tickets) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h6>Scanned Tickets</h6>
                                <h3><?= count(array_filter($tickets, function($ticket) { return $ticket['is_scanned']; })) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h6>Pending Scans</h6>
                                <h3><?= count(array_filter($tickets, function($ticket) { return !$ticket['is_scanned']; })) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark text-center">
                            <div class="card-body">
                                <h6>Events</h6>
                                <h3><?= count($events) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Tickets</h5>
    </div>
    <div class="card-body">
        <?php if (empty($tickets)): ?>
            <div class="alert alert-info">
                No tickets found for your events.
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
                                <td data-value="<?= $ticket['is_scanned'] ? 'Scanned' : 'Not Scanned' ?>">
                                    <span class="badge bg-<?= $ticket['is_scanned'] ? 'success' : 'secondary' ?>">
                                        <?= $ticket['is_scanned'] ? 'Scanned' : 'Not Scanned' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/organizer/tickets/event/<?= $ticket['event_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        View Event Tickets
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