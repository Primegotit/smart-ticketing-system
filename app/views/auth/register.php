<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Account</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/register" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>" maxlength="50" pattern="[\p{L}][\p{L}\s.'\-]*" title="Letters only (spaces, hyphens and apostrophes allowed). Cannot be numbers only." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>" maxlength="50" pattern="[\p{L}][\p{L}\s.'\-]*" title="Letters only (spaces, hyphens and apostrophes allowed). Cannot be numbers only." required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($formData['username'] ?? '') ?>" minlength="3" maxlength="50" pattern="(?=.*\p{L})[\p{L}0-9._\-]+" title="3-50 characters: letters, numbers, dots, underscores, hyphens. Must contain at least one letter (not numbers only)." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="attendee">Attendee</option>
                                    <option value="organizer">Event Organizer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture (Optional)</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Register</button>
                    <p class="mt-3">
                        Already have an account? <a href="<?= BASE_URL ?>/login">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>