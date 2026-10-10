<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Transaction History</h2>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Revenue Overview</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h6>Total Revenue</h6>
                                <h3>$<?= number_format($totalRevenue, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body">
                                <h6>Total Transactions</h6>
                                <h3><?= count($transactions) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h6>Events</h6>
                                <h3><?= count($events) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Transactions</h5>
    </div>
    <div class="card-body">
        <?php if (empty($transactions)): ?>
            <div class="alert alert-info">
                No transactions found for your events.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Event</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td>#<?= $transaction['id'] ?></td>
                                <td><?= htmlspecialchars($transaction['event_title']) ?></td>
                                <td>
                                    <?= htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($transaction['email']) ?></small>
                                </td>
                                <td>$<?= number_format($transaction['amount'], 2) ?></td>
                                <td><?= date('M j, Y g:i A', strtotime($transaction['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($transaction['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>