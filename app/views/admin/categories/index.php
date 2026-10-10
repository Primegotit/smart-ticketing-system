<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Categories</h2>
    <a href="<?= BASE_URL ?>/admin/categories/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Category
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <div class="alert alert-info">
                No categories found. <a href="<?= BASE_URL ?>/admin/categories/create">Create your first category</a>.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= htmlspecialchars($category['description'] ?? 'N/A') ?></td>
                                <td><?= date('M j, Y', strtotime($category['created_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/categories/edit/<?= $category['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="<?= BASE_URL ?>/admin/categories/delete/<?= $category['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
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