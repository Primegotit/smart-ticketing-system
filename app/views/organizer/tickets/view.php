<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tickets for <?= htmlspecialchars($event['title']) ?></h2>
    <div>
        <a href="<?= BASE_URL ?>/organizer/tickets" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to All Tickets
        </a>
        <a href="<?= BASE_URL ?>/organizer/tickets/scan" class="btn btn-primary">
            <i class="bi bi-qr-code-scan"></i> Scan Tickets
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Event Stats</h5>
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
                                <h6>Attendance Rate</h6>
                                <h3><?= count($tickets) > 0 ? round((count(array_filter($tickets, function($ticket) { return $ticket['is_scanned']; })) / count($tickets)) * 100) : 0 ?>%</h3>
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
        <h5 class="mb-0">Ticket List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($tickets)): ?>
            <div class="alert alert-info">
                No tickets sold for this event yet.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Attendee</th>
                            <th>Purchase Date</th>
                            <th>Status</th>
                            <th>QR Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td>#<?= $ticket['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($ticket['email']) ?></small>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $ticket['is_scanned'] ? 'success' : 'secondary' ?>">
                                        <?= $ticket['is_scanned'] ? 'Scanned' : 'Not Scanned' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="bg-light p-2 text-center" style="width: 80px;">
                                        <i class="bi bi-qr-code" style="font-size: 1.5rem;"></i>
                                        <small class="d-block"><?= substr($ticket['qr_code_hash'], 0, 8) ?>...</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>