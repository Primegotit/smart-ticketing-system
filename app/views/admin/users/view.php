<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Details - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <?php if ($user['profile_picture']): ?>
                    <div class="text-center mb-3">
                        <img src="<?= BASE_URL . '/' . $user['profile_picture'] ?>" alt="Profile Picture" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                <?php endif; ?>
                
                <p><strong>ID:</strong> #<?= $user['id'] ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Role:</strong> 
                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'organizer' ? 'primary' : 'secondary') ?>">
                        <?= ucfirst($user['role']) ?>
                    </span>
                </p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($user['status']) ?>
                    </span>
                </p>
                <p><strong>Joined:</strong> <?= date('M j, Y g:i A', strtotime($user['created_at'])) ?></p>
                
                <div class="mt-3">
                    <a href="<?= BASE_URL ?>/admin/users/edit/<?= $user['id'] ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <?php if ($user['role'] === 'organizer'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Organizer Events</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($events)): ?>
                        <p class="text-muted">This organizer hasn't created any events yet.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($events as $event): ?>
                                <a href="<?= BASE_URL ?>/admin/events/view/<?= $event['id'] ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($event['title']) ?></h6>
                                        <small><?= date('M j, Y', strtotime($event['date_time'])) ?></small>
                                    </div>
                                    <p class="mb-1"><?= htmlspecialchars($event['venue']) ?></p>
                                    <small>
                                        <span class="badge bg-<?= $event['status'] === 'approved' ? 'success' : ($event['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($event['status']) ?>
                                        </span>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Tickets</h5>
            </div>
            <div class="card-body">
                <?php if (empty($tickets)): ?>
                    <p class="text-muted">This user hasn't purchased any tickets yet.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($tickets as $ticket): ?>
                            <a href="<?= BASE_URL ?>/admin/tickets/view/<?= $ticket['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($ticket['event_title']) ?></h6>
                                    <small><?= date('M j, Y', strtotime($ticket['date_time'])) ?></small>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars($ticket['venue']) ?></p>
                                <small>
                                    <span class="badge bg-<?= $ticket['is_scanned'] ? 'success' : 'secondary' ?>">
                                        <?= $ticket['is_scanned'] ? 'Used' : 'Valid' ?>
                                    </span>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>