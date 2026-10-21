<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Create New User</h2>
    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Create User</h5>
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

                <form action="<?= BASE_URL ?>/admin/users/store" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= isset($formData['username']) ? htmlspecialchars($formData['username']) : '' ?>" minlength="3" maxlength="50" pattern="(?=.*\p{L})[\p{L}0-9._\-]+" title="3-50 characters: letters, numbers, dots, underscores, hyphens. Must contain at least one letter (not numbers only)." required>
                        <div class="form-text">Letters, numbers, dots, underscores or hyphens. Cannot be numbers only.</div>
                    </div>

                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= isset($formData['first_name']) ? htmlspecialchars($formData['first_name']) : '' ?>" maxlength="50" pattern="[\p{L}][\p{L}\s.'\-]*" title="Letters only. Cannot be numbers only." required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= isset($formData['last_name']) ? htmlspecialchars($formData['last_name']) : '' ?>" maxlength="50" pattern="[\p{L}][\p{L}\s.'\-]*" title="Letters only. Cannot be numbers only." required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= isset($formData['email']) ? htmlspecialchars($formData['email']) : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="attendee" <?= (isset($formData['role']) && $formData['role'] === 'attendee') ? 'selected' : '' ?>>Attendee</option>
                            <option value="organizer" <?= (isset($formData['role']) && $formData['role'] === 'organizer') ? 'selected' : '' ?>>Organizer</option>
                            <option value="admin" <?= (isset($formData['role']) && $formData['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Create User</button>
                </form>
            </div>
        </div>
    </div>
</div>