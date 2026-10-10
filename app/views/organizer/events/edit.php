<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Event</h2>
    <a href="<?= BASE_URL ?>/organizer/events" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Events
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/organizer/events/update/<?= $event['id'] ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title *</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="venue" class="form-label">Venue *</label>
                                <input type="text" class="form-control" id="venue" name="venue" value="<?= htmlspecialchars($event['venue']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_time" class="form-label">Date & Time *</label>
                                <input type="datetime-local" class="form-control" id="date_time" name="date_time" value="<?= date('Y-m-d\TH:i', strtotime($event['date_time'])) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= $event['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_image" class="form-label">Event Image</label>
                                <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">
                                <?php if ($event['event_image']): ?>
                                    <div class="mt-2">
                                        <small>Current image: <?= basename($event['event_image']) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Ticket Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="total_tickets" class="form-label">Total Tickets *</label>
                                <input type="number" class="form-control" id="total_tickets" name="total_tickets" value="<?= $event['total_tickets'] ?>" min="1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Price per Ticket ($) *</label>
                                <input type="number" class="form-control" id="price" name="price" value="<?= $event['price'] ?>" min="0" step="0.01" required>
                            </div>
                            
                            <div class="alert alert-info">
                                <small>
                                    <i class="bi bi-info-circle"></i> Events require admin approval before they become visible to attendees.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Event</button>
            <a href="<?= BASE_URL ?>/organizer/events" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>