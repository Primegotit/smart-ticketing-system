<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Event Report: <?= htmlspecialchars($event['title']) ?></h2>
    <div>
        <a href="<?= BASE_URL ?>/organizer/reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Reports
        </a>
        <a href="<?= BASE_URL ?>/organizer/reports/export/attendance?eventId=<?= $event['id'] ?>" class="btn btn-primary">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white text-center">
            <div class="card-body">
                <h5>Tickets Sold</h5>
                <h3><?= count($tickets) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <h5>Revenue</h5>
                <h3>$<?= number_format($revenue, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white text-center">
            <div class="card-body">
                <h5>Attendance</h5>
                <h3><?= $scannedCount ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white text-center">
            <div class="card-body">
                <h5>Attendance Rate</h5>
                <h3><?= $attendanceRate ?>%</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Event Details</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Title:</th>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                    </tr>
                    <tr>
                        <th>Date & Time:</th>
                        <td><?= date('M j, Y g:i A', strtotime($event['date_time'])) ?></td>
                    </tr>
                    <tr>
                        <th>Venue:</th>
                        <td><?= htmlspecialchars($event['venue']) ?></td>
                    </tr>
                    <tr>
                        <th>Price:</th>
                        <td>$<?= number_format($event['price'], 2) ?></td>
                    </tr>
                    <tr>
                        <th>Total Capacity:</th>
                        <td><?= $event['total_tickets'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Financial Summary</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Tickets Sold:</th>
                        <td><?= count($tickets) ?></td>
                    </tr>
                    <tr>
                        <th>Ticket Price:</th>
                        <td>$<?= number_format($event['price'], 2) ?></td>
                    </tr>
                    <tr>
                        <th>Total Revenue:</th>
                        <td>$<?= number_format($revenue, 2) ?></td>
                    </tr>
                    <tr>
                        <th>Average per Ticket:</th>
                        <td>$<?= count($tickets) > 0 ? number_format($revenue / count($tickets), 2) : '0.00' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Attendance Breakdown</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <canvas id="attendanceChart" width="400" height="200"></canvas>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>Total Tickets:</th>
                        <td><?= count($tickets) ?></td>
                    </tr>
                    <tr>
                        <th>Scanned Tickets:</th>
                        <td><?= $scannedCount ?></td>
                    </tr>
                    <tr>
                        <th>Not Scanned:</th>
                        <td><?= count($tickets) - $scannedCount ?></td>
                    </tr>
                    <tr>
                        <th>Attendance Rate:</th>
                        <td>
                            <span class="badge bg-<?= $attendanceRate >= 70 ? 'success' : ($attendanceRate >= 50 ? 'warning' : 'danger') ?>">
                                <?= $attendanceRate ?>%
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Scanned', 'Not Scanned'],
                datasets: [{
                    data: [<?= $scannedCount ?>, <?= count($tickets) - $scannedCount ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>