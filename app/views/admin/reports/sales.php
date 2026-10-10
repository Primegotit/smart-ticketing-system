<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Sales Reports</h2>
    <div>
        <a href="<?= BASE_URL ?>/admin/reports/export/sales" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Sales Overview</h5>
    </div>
    <div class="card-body">
        <?php if (empty($salesData)): ?>
            <div class="alert alert-info">
                No sales data available.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event</th>
                            <th>Tickets Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalRevenue = 0;
                        $totalTickets = 0;
                        ?>
                        <?php foreach ($salesData as $sale): ?>
                            <?php 
                            $totalRevenue += $sale['revenue'];
                            $totalTickets += $sale['tickets_sold'];
                            ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($sale['sale_date'])) ?></td>
                                <td><?= htmlspecialchars($sale['event_title']) ?></td>
                                <td><?= $sale['tickets_sold'] ?></td>
                                <td>$<?= number_format($sale['revenue'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-primary">
                            <td colspan="2"><strong>Total</strong></td>
                            <td><strong><?= $totalTickets ?></strong></td>
                            <td><strong>$<?= number_format($totalRevenue, 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>