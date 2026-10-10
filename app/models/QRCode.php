<?php
namespace app\models;

use app\config\Database;
use PDO;

class QRCode {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Returns the QR information for a ticket.
     *
     * IMPORTANT: the QR code content is exactly the ticket's stored qr_code_hash
     * (the same value that is printed on the ticket / PDF and accepted by manual
     * entry). It is never regenerated, so what the attendee shows is always what
     * the scanner looks up in the database.
     *
     * @param int         $ticketId
     * @param array       $ticketData  kept for backwards compatibility (unused)
     * @param string|null $storedHash  pass the hash if you already have it, otherwise it is read from the DB
     */
    public function generateForTicket($ticketId, $ticketData = [], $storedHash = null) {
        if ($storedHash === null || $storedHash === '') {
            $stmt = $this->db->prepare("SELECT qr_code_hash FROM tickets WHERE id = :id LIMIT 1");
            $stmt->bindValue(':id', $ticketId, PDO::PARAM_INT);
            $stmt->execute();
            $storedHash = (string)$stmt->fetchColumn();
        }

        return [
            'value'   => $storedHash,
            'url'     => (defined('BASE_URL') ? BASE_URL : '') . '/tickets/qr/' . (int)$ticketId,
            'content' => $storedHash
        ];
    }

    /**
     * Builds a new unique ticket hash, e.g. TICKET_12_9f86d081884c7d65
     */
    public static function makeHash($ticketId) {
        return 'TICKET_' . (int)$ticketId . '_' . bin2hex(random_bytes(8));
    }

    /**
     * Turns whatever the scanner / person typed into the plain ticket hash.
     * Accepts: the bare hash, hash with stray spaces/newlines, a URL ending in the
     * hash (e.g. /tickets/validate/<hash>) and the older JSON style payload.
     */
    public static function normalizeScanned($raw) {
        $raw = (string)$raw;
        // replace BOM / zero-width / non-breaking spaces with normal spaces
        $raw = preg_replace('/\x{FEFF}|\x{200B}|\x{00A0}/u', ' ', $raw) ?? $raw;
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        // Older JSON payloads: {"ticket_id":..,"qr_code_value":"TICKET_.."}
        if ($raw[0] === '{') {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                foreach (['qr_code_hash', 'qr_code_value', 'hash', 'code'] as $key) {
                    if (!empty($json[$key]) && is_string($json[$key])) {
                        return self::normalizeScanned($json[$key]);
                    }
                }
            }
        }

        // URL style payloads
        if (preg_match('#^https?://#i', $raw)) {
            $path = parse_url($raw, PHP_URL_PATH) ?: '';
            $last = basename(rtrim($path, '/'));
            if ($last !== '') {
                return self::normalizeScanned(rawurldecode($last));
            }
        }

        // A TICKET_<id>_<hex> hash somewhere inside the text
        if (preg_match('/TICKET_\d*_[A-Fa-f0-9]+/', $raw, $m)) {
            return $m[0];
        }

