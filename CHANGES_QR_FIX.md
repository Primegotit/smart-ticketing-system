# QR scanning / ticket hash fix - change log

## Root causes found
1. The QR shown to attendees was regenerated with a NEW random value every time a page loaded, and its
   payload was a JSON blob. The scanner looked up `qr_code_hash = <that JSON>`, so no scan could ever match.
2. QR images came from an external web service (api.qrserver.com): needs internet and leaked ticket data.
3. Scanner pages loaded their JS from CDNs, the camera needs HTTPS (no explanation was shown), and ticket
   data was injected into the page as unescaped HTML.
4. There was no PDF ticket and the attendee never saw the QR hash.

## What changed
* QR code content = the ticket's stored `qr_code_hash` only (short, easy to scan). Generated locally.
* Ticket is created with its final hash `TICKET_<id>_<random>` in one transaction (old code could leave
  hashes like `TICKET__abc` with a missing id).
* New: `GET /tickets/pdf/{id}` - PDF ticket with QR code AND the printed hash. Button on the ticket page.
* Ticket page + My Tickets now show the hash (also appears when printing). Purchase redirect shows success.
* Scanner (both /organizer/scanner/scan and /organizer/scans/scan):
  camera scan (native BarcodeDetector or bundled jsQR), photo upload, and manual hash entry - all use one
  server routine (`QRCode::processScan`). Manual entry tolerates spaces/case/URL/old JSON payloads.
* Race-safe check-in (a ticket can only be marked used once); duplicate attempts are logged as 'duplicate'.
* Organizers can only check in tickets for their own events (admins any).

## Minor bugs fixed
* Router: `{qrCodeHash}` / `{ticketId}` style routes never matched (only lowercase names were supported).
* `ScannerController::download` ignored the `?ticketId=` form field; `history` rendered a view that did not exist.
* `QRCode::recordScan` wrote to columns that do not exist in `ticket_scans`.
* Ticket page showed the wrong "scanned on" time (read a non-existent `updated_at` column).
* `show()` used a strict `!==` id comparison that can fail depending on PDO int/string types.
* Failed ticket creation now marks the transaction `failed` instead of leaving a completed payment.

## New files
app/helpers/QrImage.php, app/helpers/TicketPdf.php, app/lib/qrcode.php (MIT, kazuhikoarase/qrcode-generator),
public/assets/js/ticket-scanner.js, public/assets/js/jsQR.js (Apache-2.0, cozmo/jsQR v1.4.0)

No database changes are needed. Existing tickets keep working: their stored hash is now what the QR encodes.
Requires: PHP with zlib (default in XAMPP). No Composer packages, no internet needed for QR/PDF.
