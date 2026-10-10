<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Event Details</h2>
    <a href="<?= BASE_URL ?>/admin/events" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Events
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <?php if ($event['event_image']): ?>
                <img src="<?= BASE_URL . '/' . $event['event_image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>" style="height: 400px; object-fit: cover;">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                    <i class="bi bi-calendar-event" style="font-size: 5rem;"></i>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="card-title"><?= htmlspecialchars($event['title']) ?></h2>
                    <span class="badge bg-<?= $event['status'] === 'pending' ? 'warning' : ($event['status'] === 'approved' ? 'success' : 'danger') ?>">
                        <?= ucfirst($event['status']) ?>
                    </span>
                </div>
                
                <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Event Details</h5>
                        <p><i class="bi bi-geo-alt"></i> <strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></p>
                        <p><i class="bi bi-calendar"></i> <strong>Date:</strong> <?= date('F j, Y', strtotime($event['date_time'])) ?></p>
                        <p><i class="bi bi-clock"></i> <strong>Time:</strong> <?= date('g:i A', strtotime($event['date_time'])) ?></p>
                        <p><i class="bi bi-tag"></i> <strong>Category:</strong> <?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Ticket Information</h5>
                        <p><i class="bi bi-ticket-perforated"></i> <strong>Price:</strong> $<?= number_format($event['price'], 2) ?></p>
                        <p><i class="bi bi-people"></i> <strong>Available Tickets:</strong> <?= $event['total_tickets'] - $event['tickets_sold'] ?> of <?= $event['total_tickets'] ?></p>
                        <p><i class="bi bi-cash-coin"></i> <strong>Revenue:</strong> $<?= number_format($event['tickets_sold'] * $event['price'], 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Organizer Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($event['organizer_email']) ?></p>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Admin Actions</h5>
            </div>
            <div class="card-body">
                <?php if ($event['status'] === 'pending'): ?>
                    <a href="<?= BASE_URL ?>/admin/events/approve/<?= $event['id'] ?>" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-check-circle"></i> Approve Event
                    </a>
                    <a href="<?= BASE_URL ?>/admin/events/reject/<?= $event['id'] ?>" class="btn btn-danger w-100">
                        <i class="bi bi-x-circle"></i> Reject Event
                    </a>
                <?php elseif ($event['status'] === 'approved'): ?>
                    <a href="<?= BASE_URL ?>/admin/events/disable/<?= $event['id'] ?>" class="btn btn-warning w-100">
                        <i class="bi bi-pause-circle"></i> Disable Event
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/admin/events/approve/<?= $event['id'] ?>" class="btn btn-success w-100">
                        <i class="bi bi-check-circle"></i> Approve Event
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Event Statistics</h5>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($event['created_at'])) ?></p>
                <p><strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($event['updated_at'] ?? $event['created_at'])) ?></p>
                <p><strong>Tickets Sold:</strong> <?= $event['tickets_sold'] ?></p>
                <p><strong>Revenue Generated:</strong> $<?= number_format($event['tickets_sold'] * $event['price'], 2) ?></p>
            </div>
        </div>
    </div>
</div>