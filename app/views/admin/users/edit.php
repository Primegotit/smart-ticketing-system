<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit User - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
    <a href="<?= BASE_URL ?>/admin/users/view/<?= $user['id'] ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to User
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit User Information</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/admin/users/update/<?= $user['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="attendee" <?= $user['role'] === 'attendee' ? 'selected' : '' ?>>Attendee</option>
                            <option value="organizer" <?= $user['role'] === 'organizer' ? 'selected' : '' ?>>Organizer</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>