<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Events</h2>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Event Status Overview</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body">
                                <h6>Pending</h6>
                                <h3><?= $statusCounts['pending'] ?></h3>
                                <a href="<?= BASE_URL ?>/admin/events?status=pending" class="text-white">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h6>Approved</h6>
                                <h3><?= $statusCounts['approved'] ?></h3>
                                <a href="<?= BASE_URL ?>/admin/events?status=approved" class="text-white">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white text-center">
                            <div class="card-body">
                                <h6>Rejected</h6>
                                <h3><?= $statusCounts['rejected'] ?></h3>
                                <a href="<?= BASE_URL ?>/admin/events?status=rejected" class="text-white">View All</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <?php if ($currentStatus === 'all'): ?>
                All Events
            <?php else: ?>
                <?= ucfirst($currentStatus) ?> Events
            <?php endif; ?>
        </h5>
        <div>
            <a href="<?= BASE_URL ?>/admin/events?status=all" class="btn btn-sm btn-outline-primary <?= $currentStatus === 'all' ? 'active' : '' ?>">All</a>
            <a href="<?= BASE_URL ?>/admin/events?status=pending" class="btn btn-sm btn-outline-warning <?= $currentStatus === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="<?= BASE_URL ?>/admin/events?status=approved" class="btn btn-sm btn-outline-success <?= $currentStatus === 'approved' ? 'active' : '' ?>">Approved</a>
            <a href="<?= BASE_URL ?>/admin/events?status=rejected" class="btn btn-sm btn-outline-danger <?= $currentStatus === 'rejected' ? 'active' : '' ?>">Rejected</a>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($events)): ?>
            <div class="alert alert-info">
                No events found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" data-list-tools data-search-placeholder="Search by event, organizer, venue, category, status...">
                    <thead>
                        <tr>
                            <th data-type="number">ID</th>
                            <th data-type="text" data-filter="Category">Event</th>
                            <th data-type="text" data-filter>Organizer</th>
                            <th data-type="date" data-filter="range">Date & Time</th>
                            <th data-type="text">Venue</th>
                            <th data-type="number">Tickets</th>
                            <th data-type="text" data-filter>Status</th>
                            <th data-type="none">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td data-value="<?= (int)$event['id'] ?>"><?= $event['id'] ?></td>
                                <td data-value="<?= htmlspecialchars($event['title']) ?>" data-filter-value="<?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?>">
                                    <strong><?= htmlspecialchars($event['title']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?></small>
                                </td>
                                <td data-value="<?= htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) ?>"><?= htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) ?></td>
                                <td data-value="<?= strtotime($event['date_time']) ?>">
                                    <?= date('M j, Y', strtotime($event['date_time'])) ?>
                                    <br>
                                    <small class="text-muted"><?= date('g:i A', strtotime($event['date_time'])) ?></small>
                                </td>
                                <td><?= htmlspecialchars($event['venue']) ?></td>
                                <td data-value="<?= (int)$event['tickets_sold'] ?>">
                                    <span class="badge bg-<?= ($event['total_tickets'] - $event['tickets_sold']) > 0 ? 'success' : 'secondary' ?>">
                                        <?= $event['tickets_sold'] ?> / <?= $event['total_tickets'] ?>
                                    </span>
                                </td>
                                <td data-value="<?= htmlspecialchars(ucfirst($event['status'])) ?>">
                                    <?php if ($event['status'] === 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($event['status'] === 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/events/view/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <?php if ($event['status'] === 'pending'): ?>
                                        <a href="<?= BASE_URL ?>/admin/events/approve/<?= $event['id'] ?>" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </a>
                                        <a href="<?= BASE_URL ?>/admin/events/reject/<?= $event['id'] ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </a>
                                    <?php elseif ($event['status'] === 'approved'): ?>
                                        <a href="<?= BASE_URL ?>/admin/events/disable/<?= $event['id'] ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pause-circle"></i> Disable
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/admin/events/approve/<?= $event['id'] ?>" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>