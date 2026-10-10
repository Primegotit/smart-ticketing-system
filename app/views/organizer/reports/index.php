<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Reports Dashboard</h2>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-currency-dollar display-4 text-primary"></i>
                <h5 class="card-title mt-3">Sales Reports</h5>
                <p class="card-text">View revenue and ticket sales across all your events</p>
                <a href="<?= BASE_URL ?>/organizer/reports/sales" class="btn btn-primary">View Sales Reports</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people display-4 text-success"></i>
                <h5 class="card-title mt-3">Attendance Reports</h5>
                <p class="card-text">Track attendance rates and scan statistics</p>
                <a href="<?= BASE_URL ?>/organizer/reports/attendance" class="btn btn-success">View Attendance Reports</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-download display-4 text-info"></i>
                <h5 class="card-title mt-3">Export Data</h5>
                <p class="card-text">Download your event data in CSV format</p>
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                        Export Reports
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/organizer/reports/export/sales">Sales Data</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportAttendanceModal">Attendance Data</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Quick Event Overview</h5>
    </div>
    <div class="card-body">
        <?php if (empty($events)): ?>
            <div class="alert alert-info">
                You don't have any events yet.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Tickets Sold</th>
                            <th>Revenue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): 
                            $revenue = $event['price'] * $event['tickets_sold'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($event['title']) ?></td>
                                <td><?= date('M j, Y', strtotime($event['date_time'])) ?></td>
                                <td><?= $event['tickets_sold'] ?> / <?= $event['total_tickets'] ?></td>
                                <td>$<?= number_format($revenue, 2) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/organizer/reports/event/<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        View Report
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

<!-- Export Attendance Modal -->
<div class="modal fade" id="exportAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Attendance Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="<?= BASE_URL ?>/organizer/reports/export/attendance" method="GET">
                    <div class="mb-3">
                        <label for="eventSelect" class="form-label">Select Event</label>
                        <select class="form-select" id="eventSelect" name="eventId" required>
                            <option value="">Choose an event...</option>
                            <?php foreach ($events as $event): ?>
                                <option value="<?= $event['id'] ?>"><?= htmlspecialchars($event['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Export CSV</button>
                </form>
            </div>
        </div>
    </div>
</div>