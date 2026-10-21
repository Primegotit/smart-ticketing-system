<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Management</h2>
    <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create User
    </a>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Users</h5>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="alert alert-info">
                No users found in the system.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" data-list-tools data-search-placeholder="Search by name, username, email, role, status...">
                    <thead>
                        <tr>
                            <th data-type="number">ID</th>
                            <th data-type="text">Name</th>
                            <th data-type="text">Email</th>
                            <th data-type="text" data-filter>Role</th>
                            <th data-type="text" data-filter>Status</th>
                            <th data-type="date" data-filter="range">Joined</th>
                            <th data-type="none">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td data-value="<?= (int)$user['id'] ?>">#<?= $user['id'] ?></td>
                                <td data-value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>">
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                    <?php if (!empty($user['username'])): ?><br><small class="text-muted">@<?= htmlspecialchars($user['username']) ?></small><?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td data-value="<?= htmlspecialchars($user['role']) ?>">
                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'organizer' ? 'primary' : 'secondary') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td data-value="<?= htmlspecialchars($user['status']) ?>">
                                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td data-value="<?= strtotime($user['created_at']) ?>"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/users/view/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="<?= BASE_URL ?>/admin/users/delete/<?= $user['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>