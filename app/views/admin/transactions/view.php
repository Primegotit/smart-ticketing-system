<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Transaction Details</h2>
    <a href="<?= BASE_URL ?>/admin/transactions" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Transactions
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Transaction Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Transaction ID:</th>
                        <td>#<?= $transaction['id'] ?></td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td>$<?= number_format($transaction['amount'], 2) ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge bg-<?= $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($transaction['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td><?= date('M j, Y g:i A', strtotime($transaction['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">User Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>User:</th>
                        <td><?= htmlspecialchars($transaction['user_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= htmlspecialchars($transaction['user_email']) ?></td>
                    </tr>
                    <tr>
                        <th>User ID:</th>
                        <td>#<?= $transaction['user_id'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($transaction['event_title']): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">Event Information</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Event:</th>
                <td><?= htmlspecialchars($transaction['event_title']) ?></td>
            </tr>
            <tr>
                <th>Event ID:</th>
                <td>#<?= $transaction['event_id'] ?></td>
            </tr>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="text-center">
    <a href="<?= BASE_URL ?>/admin/transactions" class="btn btn-primary">
        <i class="bi bi-arrow-left"></i> Back to Transactions
    </a>
</div>