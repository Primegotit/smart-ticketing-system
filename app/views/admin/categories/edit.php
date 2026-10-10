<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Category</h2>
    <a href="<?= BASE_URL ?>/admin/categories" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Categories
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/admin/categories/update/<?= $category['id'] ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name *</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="<?= BASE_URL ?>/admin/categories" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>