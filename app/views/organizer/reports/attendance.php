<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Attendance Reports</h2>
    <a href="<?= BASE_URL ?>/organizer/reports" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Reports
    </a>
</div>

<?php if ($selectedEventId): ?>
    <div class="alert alert-info">
        Showing attendance for: <strong><?= htmlspecialchars($attendanceData['event']['title']) ?></strong>
        <a href="<?= BASE_URL ?>/organizer/reports/attendance" class="float-end">View All Events</a>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white text-center">
                <div class="card-body">
                    <h5>Total Tickets</h5>
                    <h3><?= $attendanceData['total_tickets'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white text-center">
                <div class="card-body">
                    <h5>Scanned Tickets</h5>
                    <h3><?= $attendanceData['scanned_tickets'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <h5>Attendance Rate</h5>
                    <h3><?= $attendanceData['attendance_rate'] ?>%</h3>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Filter by Event</h5>
            <form method="GET" action="<?= BASE_URL ?>/organizer/reports/attendance">
                <div class="row">
                    <div class="col-md-8">
                        <select class="form-select" name="eventId">
                            <option value="">All Events</option>
                            <?php foreach ($events as $event): ?>
                                <option value="<?= $event['id'] ?>" <?= $selectedEventId == $event['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $selectedEventId ? 'Event Attendance' : 'All Events Attendance' ?></h5>
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
                            <th>Total Tickets</th>
                            <th>Scanned Tickets</th>
                            <th>Attendance Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($selectedEventId): ?>
                            <tr>
                                <td><?= htmlspecialchars($attendanceData['event']['title']) ?></td>
                                <td><?= date('M j, Y', strtotime($attendanceData['event']['date_time'])) ?></td>
                                <td><?= $attendanceData['total_tickets'] ?></td>
                                <td><?= $attendanceData['scanned_tickets'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $attendanceData['attendance_rate'] >= 70 ? 'success' : ($attendanceData['attendance_rate'] >= 50 ? 'warning' : 'danger') ?>">
                                        <?= $attendanceData['attendance_rate'] ?>%
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/organizer/reports/event/<?= $attendanceData['event']['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        View Report
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendanceData as $eventId => $data): ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['event']['title']) ?></td>
                                    <td><?= date('M j, Y', strtotime($data['event']['date_time'])) ?></td>
                                    <td><?= $data['total_tickets'] ?></td>
                                    <td><?= $data['scanned_tickets'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $data['attendance_rate'] >= 70 ? 'success' : ($data['attendance_rate'] >= 50 ? 'warning' : 'danger') ?>">
                                            <?= $data['attendance_rate'] ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/organizer/reports/attendance/<?= $data['event']['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>