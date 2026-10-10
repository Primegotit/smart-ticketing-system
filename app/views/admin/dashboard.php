<h2 class="mb-4">Admin Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text display-6"><?= $stats['total_users'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Events</h5>
                <p class="card-text display-6"><?= $stats['total_events'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Total Tickets</h5>
                <p class="card-text display-6"><?= $stats['total_tickets'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Revenue</h5>
                <p class="card-text display-6">$<?= number_format($stats['total_revenue'], 2) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Users by Role</h5>
            </div>
            <div class="card-body">
                <canvas id="userRoleChart" width="400" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">New user registered</h6>
                            <small>2 hours ago</small>
                        </div>
                        <p class="mb-1">John Doe signed up as an attendee</p>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Event created</h6>
                            <small>5 hours ago</small>
                        </div>
                        <p class="mb-1">Summer Music Festival was created</p>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Ticket purchased</h6>
                            <small>1 day ago</small>
                        </div>
                        <p class="mb-1">Alice purchased a ticket for Tech Summit</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center mb-3">
                <a href="#" class="btn btn-outline-primary p-3 w-100">
                    <i class="bi bi-people d-block mb-2" style="font-size: 2rem;"></i>
                    Manage Users
                </a>
            </div>
            <div class="col-md-3 text-center mb-3">
                <a href="#" class="btn btn-outline-success p-3 w-100">
                    <i class="bi bi-calendar-event d-block mb-2" style="font-size: 2rem;"></i>
                    Manage Events
                </a>
            </div>
            <div class="col-md-3 text-center mb-3">
                <a href="#" class="btn btn-outline-info p-3 w-100">
                    <i class="bi bi-bar-chart d-block mb-2" style="font-size: 2rem;"></i>
                    View Reports
                </a>
            </div>
            <div class="col-md-3 text-center mb-3">
                <a href="#" class="btn btn-outline-warning p-3 w-100">
                    <i class="bi bi-gear d-block mb-2" style="font-size: 2rem;"></i>
                    System Settings
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('userRoleChart').getContext('2d');
        const userRoleChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Admins', 'Organizers', 'Attendees'],
                datasets: [{
                    data: [
                        <?= $stats['users_by_role']['admin'] ?>,
                        <?= $stats['users_by_role']['organizer'] ?>,
                        <?= $stats['users_by_role']['attendee'] ?>
                    ],
                    backgroundColor: [
                        '#ff6384',
                        '#36a2eb',
                        '#ffcd56'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>