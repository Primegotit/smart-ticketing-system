
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Scan Ticket</h2>
    <a href="<?= BASE_URL ?>/organizer/scanner" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Scanner
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">QR Code Scanner</h5>
            </div>
            <div class="card-body">
                <div id="scanner-container" class="text-center">
                    <div id="camera-permission" class="alert alert-info">
                        <i class="bi bi-camera"></i> Please allow camera access to use the scanner.
                    </div>
                    <div id="video-container">
                        <video id="qr-video" width="100%" height="300" class="border rounded" style="display: none;"></video>
                    </div>
                    <div id="camera-error" class="alert alert-warning mt-3" style="display: none;">
                        <i class="bi bi-camera-video-off"></i> Camera access denied or not available. Please use manual entry.
                    </div>
                    <div id="browser-error" class="alert alert-danger mt-3" style="display: none;">
                        <i class="bi bi-exclamation-triangle"></i> Your browser doesn't support camera access. Please use a modern browser or manual entry.
                    </div>
                    <div id="qr-result" class="mt-3" style="display: none;">
                        <h5>Scan Result</h5>
                        <div id="result-message" class="alert alert-info"></div>
                        <div id="ticket-details"></div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Manual Entry</h5>
                    <form id="manual-scan-form" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="manual-qr-code" placeholder="Enter ticket code (QR code hash), e.g. TICKET_12_ab12cd34ef567890" autocomplete="off" autocapitalize="off" spellcheck="false" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Submit</button>
                        </div>
                    </form>
                </div>

                <div class="mt-3">
                    <label for="qr-image-input" class="form-label">Or upload / take a photo of the QR code</label>
                    <input type="file" class="form-control" id="qr-image-input" accept="image/*">
                    <div class="form-text">Use this if the live camera is not available or can't read the code.</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Event Selection</h5>
            </div>
            <div class="card-body">
                <select class="form-select" id="event-select">
                    <option value="">All Events</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>"><?= htmlspecialchars($event['title']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="mt-3">
                    <p class="small text-muted">
                        <i class="bi bi-info-circle"></i> Select an event to validate tickets specifically for that event.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Scan Instructions</h5>
            </div>
            <div class="card-body">
                <ol class="small">
                    <li>Allow camera access when prompted</li>
                    <li>Position the QR code in the viewfinder</li>
                    <li>The scanner will automatically detect the code</li>
                    <li>Or use the manual entry form below</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Scanner scripts (served locally - no CDN needed) -->
<script src="<?= BASE_URL ?>/assets/js/jsQR.js"></script>
<script>
    window.TICKET_SCANNER_CONFIG = {
        processUrl: '<?= BASE_URL ?>/organizer/scanner/process',
        eventSelectId: 'event-select'
    };
</script>
<script src="<?= BASE_URL ?>/assets/js/ticket-scanner.js"></script>
