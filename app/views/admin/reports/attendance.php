<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Attendance Reports</h2>
    <div>
        <a href="<?= BASE_URL ?>/admin/reports/export/attendance" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter by Event</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/admin/reports/attendance">
            <div class="row">
                <div class="col-md-8">
                    <select class="form-select" name="event_id" onchange="this.form.submit()">
                        <option value="">All Events</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?= $event['id'] ?>" <?= isset($selectedEvent) && $selectedEvent['id'] == $event['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($event['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <?php if (isset($selectedEvent)): ?>
                        <a href="<?= BASE_URL ?>/admin/reports/export/event/<?= $selectedEvent['id'] ?>" class="btn btn-outline-primary w-100">
                            <i class="bi bi-download"></i> Export Event Report
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Attendance Overview</h5>
    </div>
    <div class="card-body">
        <?php if (empty($attendanceData)): ?>
            <div class="alert alert-info">
                No attendance data available.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Total Tickets</th>
                            <th>Tickets Scanned</th>
                            <th>Attendance Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceData as $data): ?>
                            <tr>
                                <td><?= htmlspecialchars($data['event_title']) ?></td>
                                <td><?= $data['total_tickets'] ?></td>
                                <td><?= $data['tickets_scanned'] ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar <?= $data['attendance_rate'] > 70 ? 'bg-success' : ($data['attendance_rate'] > 40 ? 'bg-warning' : 'bg-danger') ?>" 
                                             role="progressbar" 
                                             style="width: <?= $data['attendance_rate'] ?>%;"
                                             aria-valuenow="<?= $data['attendance_rate'] ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= $data['attendance_rate'] ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>