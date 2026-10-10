<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Fraud Check - Ticket #<?= $ticket['id'] ?></h2>
    <a href="<?= BASE_URL ?>/admin/tickets" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Tickets
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Ticket Details</h5>
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
                <h5 class="mb-0">Fraud Indicators</h5>
            </div>
            <div class="card-body">
                <?php if (empty($fraudIndicators)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> No fraud indicators detected.
                    </div>
                <?php else: ?>
                    <?php foreach ($fraudIndicators as $indicator): ?>
                        <div class="alert alert-<?= $indicator['severity'] === 'high' ? 'danger' : 'warning' ?>">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <?= $indicator['message'] ?>
                            <br>
                            <small>Type: <?= $indicator['type'] ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/admin/tickets/view/<?= $ticket['id'] ?>" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i> View Ticket Details
                    </a>
                
                    <?php if (!empty($fraudIndicators)): ?>
                        <button class="btn btn-outline-danger">
                            <i class="bi bi-shield-exclamation"></i> Flag as Fraudulent
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>