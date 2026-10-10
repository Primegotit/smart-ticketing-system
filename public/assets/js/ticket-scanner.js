/*
 * Ticket scanner used by BOTH organizer scanner pages.
 *
 * Three ways to check a ticket in - they all end up in the same server routine:
 *   1. Live camera scan (native BarcodeDetector when the browser has it, otherwise jsQR)
 *   2. Upload / take a photo of the QR code
 *   3. Type the ticket code (QR hash) printed on the ticket / PDF
 *
 * Configure with window.TICKET_SCANNER_CONFIG = { processUrl: '...', eventSelectId: 'event-select' }
 */
(function () {
    'use strict';

    var cfg = window.TICKET_SCANNER_CONFIG || {};
    var $ = function (id) { return document.getElementById(id); };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    var video, cameraPermission, cameraError, browserError;
    var stream = null;
    var busy = false;
    var pausedUntil = 0;
    var lastCode = '';
    var lastCodeAt = 0;
    var detector = null;
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d', { willReadFrequently: true });

    // ------------------------------------------------------------------ decoding

    function jsqrDecode(source, w, h, maxSide) {
        if (typeof jsQR !== 'function' || !w || !h) { return null; }
        var scale = Math.min(1, maxSide / Math.max(w, h));
        var cw = Math.max(1, Math.round(w * scale));
        var ch = Math.max(1, Math.round(h * scale));
        canvas.width = cw;
        canvas.height = ch;
        ctx.drawImage(source, 0, 0, cw, ch);
        var data = ctx.getImageData(0, 0, cw, ch);
        var res = jsQR(data.data, cw, ch, { inversionAttempts: 'attemptBoth' });
        return res && res.data ? res.data : null;
    }

    function decodeSource(source, w, h) {
        // Returns a Promise<string|null>
        var tryNative = detector ? detector.detect(source).then(function (codes) {
            return codes && codes.length ? codes[0].rawValue : null;
        }).catch(function () { return null; }) : Promise.resolve(null);

        return tryNative.then(function (v) {
            if (v) { return v; }
            return jsqrDecode(source, w, h, 960);
        });
    }

    // ------------------------------------------------------------------ result display

    function showResult(kind, message, ticket, extraHtml) {
        var resultDiv = $('qr-result');
        var messageDiv = $('result-message');
        var detailsDiv = $('ticket-details');
        if (!resultDiv || !messageDiv || !detailsDiv) { return; }

        resultDiv.style.display = 'block';
        messageDiv.className = 'alert alert-' + kind;
        messageDiv.textContent = message;
        detailsDiv.innerHTML = '';

        if (ticket) {
            var status = kind === 'success'
                ? '<div class="alert alert-success mt-3"><i class="bi bi-check-circle"></i> Ticket validated successfully</div>'
                : '<div class="alert alert-warning mt-3"><i class="bi bi-exclamation-triangle"></i> ' + esc(message) + '</div>';
            detailsDiv.innerHTML =
                '<div class="card mt-3"><div class="card-body text-start">' +
                '<h5>Ticket Details</h5>' +
                '<p><strong>Event:</strong> ' + esc(ticket.event_title) + '</p>' +
                '<p><strong>Attendee:</strong> ' + esc(ticket.first_name) + ' ' + esc(ticket.last_name) + '</p>' +
                '<p><strong>Email:</strong> ' + esc(ticket.email) + '</p>' +
                '<p><strong>Ticket code:</strong> <code>' + esc(ticket.qr_code_hash) + '</code></p>' +
                '<p><strong>' + (kind === 'success' ? 'Scanned At' : 'Checked At') + ':</strong> ' + esc(new Date().toLocaleString()) + '</p>' +
                status +
                '</div></div>';
        } else if (extraHtml) {
            detailsDiv.innerHTML = extraHtml;
        }

        try { resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); } catch (e) { /* ignore */ }
    }

    // ------------------------------------------------------------------ server call

    function processScan(code, source) {
        code = String(code || '').trim();
        if (!code) { return Promise.resolve(); }
        if (busy) { return Promise.resolve(); }
        busy = true;

        showResult('info', 'Processing...');

        var formData = new FormData();
        formData.append('qr_code', code);
        var sel = cfg.eventSelectId ? $(cfg.eventSelectId) : null;
        if (sel && sel.value) { formData.append('event_id', sel.value); }

        return fetch(cfg.processUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (response) { return response.text().then(function (text) { return { ok: response.ok, text: text }; }); })
        .then(function (r) {
            var data;
            try { data = JSON.parse(r.text); } catch (e) { data = null; }

            if (!data) {
                showResult('danger', r.ok
                    ? 'The server sent an unexpected response. Your session may have expired - please log in again.'
                    : 'Server error. Please try again.');
                return;
            }

            if (data.success) {
                showResult('success', data.message, data.ticket);
                if (navigator.vibrate) { navigator.vibrate(150); }
            } else {
                showResult(data.scan_type === 'duplicate' ? 'warning' : 'danger', data.message, data.ticket);
                if (navigator.vibrate) { navigator.vibrate([100, 60, 100]); }
            }
        })
        .catch(function (err) {
            console.error('Scan request failed:', err);
            showResult('danger', 'Network error. Check your connection and try again, or use manual entry.');
        })
        .then(function () {
            busy = false;
            pausedUntil = Date.now() + 2500; // short pause so one QR is not processed repeatedly
        });
    }

    // ------------------------------------------------------------------ camera

    function showCameraProblem(html, isBrowserProblem) {
        if (cameraPermission) { cameraPermission.style.display = 'none'; }
        var box = isBrowserProblem ? browserError : cameraError;
        if (box) {
            box.style.display = 'block';
            box.innerHTML = html;
        }
        var retry = box && box.querySelector('[data-retry]');
        if (retry) { retry.addEventListener('click', function (e) { e.preventDefault(); box.style.display = 'none'; startCamera(); }); }
    }

    function startCamera() {
        if (window.isSecureContext === false) {
            showCameraProblem('<i class="bi bi-shield-lock"></i> The camera can only be used on a secure page (HTTPS or <code>localhost</code>). ' +
                'This page is on an insecure address, so please use <strong>Upload QR image</strong> or type the ticket code below.', false);
            return;
        }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraProblem('<i class="bi bi-exclamation-triangle"></i> Your browser does not support camera access. ' +
                'Please use a modern browser, or use manual entry / Upload QR image.', true);
            return;
        }

        var attempts = [
            { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false },
            { video: true, audio: false }
        ];

        (function tryNext(i) {
            navigator.mediaDevices.getUserMedia(attempts[i]).then(function (s) {
                stream = s;
                if (cameraPermission) { cameraPermission.style.display = 'none'; }
                video.srcObject = s;
                video.muted = true;
                video.setAttribute('playsinline', 'true');
                video.setAttribute('autoplay', 'true');
                video.style.display = 'block';
                var p = video.play();
                if (p && p.catch) { p.catch(function () { /* autoplay blocked - tapping the video starts it */ }); }
                loop();
            }).catch(function (err) {
                if (i + 1 < attempts.length && err && (err.name === 'OverconstrainedError' || err.name === 'NotFoundError' || err.name === 'ConstraintNotSatisfiedError')) {
                    return tryNext(i + 1);
                }
                var reason = (err && err.name === 'NotAllowedError')
                    ? 'Camera permission was denied. Allow camera access in your browser settings.'
                    : (err && err.name === 'NotFoundError') ? 'No camera was found on this device.'
                    : (err && err.name === 'NotReadableError') ? 'The camera is in use by another app.'
                    : 'Could not start the camera' + (err && err.message ? ' (' + err.message + ')' : '') + '.';
                showCameraProblem('<i class="bi bi-camera-video-off"></i> ' + esc(reason) +
                    ' Please use manual entry or Upload QR image. <a href="#" data-retry>Try again</a>', false);
                console.error('Camera error:', err);
            });
        })(0);
    }

    function loop() {
        if (!stream) { return; }

        var ready = video.readyState >= 2 && video.videoWidth > 0;
        if (ready && !busy && Date.now() >= pausedUntil) {
            decodeSource(video, video.videoWidth, video.videoHeight).then(function (code) {
                if (code && !busy) {
                    var now = Date.now();
                    if (code === lastCode && now - lastCodeAt < 4000) { return; }
                    lastCode = code;
                    lastCodeAt = now;

                    video.style.boxShadow = '0 0 20px green';
                    setTimeout(function () { video.style.boxShadow = ''; }, 500);
                    processScan(code, 'camera');
                }
            }).catch(function () { /* keep scanning */ }).then(function () {
                setTimeout(loop, 120);
            });
        } else {
            setTimeout(loop, 200);
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
    }

    // ------------------------------------------------------------------ image upload

    function decodeImageFile(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                var w = img.naturalWidth, h = img.naturalHeight;
                // Try several sizes - very large photos can confuse the decoder
                var sizes = [1600, 1100, 800, 500];
                var chain = Promise.resolve(null);
                sizes.forEach(function (max) {
                    chain = chain.then(function (found) {
                        if (found) { return found; }
                        return Promise.resolve(jsqrDecode(img, w, h, max));
                    });
                });
                chain = chain.then(function (found) {
                    if (found || !detector) { return found; }
                    return detector.detect(img).then(function (codes) {
                        return codes && codes.length ? codes[0].rawValue : null;
                    }).catch(function () { return null; });
                });
                chain.then(function (v) { URL.revokeObjectURL(url); resolve(v); }, function (e) { URL.revokeObjectURL(url); reject(e); });
            };
            img.onerror = function () { URL.revokeObjectURL(url); reject(new Error('Could not open that image.')); };
            img.src = url;
        });
    }

    function setupImageUpload() {
        var input = $('qr-image-input');
        if (!input) { return; }
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) { return; }
            showResult('info', 'Reading QR code from image...');
            decodeImageFile(file).then(function (code) {
                input.value = '';
                if (!code) {
                    showResult('warning', 'No QR code could be read from that image. Try a clearer, closer photo, or type the ticket code below.');
                    return;
                }
                processScan(code, 'image');
            }).catch(function (err) {
                input.value = '';
                showResult('danger', err && err.message ? err.message : 'Could not read that image.');
            });
        });
    }

    // ------------------------------------------------------------------ manual entry (fallback)

    function setupManualForm() {
        var form = $('manual-scan-form');
        var input = $('manual-qr-code');
        if (!form || !input) { return; }
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var value = input.value.trim();
            if (!value) {
                showResult('warning', 'Please enter the ticket code (QR code hash).');
                return;
            }
            processScan(value, 'manual').then(function () { input.select(); });
        });
    }

    // ------------------------------------------------------------------ init

    document.addEventListener('DOMContentLoaded', function () {
        video = $('qr-video');
        cameraPermission = $('camera-permission');
        cameraError = $('camera-error');
        browserError = $('browser-error');

        setupManualForm();      // always works, even if everything below fails
        setupImageUpload();

        if (!video) { return; }

        if (typeof jsQR !== 'function') {
            console.warn('jsQR failed to load - relying on BarcodeDetector / manual entry only.');
        }
        if ('BarcodeDetector' in window) {
            try { detector = new window.BarcodeDetector({ formats: ['qr_code'] }); } catch (e) { detector = null; }
        }

        startCamera();
    });

    window.addEventListener('pagehide', stopCamera);
})();
