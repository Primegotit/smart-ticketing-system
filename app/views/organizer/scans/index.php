<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Ticket Scanning</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Scan Ticket</h5>
            </div>
            <div class="card-body text-center">
                <p>Scan a ticket QR code to validate entry for your event.</p>
                <a href="<?= BASE_URL ?>/organizer/scans/scan" class="btn btn-primary btn-lg">
                    <i class="bi bi-qr-code-scan"></i> Start Scanning
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Scan History</h5>
            </div>
            <div class="card-body text-center">
                <p>View history of all ticket scans for your events.</p>
                <a href="<?= BASE_URL ?>/organizer/scans/history" class="btn btn-outline-primary">
                    <i class="bi bi-clock-history"></i> View History
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Quick Events Overview</h5>
    </div>
    <div class="card-body">
        <?php if (empty($events)): ?>
            <div class="alert alert-info">
                You don't have any events yet.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6><?= htmlspecialchars($event['title']) ?></h6>
                                <p class="text-muted"><?= date('M j, Y', strtotime($event['date_time'])) ?></p>
                                <a href="<?= BASE_URL ?>/organizer/scans/history/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    View Scans
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>