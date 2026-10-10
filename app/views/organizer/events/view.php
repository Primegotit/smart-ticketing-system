<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Event Details</h2>
    <a href="<?= BASE_URL ?>/organizer/events" class="btn btn-outline-secondary">
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
                    <span class="badge bg-<?= $event['status'] === 'approved' ? 'success' : ($event['status'] === 'pending' ? 'warning' : 'danger') ?>">
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
                        <p><i class="bi bi-cash-coin"></i> <strong>Revenue:</strong> $<?= number_format($revenue['total_revenue'] ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Event Statistics</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="display-4 fw-bold"><?= $revenue['tickets_sold'] ?? 0 ?></div>
                    <p class="text-muted">Tickets Sold</p>
                </div>
                
                <div class="text-center mb-3">
                    <div class="display-6 fw-bold text-success">$<?= number_format($revenue['total_revenue'] ?? 0, 2) ?></div>
                    <p class="text-muted">Total Revenue</p>
                </div>
                
                <div class="text-center">
                    <div class="h5 fw-bold">$<?= number_format($revenue['average_ticket_price'] ?? $event['price'], 2) ?></div>
                    <p class="text-muted">Average Ticket Price</p>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Event Actions</h5>
            </div>
            <div class="card-body">
                <a href="<?= BASE_URL ?>/organizer/events/edit/<?= $event['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-pencil"></i> Edit Event
                </a>
                <form action="<?= BASE_URL ?>/organizer/events/delete/<?= $event['id'] ?>" method="POST" class="d-inline w-100">
                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this event?');">
                        <i class="bi bi-trash"></i> Delete Event
                    </button>
                </form>
            </div>
        </div>
        
        <?php if (!empty($tickets)): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Recent Ticket Sales</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach (array_slice($tickets, 0, 5) as $ticket): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?></h6>
                                    <small><?= date('M j', strtotime($ticket['created_at'])) ?></small>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars($ticket['email']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($tickets) > 5): ?>
                        <div class="text-center mt-2">
                            <a href="#" class="btn btn-sm btn-outline-primary">View All Tickets</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>