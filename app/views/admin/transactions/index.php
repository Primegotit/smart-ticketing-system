<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Transaction Monitoring</h2>
    <div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/transactions/export">All Transactions</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/transactions/export/completed">Completed</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/transactions/export/failed">Failed</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/transactions/export/pending">Pending</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/transactions/export/suspicious">Suspicious</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white text-center">
            <div class="card-body">
                <h5>Total Revenue</h5>
                <h3>$<?= number_format($totalRevenue, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <h5>Completed Transactions</h5>
                <h3><?= $completedTransactions ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white text-center">
            <div class="card-body">
                <h5>Failed Transactions</h5>
                <h3><?= $failedTransactions ?></h3>
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
                No transactions found in the system.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td>#<?= $transaction['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($transaction['user_name']) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($transaction['user_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($transaction['event_title'] ?? 'N/A') ?></td>
                                <td>$<?= number_format($transaction['amount'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($transaction['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($transaction['created_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/transactions/view/<?= $transaction['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
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