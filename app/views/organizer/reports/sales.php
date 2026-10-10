<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Sales Reports</h2>
    <a href="<?= BASE_URL ?>/organizer/reports" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Reports
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>Total Revenue</h5>
                <h3>$<?= number_format($totalRevenue, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>Total Tickets Sold</h5>
                <h3><?= $totalTicketsSold ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Event Sales Summary</h5>
    </div>
    <div class="card-body">
        <?php if (empty($events)): ?>
            <div class="alert alert-info">
                No events found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Tickets Sold</th>
                            <th>Total Capacity</th>
                            <th>Price</th>
                            <th>Revenue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['title']) ?></td>
                                <td><?= date('M j, Y', strtotime($event['date_time'])) ?></td>
                                <td><?= $event['tickets_sold'] ?></td>
                                <td><?= $event['total_tickets'] ?></td>
                                <td>$<?= number_format($event['price'], 2) ?></td>
                                <td>$<?= number_format($event['revenue'], 2) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/organizer/reports/event/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>