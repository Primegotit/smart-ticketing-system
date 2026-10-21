<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Upcoming Events</h2>
    <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'organizer' || $_SESSION['user']['role'] === 'admin')): ?>
        <a href="<?= BASE_URL ?>/events/create" class="btn btn-primary">Create Event</a>
    <?php endif; ?>
</div>

<div data-list-cards data-search-placeholder="Search by title, venue, category, description..."
     data-sorts='[{"key":"date","label":"Event date","type":"date"},{"key":"title","label":"Title","type":"text"},{"key":"price","label":"Price","type":"number"},{"key":"venue","label":"Venue","type":"text"},{"key":"category","label":"Category","type":"text"}]'
     data-filters='[{"key":"category","label":"Category"},{"key":"venue","label":"Venue"},{"key":"date","label":"Event date","range":true}]'>
<div class="row">
    <?php if (empty($upcomingEvents)): ?>
        <div class="col-12">
            <div class="alert alert-info">No upcoming events found.</div>
        </div>
    <?php else: ?>
        <?php foreach ($upcomingEvents as $event): ?>
            <div class="col-md-6 col-lg-4 mb-4" data-item
                 data-title="<?= htmlspecialchars($event['title']) ?>"
                 data-venue="<?= htmlspecialchars($event['venue']) ?>"
                 data-date="<?= strtotime($event['date_time']) ?>"
                 data-price="<?= (float)$event['price'] ?>"
                 data-category="<?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?>">
                <div class="card h-100">
                    <?php if ($event['event_image']): ?>
                        <img src="<?= BASE_URL . '/' . $event['event_image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-calendar-event" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
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
                        <a href="<?= BASE_URL ?>/events/<?= $event['id'] ?>" class="btn btn-outline-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($pastEvents)): ?>
<hr class="my-5">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Past Events</h2>
</div>

<div class="row">
    <?php foreach ($pastEvents as $event): ?>
        <div class="col-md-6 col-lg-4 mb-4" data-item
                 data-title="<?= htmlspecialchars($event['title']) ?>"
                 data-venue="<?= htmlspecialchars($event['venue']) ?>"
                 data-date="<?= strtotime($event['date_time']) ?>"
                 data-price="<?= (float)$event['price'] ?>"
                 data-category="<?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?>">
            <div class="card h-100">
                <?php if ($event['event_image']): ?>
                    <img src="<?= BASE_URL . '/' . $event['event_image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>" style="height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-calendar-event" style="font-size: 3rem;"></i>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
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
                    <a href="<?= BASE_URL ?>/events/<?= $event['id'] ?>" class="btn btn-outline-secondary w-100">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div>
