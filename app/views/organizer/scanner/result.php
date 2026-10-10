<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Scan Result</h2>
    <a href="<?= BASE_URL ?>/organizer/scanner/scan" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Scanner
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Scan Result</h5>
            </div>
            <div class="card-body">
                <?php if ($scan_result['success']): ?>
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-success">Valid Ticket</h3>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Ticket Details</h6>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Event:</th>
                                    <td><?= htmlspecialchars($scan_result['ticket']['event_title']) ?></td>
                                </tr>
                                <tr>
                                    <th>Attendee:</th>
                                    <td><?= htmlspecialchars($scan_result['ticket']['first_name'] . ' ' . $scan_result['ticket']['last_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($scan_result['ticket']['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Ticket ID:</th>
                                    <td>#<?= $scan_result['ticket']['id'] ?></td>
                                </tr>
                                <tr>
                                    <th>Scanned At:</th>
                                    <td><?= date('M j, Y g:i A') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> This ticket has been successfully validated and marked as used.
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-x-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-danger">Invalid Ticket</h3>
                    </div>
                    
                    <?php if (isset($scan_result['ticket'])): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Ticket Details</h6>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Event:</th>
                                        <td><?= htmlspecialchars($scan_result['ticket']['event_title']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Attendee:</th>
                                        <td><?= htmlspecialchars($scan_result['ticket']['first_name'] . ' ' . $scan_result['ticket']['last_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?= htmlspecialchars($scan_result['ticket']['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ticket ID:</th>
                                        <td>#<?= $scan_result['ticket']['id'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge bg-<?= $scan_result['scan_type'] === 'duplicate' ? 'warning' : 'danger' ?>">
                                                <?= $scan_result['scan_type'] === 'duplicate' ? 'Already Used' : 'Invalid' ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?= $scan_result['message'] ?>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>/organizer/scanner/scan" class="btn btn-primary">
                        <i class="bi bi-qr-code-scan"></i> Scan Another Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>