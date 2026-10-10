<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-center">
                <h3 class="card-title mb-0">Event Ticket</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4><?= htmlspecialchars($ticket['event_title']) ?></h4>
                        <p class="text-muted">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($ticket['venue']) ?>
                        </p>
                        <p class="text-muted">
                            <i class="bi bi-calendar"></i> <?= date('F j, Y', strtotime($ticket['date_time'])) ?>
                        </p>
                        <p class="text-muted">
                            <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($ticket['date_time'])) ?>
                        </p>
                        
                        <hr>
                        
                        <h5>Attendee Information</h5>
                        <p><strong>Name:</strong> <?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($ticket['email']) ?></p>
                        
                        <p class="mt-3">
                            <span class="badge bg-<?= $ticket['is_scanned'] ? 'danger' : 'success' ?>">
                                <?= $ticket['is_scanned'] ? 'This ticket has been used' : 'Valid for entry' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        <!-- QR Code Display -->
                        <div class="border p-3 mb-3">
                            <img src="<?= $qrCode['url'] ?>" alt="QR Code" class="img-fluid">
                            <p class="small text-muted mt-2 mb-1">Ticket #<?= $ticket['id'] ?></p>
                            <p class="small mb-1"><strong>Ticket code</strong> (use this if the QR code won't scan)</p>
                            <code class="d-block user-select-all text-break" id="ticket-hash" style="font-size: 0.95rem;"><?= htmlspecialchars($ticket['qr_code_hash']) ?></code>
                        </div>
                        
                        <?php if (!$ticket['is_scanned']): ?>
                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle"></i> Present this QR code at the event entrance for scanning.
                            </div>
                            <a href="<?= BASE_URL ?>/tickets/pdf/<?= $ticket['id'] ?>" class="btn btn-primary btn-sm mb-1">
                                <i class="bi bi-file-earmark-pdf"></i> Download Ticket (PDF)
                            </a>
                            <a href="<?= BASE_URL ?>/tickets/download/<?= $ticket['id'] ?>" class="btn btn-outline-primary btn-sm mb-1">
                                <i class="bi bi-download"></i> Download QR Code
                            </a>
                        <?php else: ?>
                            <div class="alert alert-warning small">
                                <i class="bi bi-exclamation-triangle"></i> This ticket was scanned on <?= date('M j, Y g:i A', strtotime($ticket['scanned_at'] ?? $ticket['created_at'])) ?>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="<?= BASE_URL ?>/tickets" class="btn btn-outline-secondary">Back to My Tickets</a>
                <button class="btn btn-outline-primary" onclick="window.print()">Print Ticket</button>
            </div>
        </div>
    </div>
</div>