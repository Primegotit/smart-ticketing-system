<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <?php if ($event['event_image']): ?>
                <img src="<?= BASE_URL . '/' . $event['event_image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>" style="height: 400px; object-fit: cover;">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                    <i class="bi bi-calendar-event" style="font-size: 5rem;"></i>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($event['title']) ?></h2>
                <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                
                <?php
                $currentTime = time();
                $eventTime = strtotime($event['date_time']);
                $isPastEvent = $eventTime < $currentTime;
                ?>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Event Details</h5>
                        <p><i class="bi bi-geo-alt"></i> <strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></p>
                        <p><i class="bi bi-calendar"></i> <strong>Date:</strong> <?= date('F j, Y', strtotime($event['date_time'])) ?></p>
                        <p><i class="bi bi-clock"></i> <strong>Time:</strong> <?= date('g:i A', strtotime($event['date_time'])) ?></p>
                        <p><i class="bi bi-tag"></i> <strong>Category:</strong> <?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?></p>
                        <?php if ($isPastEvent): ?>
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle"></i> This event has already passed.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5>Ticket Information</h5>
                        <p><i class="bi bi-ticket-perforated"></i> <strong>Price:</strong> $<?= number_format($event['price'], 2) ?></p>
                        <p><i class="bi bi-people"></i> <strong>Available Tickets:</strong> <?= $event['total_tickets'] - $event['tickets_sold'] ?> of <?= $event['total_tickets'] ?></p>
                        
                        <?php if (isset($_SESSION['user'])): ?>
                            <?php if (($event['total_tickets'] - $event['tickets_sold']) > 0 && !$isPastEvent): ?>
                                <a href="<?= BASE_URL ?>/tickets/purchase/<?= $event['id'] ?>" class="btn btn-success btn-lg mt-3">Purchase Ticket</a>
                            <?php else: ?>
                                <?php if ($isPastEvent): ?>
                                    <button class="btn btn-secondary btn-lg mt-3" disabled>Event Ended</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-lg mt-3" disabled>Sold Out</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/login" class="btn btn-primary btn-lg mt-3">Login to Purchase</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Organizer Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Organizer:</strong> <?= htmlspecialchars($event['first_name'] . ' ' . $event['last_name']) ?></p>
                <p><strong>Contact:</strong> <?= htmlspecialchars($event['organizer_email']) ?></p>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Share This Event</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-primary"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-info"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="btn btn-outline-danger"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-outline-success"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>