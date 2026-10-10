<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Events</h2>
    <a href="<?= BASE_URL ?>/organizer/events/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Event
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="row">
    <?php if (empty($events)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                You haven't created any events yet. <a href="<?= BASE_URL ?>/organizer/events/create">Create your first event</a>.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <?php if ($event['event_image']): ?>
                        <img src="<?= BASE_URL . '/' . $event['event_image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-calendar-event" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                            <span class="badge bg-<?= $event['status'] === 'approved' ? 'success' : ($event['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($event['status']) ?>
                            </span>
                        </div>
                        <p class="card-text text-muted">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($event['venue']) ?>
                        </p>
                        <p class="card-text text-muted">
                            <i class="bi bi-calendar"></i> <?= date('M j, Y', strtotime($event['date_time'])) ?>
                        </p>
                        <p class="card-text text-muted">
                            <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($event['date_time'])) ?>
                        </p>
                        <p class="card-text">
                            <?= strlen($event['description']) > 100 ? substr($event['description'], 0, 100) . '...' : $event['description'] ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary"><?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?></span>
                            <span class="text-success fw-bold">$<?= number_format($event['price'], 2) ?></span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>/organizer/events/view/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="<?= BASE_URL ?>/organizer/events/edit/<?= $event['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="<?= BASE_URL ?>/organizer/events/delete/<?= $event['id'] ?>" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this event?');">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                Tickets Sold: <?= $event['tickets_sold_count'] ?> / <?= $event['total_tickets'] ?> | 
                                Revenue: $<?= number_format($event['total_revenue'] ?? 0, 2) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>