        // Typed codes: remove any whitespace the person may have added
        return preg_replace('/\s+/', '', $raw);
    }

    public function findByValue($qrCodeValue) {
        $qrCodeValue = self::normalizeScanned($qrCodeValue);
        if ($qrCodeValue === '') {
            return false;
        }

        $sql = "SELECT t.*, e.title as event_title, e.venue, e.date_time, e.organizer_id,
                u.first_name, u.last_name, u.email,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                (SELECT MIN(ts.scanned_at) FROM ticket_scans ts
                  WHERE ts.ticket_id = t.id AND ts.scan_result = 'valid') as first_scanned_at
                FROM tickets t
                JOIN events e ON t.event_id = e.id
                JOIN users u ON t.user_id = u.id
                WHERE t.qr_code_hash = :qr_code_value
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':qr_code_value', $qrCodeValue);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Writes a row to ticket_scans using the real table columns.
     * $result is one of: valid, invalid, duplicate
     */
    public function recordScan($ticketId, $scannedBy, $result = 'valid') {
        if (!in_array($result, ['valid', 'invalid', 'duplicate'], true)) {
            $result = 'invalid';
        }
        $sql = "INSERT INTO ticket_scans (ticket_id, scanned_by, scan_result)
                VALUES (:ticket_id, :scanned_by, :scan_result)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ticket_id', $ticketId, PDO::PARAM_INT);
        $stmt->bindValue(':scanned_by', $scannedBy, PDO::PARAM_INT);
        $stmt->bindValue(':scan_result', $result);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error recording ticket scan: " . $e->getMessage());
            return false;
        }
    }

    public function getScanHistory($ticketId) {
        $sql = "SELECT ts.*, u.first_name, u.last_name 
                FROM ticket_scans ts
                JOIN users u ON ts.scanned_by = u.id
                WHERE ts.ticket_id = :ticket_id
                ORDER BY ts.scanned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validateQRCode($qrCodeValue, $eventId = null) {
        $ticketData = $this->findByValue($qrCodeValue);
        
        if (!$ticketData) {
            return [
                'valid' => false,
                'message' => 'Invalid QR code. Ticket not found.',
                'ticket' => null
            ];
        }
        
        // Check if ticket is for the specified event (if provided)
        if ($eventId && $ticketData['event_id'] != $eventId) {
            return [
                'valid' => false,
                'message' => 'This ticket is not valid for this event.',
                'ticket' => $ticketData
            ];
        }
        
        // Check if ticket has already been scanned
        if ($ticketData['is_scanned']) {
            return [
                'valid' => false,
                'message' => 'This ticket has already been used.',
                'ticket' => $ticketData,
                'scan_type' => 'duplicate'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Valid ticket.',
            'ticket' => $ticketData
        ];
    }

    /**
     * One shared routine for every way a ticket can be checked in
     * (camera scan, uploaded QR image, or the typed-in hash fallback).
     *
     * @param string   $rawValue  decoded QR text or typed hash
     * @param array    $user      $_SESSION['user']
     * @param int|null $eventId   optional event the scanner is restricted to
     * @return array   success, message, scan_type, ticket
     */
    public function processScan($rawValue, array $user, $eventId = null) {
        $hash = self::normalizeScanned($rawValue);

        if ($hash === '') {
            return ['success' => false, 'message' => 'No ticket code was received.', 'ticket' => null, 'scan_type' => 'invalid'];
        }

        $ticket = $this->findByValue($hash);

        if (!$ticket) {
            return ['success' => false, 'message' => 'Invalid ticket code. No ticket found for this code.', 'ticket' => null, 'scan_type' => 'invalid'];
        }

        // Organizers may only check in tickets for their own events (admins can check any)
        if (($user['role'] ?? '') !== 'admin' && (int)$ticket['organizer_id'] !== (int)$user['id']) {
            return ['success' => false, 'message' => 'This ticket is not for your event.', 'ticket' => $this->publicTicket($ticket), 'scan_type' => 'invalid'];
        }

        if (!empty($eventId) && (int)$ticket['event_id'] !== (int)$eventId) {
            return ['success' => false, 'message' => 'This ticket is not valid for the selected event.', 'ticket' => $this->publicTicket($ticket), 'scan_type' => 'invalid'];
        }

        if (!empty($ticket['is_scanned'])) {
            $this->recordScan($ticket['id'], $user['id'], 'duplicate');
            $msg = 'This ticket has already been used.';
            if (!empty($ticket['first_scanned_at'])) {
                $msg .= ' First scanned ' . date('M j, Y g:i A', strtotime($ticket['first_scanned_at'])) . '.';
            }
            return ['success' => false, 'message' => $msg, 'ticket' => $this->publicTicket($ticket), 'scan_type' => 'duplicate'];
        }

        $ticketModel = new Ticket();
        if ($ticketModel->markAsScanned($ticket['id'], $user['id'])) {
            $ticket['is_scanned'] = 1;
            return ['success' => true, 'message' => 'Ticket scanned successfully!', 'ticket' => $this->publicTicket($ticket), 'scan_type' => 'valid'];
        }

        // markAsScanned() returns false when another scan won the race
        $this->recordScan($ticket['id'], $user['id'], 'duplicate');
        return ['success' => false, 'message' => 'This ticket has already been used.', 'ticket' => $this->publicTicket($ticket), 'scan_type' => 'duplicate'];
    }

    private function publicTicket(array $t) {
        return [
            'id'           => $t['id'],
            'event_id'     => $t['event_id'],
            'event_title'  => $t['event_title'],
            'first_name'   => $t['first_name'],
            'last_name'    => $t['last_name'],
            'email'        => $t['email'],
            'qr_code_hash' => $t['qr_code_hash'],
            'is_scanned'   => $t['is_scanned']
        ];
    }

    public function getScansByEventIds($eventIds) {
        if (empty($eventIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
        $sql = "SELECT ts.*, t.qr_code_hash, e.title as event_title, 
                CONCAT(u.first_name, ' ', u.last_name) as attendee_name,
                CONCAT(staff.first_name, ' ', staff.last_name) as scanner_name
                FROM ticket_scans ts
                JOIN tickets t ON ts.ticket_id = t.id
                JOIN events e ON t.event_id = e.id
                JOIN users u ON t.user_id = u.id
                JOIN users staff ON ts.scanned_by = staff.id
                WHERE t.event_id IN ($placeholders)
                ORDER BY ts.scanned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($eventIds);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
