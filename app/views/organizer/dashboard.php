<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Organizer Dashboard</h2>
    <a href="<?= BASE_URL ?>/organizer/events/create" class="btn btn-primary">Create New Event</a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Events</h5>
                <p class="card-text display-6"><?= count($events) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Tickets Sold</h5>
                <p class="card-text display-6">
                    <?= array_reduce($events, function($carry, $event) {
                        return $carry + ($event['ticket_count'] ?? 0);
                    }, 0) ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Upcoming Events</h5>
                <p class="card-text display-6">
                    <?= count(array_filter($events, function($event) {
                        return strtotime($event['date_time']) > time();
                    })) ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Past Events</h5>
                <p class="card-text display-6">
                    <?= count(array_filter($events, function($event) {
                        return strtotime($event['date_time']) < time();
                    })) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<h3 class="mb-3">My Events</h3>

<?php if (empty($events)): ?>
    <div class="alert alert-info">
        You haven't created any events yet. <a href="<?= BASE_URL ?>/events/create">Create your first event</a>.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Tickets Sold</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($event['title']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?></small>
                                </td>
                                <td><?= date('M j, Y', strtotime($event['date_time'])) ?></td>
                                <td><?= htmlspecialchars($event['venue']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $event['ticket_count'] > 0 ? 'success' : 'secondary' ?>">
                                        <?= $event['ticket_count'] ?> / <?= $event['total_tickets'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (strtotime($event['date_time']) > time()): ?>
                                        <span class="badge bg-success">Upcoming</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/events/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="<?= BASE_URL ?>/organizer/reports/event/<?= $event['id'] ?>" class="btn btn-sm btn-outline-info">Reports</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>