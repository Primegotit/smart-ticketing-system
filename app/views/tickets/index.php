<h2 class="mb-4">My Tickets</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<?php if (empty($tickets)): ?>
    <div class="alert alert-info">
        You don't have any tickets yet. <a href="<?= BASE_URL ?>/events">Browse events</a> to purchase tickets.
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($tickets as $ticket): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?= $ticket['is_scanned'] ? 'danger' : 'success' ?>">
                            <?= $ticket['is_scanned'] ? 'Used' : 'Valid' ?>
                        </span>
                        <small class="text-muted">#<?= $ticket['id'] ?></small>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($ticket['event_title']) ?></h5>
                        <p class="card-text text-muted">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($ticket['venue']) ?>
                        </p>
                        <p class="card-text text-muted">
                            <i class="bi bi-calendar"></i> <?= date('M j, Y', strtotime($ticket['date_time'])) ?>
                        </p>
                        <p class="card-text text-muted">
                            <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($ticket['date_time'])) ?>
                        </p>
                        <div class="mt-3">
                            <p class="mb-1"><strong>QR Code:</strong></p>
                            <div class="text-center">
                                <img src="<?= $ticket['qr_code_url'] ?>" alt="QR Code" class="img-fluid" style="max-width: 150px;">
                                <p class="small text-muted mt-2 mb-1">Scan this code at the event</p>
                                <p class="small mb-0">Ticket code: <code class="user-select-all text-break"><?= htmlspecialchars($ticket['qr_code_hash']) ?></code></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="<?= BASE_URL ?>/tickets/<?= $ticket['id'] ?>" class="btn btn-outline-primary w-100">View Ticket</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>